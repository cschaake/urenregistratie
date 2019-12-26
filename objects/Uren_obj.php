<?php
/**
 * Class Uren | objects/Uren_obj.php
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
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.2
 */

/**
 * Required files
 */
require_once ('Uur_obj.php');
require_once ('Punten_obj.php');
require_once ('PuntenWaardes_obj.php');
require_once ('Activiteiten_obj.php');
require_once ('Rollen_obj.php');

/**
 * Class Uren - Collection van Uur objecten
 * 
 * @since Class available since Release 1.0.0
 * @version 1.2.2
 */
class Uren
{

    /**
     * Array met Uur objecten
     *
     * @var Uur[]
     * @access public
     */
    public $uren;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Method constructor - Creeer uren object
     *
     * @access public
     * @param mysqli $mysqli
     * @throws Exception
     * @return bool Success flag
     */
    public function __construct(mysqli $mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object', 500);
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Method create - Creeer uur
     *
     * @access public
     * @param Uur $uur_obj Uur object
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var array $time
     *      @option string "start" Starttijd
     *      @option string "eind" Eindtijd 
     * @var string $prep_stmt SQL Statement
     * @var SQLite3Stmt $stmt    
     */
    public function create(Uur $uur_obj)
    {
        $time = null;
        $prep_stmt = null;
        $stmt = null;
        
        // Controleer overlap in tijd
        $time = $this->_checkTime($uur_obj->username, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->rol_id);
        if ($time) {
            throw new Exception('Reeds uren geboekt tussen ' . $time->start . ' en ' . $time->eind);
        }
        
        $uur_obj->getActiviteitTijden($uur_obj);
        $uur_obj->calculateUren();

        $prep_stmt = "
            INSERT
				ura_uren
            SET
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
                groep_id = ( 
                    SELECT 
                        groep_id 
                    FROM 
                        ura_certificering 
                    WHERE rol_id = ?),
                datum = ?,
                start = ?,
                eind = ?,
                uren = ?,
                akkoord = 0,
                reden = '',
				opmerking = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('siiisssds', $uur_obj->username, $uur_obj->activiteit_id, $uur_obj->rol_id, $uur_obj->rol_id, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->uren, $uur_obj->opmerking);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $id = (int) $stmt->insert_id;
            } else {
                $stmt->close();
                throw new Exception('Fout bij updaten uur', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        $this->read($uur_obj->username, $id);

        return true;
    }

    /**
     * Method read - Lees uur of uren
     *
     * @access public
     * @param string $username optional Username
     * @param int $id optional Uur id
     * @param datetime $pijldatum optional Toon alleen uren voor certificaat dat actief is tijdens pijldatum
     * @param string $action Welke soort uren moeten opgevraagd worden (null = alles; goedgekeurd = goedgekeurd)
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt SQL Statement
     * @var SQLite3Stmt $stmt  
     */
    public function read($username = null, $id = null, $pijldatum = null, $action = null)
    {
        $prep_stmt = null;
        $stmt = null;
        
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($id)) {
            $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        }
        if (isset($pijldatum)) {
            $pijldatum = date('Y-m-d', strtotime($pijldatum));
        }
        if (isset($action)) {
            $action = filter_var($action, FILTER_SANITIZE_STRING);
        }

        $prep_stmt = $this->_prepRead($username, $id, $action);
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if ($id && $username) {
                $stmt->bind_param('si', $username, $id);
            } elseif ($username) {
                $stmt->bind_param('s', $username);
            } elseif ($id) {
                $stmt->bind_param('i', $id);
            }
            $stmt->execute();
            $stmt->store_result();

            $this->_procRead($stmt);
            
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method _prepRead - Prepare SQL statement read
     *
     * @access private
     * @param string $username optional Username
     * @param int $id optional Uur id
     * @param string $action
     * @throws Exception
     * @return string prep_stmt
     * 
     * @var string $prep_stmt SQL Statement
     */
    private function _prepRead($username = null, $id = null, $action)
    {
        $prep_stmt = null;

        $prep_stmt = "
            SELECT
                ura_uren.id,
                ura_uren.username,
                ura_uren.activiteit_id,
                ura_activiteiten.activiteit,
                ura_activiteiten.groep_id,
                ura_groepen.groep,
                ura_uren.rol_id,
                ura_rollen.rol,
                ura_uren.datum,
                ura_uren.start,
                ura_uren.eind,
                ura_uren.uren,
                ura_uren.akkoord,
                ura_uren.reden,
				ura_uren.opmerking,
				ura_uren.flag
            FROM
                ura_uren,
                ura_activiteiten,
                ura_groepen,
                ura_rollen
            WHERE ura_uren.activiteit_id = ura_activiteiten.id
            AND ura_activiteiten.groep_id = ura_groepen.id
            AND ura_uren.rol_id = ura_rollen.id ";
        if ($username) {
            $prep_stmt .= " AND ura_uren.username = ? ";
        }
        if ($id) {
            $prep_stmt .= " AND ura_uren.id = ?";
        }
        if ($action == 'goedgekeurd') {
            $prep_stmt .= " AND ura_uren.akkoord = 1 ";
        }
        $prep_stmt .= " ORDER BY ura_uren.datum DESC";
       
        return $prep_stmt;
    }
    
    /**
     * Method _procRead - Process SQL result read
     *
     * @access private
     * @param string $stmt
     * @throws Exception
     * @return bool 
     * 
     * @var Uur $uur_obj
     * @var int $id
     * @var string $username
     * @var int $activiteit_id
     * @var string $activiteit
     * @var int $groep_id
     * @var string $groep
     * @var int $rol_id
     * @var string $rol
     * @var datetime $datum
     * @var string $start
     * @var string $eind
     * @var int $uren
     * @var bool $akkoord
     * @var string $reden
     * @var string $opmerking
     * @var bool $flag
     */
    private function _procRead($stmt)
    {
        $uur_obj = null;
        $id = null;
        $username = null;
        $activiteit_id = null;
        $activiteit = null;
        $groep_id = null;
        $groep = null;
        $rol_id = null;
        $rol = null;
        $datum = null;
        $start = null;
        $eind = null;
        $uren = null;
        $akkoord = null;
        $reden = null;
        $opmerking = null;
        $flag = null;
        
        if ($stmt->num_rows >= 1) {
            $stmt->bind_result($id, $username, $activiteit_id, $activiteit, $groep_id, $groep, $rol_id, $rol, $datum, $start, $eind, $uren, $akkoord, $reden, $opmerking, $flag);
            
            while ($stmt->fetch()) {
                $uur_obj = new Uur($username, $activiteit_id, $rol_id, $datum, $start, $eind, $uren, $opmerking, $akkoord, $reden, $flag, $id);
                $uur_obj->addActiviteit($activiteit_id, $activiteit);
                $uur_obj->addRol($rol_id, $rol);
                $uur_obj->addGroep($groep_id, $groep);
                $this->uren[] = $uur_obj;
            }
        } elseif ($stmt->num_rows == 0) {
            $stmt->close();
            throw new Exception('Geen uur record gevonden #A01', 404);
        } else {
            $stmt->close();
            throw new Exception('Fout bij opvragen uren', 500);
        }
        return true;
    }
    
    /**
     * Method readGoedTeKeuren - Lees goed te keuren uren
     *
     * @access public
     * @param string $username
     *            optional Username
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     * @var Uur $uur_obj
     * @var string $firstname
     * @var string $lastname
     * @var int $activiteit_id
     * @var string $activiteit
     * @var int $rol_id
     * @var string $rol
     * @var datetime $datum
     * @var string $start
     * @var string $eind
     * @var int $uren
     * @var string $opmerking
     */
    public function readGoedTeKeuren($username = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $uur_obj = null;
        $firstname = null;
        $lastname = null;
        $activiteit_id = null;
        $activiteit = null;
        $rol_id = null;
        $rol = null;
        $datum = null;
        $start = null;
        $eind = null;
        $uren = null;
        $opmerking = null;
        
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            SELECT
                ura_uren.id,
				ura_uren.username,
				users.firstName,
				users.lastName,
				ura_uren.activiteit_id,
				ura_activiteiten.activiteit,
                ura_uren.rol_id,
				ura_rollen.rol,
				ura_uren.datum,
				ura_uren.start,
				ura_uren.eind,
				ura_uren.uren,
				ura_uren.opmerking
            FROM
				ura_uren
			JOIN
				users ON ura_uren.username = users.username
			JOIN
				ura_rollen ON ura_uren.rol_id = ura_rollen.id
			JOIN
				ura_activiteiten ON ura_uren.activiteit_id = ura_activiteiten.id
            WHERE akkoord < 1 ";

        if ($username) {
            $prep_stmt .= "
                AND ura_uren.rol_id IN (
                    SELECT
                        ura_urengoedkeuren.rol_id
                    FROM
                        ura_urengoedkeuren
                    WHERE ura_urengoedkeuren.username = ?
                )";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if ($username) {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $username, $firstname, $lastname, $activiteit_id, $activiteit, $rol_id, $rol, $datum, $start, $eind, $uren, $opmerking);

                while ($stmt->fetch()) {
                    $uur_obj = new Uur($username, $activiteit_id, $rol_id, $datum, $start, $eind, $uren, $opmerking, null, null, null, $id);
                    $uur_obj->addActiviteit($activiteit_id, $activiteit);
                    $uur_obj->addRol($rol_id, $rol);
                    $uur_obj->addName($firstname, $lastname);
                    $this->uren[] = $uur_obj;
                }
            } elseif ($stmt->num_rows == 0) {
                $stmt->close();
                throw new Exception('Geen uur record gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen uren', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method update - Update uur
     *
     * @access public
     * @param Uur $uur_obj Uur object
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function update(Uur $uur_obj)
    {
        $prep_stmt = null;
        $stmt = null;
        
        // Controleer overlap in tijd
        $time = $this->_checkTime($uur_obj->username, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->rol_id, $uur_obj->id);
        if ($time) {
            throw new Exception('Reeds uren geboekt tussen ' . $time->start . ' en ' . $time->eind);
        }
        
        $uur_obj->getActiviteitTijden($uur_obj);
        $uur_obj->calculateUren();
        
        $prep_stmt = "
            UPDATE
				ura_uren
            SET
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
                groep_id = ( 
                    SELECT 
                        groep_id 
                    FROM 
                        ura_certificering 
                    WHERE rol_id = ?),
                datum = ?,
                start = ?,
                eind = ?,
                uren = ?,
                akkoord = ?,
                reden = ?,
				opmerking = ?
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('siiisssdissi', $uur_obj->username, $uur_obj->activiteit_id, $uur_obj->rol_id, $uur_obj->rol_id, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->uren, $uur_obj->akkoord, $uur_obj->reden, $uur_obj->opmerking, $uur_obj->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Uur record niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read($uur_obj->username, $uur_obj->id);

        return true;
    }

    /**
     * Method delete - Delete uur
     *
     * @access public
     * @param int $id
     *            Uur id
     * @param string $username
     *            Username
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function delete($id, $username = null)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            DELETE FROM
				ura_uren
            WHERE
				id = ? ";

        if ($username) {
            $prep_stmt .= " AND username = ? ";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if ($username) {
                $stmt->bind_param('is', $id, $username);
            } else {
                $stmt->bind_param('i', $id);
            }
            $stmt->execute();
            $stmt->store_result();

            $result = ($stmt->affected_rows >= 1);
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $result;
    }

    /**
     * Method goedkeuren - Uur goedkeuren
     *
     * @access public
     * @param int $id
     *            Uur id
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function goedkeuren($id)
    {
        global $mysqli;
        
        $prep_stmt = null;
        $stmt = null;
        
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);

        $Uur = new Uren($this->mysqli);
        $Uur->read(null,$id,null);

        $prep_stmt = "
            UPDATE
				ura_uren
            SET
                akkoord = 1
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            
            $stmt->bind_param('i', $id);

            $stmt->execute();
            $stmt->store_result();

            $result = ($stmt->affected_rows >= 1);
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read(null, $id);
        
        if ($this->_checkMagPunten()) {
            // Geef het juiste aantal punten vrij bij goedkeuren.
            $waardePunten_obj = new PuntenWaardes($mysqli);
            $waardePunten_obj->read();
            $waardePunten = $waardePunten_obj->getPuntenWaarde(date('Y-m-d'));
    
            $punt_obj = new Punt(0, $Uur->uren[0]->username, $Uur->uren[0]->datum, $Uur->uren[0]->start, $Uur->uren[0]->eind, $Uur->uren[0]->id, date('Y-m-d'), $Uur->uren[0]->uren, $waardePunten, 0);
            $punten = new Punten($this->mysqli);
            $punten->create($punt_obj);
        }
        return $result;
    }

    /**
     * Method afkeuren - Uur afkeuren
     *
     * @access public
     * @param int $id
     *            Uur id
     * @param string $reden
     *            Reden
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt q$stmt
     */
    public function afkeuren($id, $reden)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        $reden = filter_var($reden, FILTER_SANITIZE_STRING);

        $prep_stmt = "
            UPDATE
				ura_uren
            SET
                akkoord = 2,
                reden = ?
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('si', $reden, $id);

            $stmt->execute();
            $stmt->store_result();

            $result = ($stmt->affected_rows >= 1);
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $result;
    }

    /**
     * Method _checkTime - Check time
     *
     * Check of er reeds uren zijn geboekt op het gevraagde tijdstip
     *
     * @param string $username Username
     * @param string $date Datum
     * @param string $start Start tijd
     * @param string $end End tijd
     * @param int $rol_id
     * @param int $uur_id optioneel te wijzigen uur_id
     * @return bool Succes vlag
     * 
     * @var string prep_stmt
     * @var mysqli_stmt stmt
     */
    private function _checkTime($username, $date, $start, $end, $rol_id, $uur_id = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $result = false;
        
        if ($start > $end) {
            throw new Exception('Eindtijd voor begintijd', 500);
        }

        $prep_stmt = "
            SELECT
				start, eind
			FROM
				ura_uren
            WHERE
				username = ?
			AND
				datum = ?
			AND
				eind > ?
			AND
				start < ?
            AND
                rol_id = ?";
        
        if ($uur_id) {
            $prep_stmt .= "
            AND
                id != ?";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if ($uur_id) {
                $stmt->bind_param('ssssii', $username, $date, $start, $end, $rol_id, $uur_id);
            } else {
                $stmt->bind_param('ssssi', $username, $date, $start, $end, $rol_id);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Fout bij bepalen tijds overlap', 500);
            } elseif ($stmt->affected_rows > 0) {
                $stmt->bind_result($result->start, $result->eind);
                $stmt->fetch();
            }

            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $result;
    }
    
    /**
     * Method _checkMagPunten
     *
     * Controleer of punten geboekt mogen worden op basis van activiteit en rol
     *
     * @return bool Succes vlag
     *
     * @var Activiteiten $activiteiten_obj
     * @var Rollen $rollen_obj
     */
    private function _checkMagPunten() {
        // Lees alle activiteiten
        $activiteiten_obj = new Activiteiten($this->mysqli);
        try {
            $activiteiten_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
        // Lees alle rollen
        $rollen_obj = new Rollen($this->mysqli);
        try {
            $rollen_obj->read();
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
        return (($activiteiten_obj->magSparen($this->uren[0]->activiteit_id)) && ($rollen_obj->magSparen($this->uren[0]->rol_id)));
    }
}
