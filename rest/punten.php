<?php
/**
 * Service punten | rest/punten.php
 *
 * Rest service voor Punten
 *
 * PHP version 7.2
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/mit-license.html  MIT License.
 * If you did not receive a copy of the MIT License and are unable to
 * obtain it through the web, please send a note to license@php.net so
 * we can mail you a copy immediately.
 *
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2020 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.1
 * @version    1.2.2
 *
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var input $input
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';
require_once '../includes/configuration.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Uren_obj.php';
require_once '../objects/Punten_obj.php';
require_once '../objects/PuntenWaardes_obj.php';
require_once '../objects/Activiteiten_obj.php';
require_once '../objects/Rollen_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (! $authenticate->authorisation_check(false)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Unauthorized',
        'code' => 401
    ));
    exit();
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

switch ($input->get_method()) {
        // Read one or all records
    case 'GET':
        if ($input->hasPathParams()) {
            if (array_keys($input->get_pathParams())[0] == 'getTotals') {
                getPuntenTotaal($input);
                break;
            } else {
                http_response_code(501);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Not implemented #A01',
                    'code' => 501
                ));
            }
        } else {
            http_response_code(501);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Not implemented #A02',
                'code' => 501
            ));
        }
        
    case 'POST':
        herberekenPunten();
        break;
        
    default:
        http_response_code(501);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not implemented #A03',
            'code' => 501
        ));
}

/**
 * Function getUsers
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 *
 * @var Punten $punten_obj
 * @var array $result
 */
function getPuntenTotaal($input)
{
    /**
     * @var array $result Resultaat van bevragingen
     */
    $result = null;
    
    global $mysqli;
    
    if ($input->get_pathParams()) {
        
        $punten_obj = new Punten($mysqli);
        try {
            $punten_obj->read(array_keys($input->get_pathParams())[1]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Missing input',
            'code' => 400
        ));
        
    }
    $punten_obj->calculateTotals(date('Y-m-d'));
    
    $result['punten']['totaal'] = $punten_obj->totaalPunten;
    $result['punten']['gebruikt'] = $punten_obj->puntenGebruikt;
    $result['punten']['beschikbaar'] = $punten_obj->puntenBeschikbaar;
    
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($result);
    
    return true;
}

/**
 * Function herberkenPunten
 *
 * @return bool Successflag
 *
 * @var PuntenWaardes $puntenWaardes_obj
 * @var Activiteiten $activteiten_obj
 * @var Rollen $rollen_obj
 * @var Uren $uren_obj
 * @var DateTime $oudste_datum
 * @var bool $magSparen
 * @var DateTime $uurdate 
 * @var Punten $punten_obj
 */
function herberekenPunten()
{
    global $mysqli;
    
        // Lees alle puntenwaardes
        $puntenWaardes_obj = new PuntenWaardes($mysqli);
        try {
            $puntenWaardes_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
    
        // Lees alle activiteiten
        $activteiten_obj = new Activiteiten($mysqli);
        try {
            $activteiten_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
        // Lees alle rollen
        $rollen_obj = new Rollen($mysqli);
        try {
            $rollen_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
    
        // Lees alle goedgekeurde uren
        $uren_obj = new Uren($mysqli);
        try {
            $uren_obj->read(null, null, null, 'goedgekeurd');
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
        // Bereken eerste geldigheids datum punten
        $oudste_datum = new DateTime("now");
        $oudste_datum->sub(new DateInterval(PUNTENGELDIGHEID));
        
        echo "Bereken punten voor uren welke geboekt zijn na " . ($oudste_datum->format('Y-m-d')) . "\n";
        
        foreach ($uren_obj->uren as $uur)
        {
            $magSparen = false;
            
            // Output info over uur record
            echo "---------------------------------------------------------\n";
            echo "Uur record " . $uur->id . " voor " . $uur->username . "\n";
            echo "datum " . $uur->datum . "\n";
            
            $uurdate = new DateTime($uur->datum);
            
            // is uur record niet ouder dan PUNTENGELDIGHEID
            if ($uurdate < $oudste_datum) {
                echo "Uur record ouder dan geldigheid punten\n";
            } else {
            
                // mogen er punten gespaard worden voor de activiteit?
                if ($activteiten_obj->magSparen($uur->activiteit_id)) {
                    echo "  " . $uur->activiteit . " mag punten sparen\n";
                    $magSparen = true;
                } else {
                    echo "  " . $uur->activiteit . " mag geen punten sparen\n";
                    $magSparen = false;
                }
                
                // mogen er punten gespaard worden voor de rol?
                
                if ($rollen_obj->magSparen($uur->rol_id)) {
                    echo "  " . $uur->rol . " mag punten sparen\n";
                } else {
                    echo "  " . $uur->rol . " mag geen punten sparen\n";
                    $magSparen = false;
                }
                
                // bestaat er een record in ura_punten?
                $punten_obj = new Punten($mysqli);
                if ($punten_obj->read(null, null, $uur->id)) {
                    echo "Punten record gevonden\n";

                    if ($magSparen) {
                        controleerPuntenRecord($uur, $punten_obj->punten[0], $puntenWaardes_obj);
                    } else {
                        deletePuntenRecord($punten_obj);
                    }
                    
                } else {
                    echo "Punten record niet gevongen.\n";
                    if ($magSparen) {
                        // voeg nieuw record toe in ura_punten
                        maakPuntenRecord($uur, $puntenWaardes_obj);
                    } else {
                        echo "Geen punten toegewezen.\n";
                    }
                }
                
            $punten_obj = null;
            }
            
        }

    return true;
}

/**
 * Function maakPuntenRecord
 *
 * @param uur $uur_obj 
 * @param PuntenWaardes $puntenWaardes_obj
 * @return void
 *
 * @var double $puntenWaarde
 * @var Punt $punt_obj
 * @var Punten $punten_obj
 */
function maakPuntenRecord(uur $uur_obj, puntenWaardes $puntenWaardes_obj) {
    
    global $mysqli;
    
    $puntenWaarde = $puntenWaardes_obj->getPuntenWaarde($uur_obj->datum);
    
    echo "Nieuw punten record aangemaakt voor " . $uur_obj->uren . " punten met als waarde € " . $puntenWaarde . " per punt.\n";
    
    $punt_obj = new Punt(0, $uur_obj->username, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->id, date('Y-m-d'), $uur_obj->uren, $puntenWaarde, null);
    
    $punten_obj = new Punten($mysqli);
    $punten_obj->create($punt_obj);
}

/**
 * Function ControleerPuntenRecord
 *
 * @param Uur $uur_obj
 * @param Punt $punt_obj
 * @param PuntenWaardes $puntenWaardes_obj
 * @return void
 *
 * @var string $puntengelijk
 * @var string $waardegelijk
 */
function controleerPuntenRecord(Uur $uur_obj, Punt $punt_obj, puntenWaardes $puntenWaardes_obj) {
    
    $puntenWaarde = $puntenWaardes_obj->getPuntenWaarde($uur_obj->datum);
    
    if ($uur_obj->uren == $punt_obj->punten) {
        $puntengelijk = "wel";
    } else {
        $puntengelijk = "niet";
    }
    
    if ($punt_obj->waardePunten == $puntenWaarde) {
        $waardegelijk = "wel";
    } else {
        $waardegelijk = "niet";
    }
    
    echo "Aantal uren (" . $uur_obj->uren . ") is " . $puntengelijk . " gelijk aan aantal punten (";
    echo $punt_obj->punten . "), waarde is " . $waardegelijk . " gelijk (€ " . $punt_obj->waardePunten . " per punt).\n";
    if ($waardegelijk == "niet") {
        echo "Waarde in record is € " . $punt_obj->waardePunten . ", Recht op € " . $puntenWaarde . ".\n";
    }
    
}

/**
 * Function DeletePuntenRecord
 *
 * @param Punten $punten_obj
 * @return void
 */
function deletePuntenRecord(Punten $punten_obj) {
    
    if ($punten_obj->punten[0]->puntenGebruikt > 0) {
        echo "Reeds punten gebruikt, kan record niet verwijderen.\n";
    } else {
        try {
            $punten_obj->delete($punten_obj->punten[0]->id);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        echo "Punten record verwijderd.\n";
    }
    
}
