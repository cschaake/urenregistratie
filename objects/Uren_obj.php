<?php
/**
 * Uren Object
 *
 * Object voor Uren tabel
 *
 * PHP version 5.4
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
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.7
 */
require_once ('Uur_obj.php');

/**
 * Uren object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *         
 * @since Class available since Release 1.0.0
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
     * Creeer uren object
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
     * Creeer uur
     *
     * @access public
     * @param Uur $uur
     *            Uur object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function create(Uur $uur_obj)
    {
        // Controleer overlap in tijd
        $time = $this->_checkTime($uur_obj->username, $uur_obj->datum, $uur_obj->start, $uur_obj->eind);
        if ($time) {
            throw new Exception('Reeds uren geboekt tussen ' . $time->start . ' en ' . $time->eind);
        }
        
        $prep_stmt = "
            INSERT 
				ura_uren 
            SET 
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
                datum = ?,
                start = ?,
                eind = ?,
                uren = ?,
                akkoord = 0,
                reden = '',
				opmerking = ?";
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('siisssds', $uur_obj->username, $uur_obj->activiteit_id, $uur_obj->rol_id, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->uren, $uur_obj->opmerking);
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
            throw new Exception('Database error');
        }
        $this->read($uur_obj->username, $id);
        
        return true;
    }

    /**
     * Lees uur of uren
     *
     * @access public
     * @param string $username
     *            optional Username
     * @param int $id
     *            optional Uur id
     * @param date $pijldatum
     *            optional Toon alleen uren voor certificaat dat actief is tijdens pijldatum
     * @throws Exception
     * @return bool Succes vlag
     */
    public function read($username = null, $id = null, $pijldatum = null)
    {
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
     * Lees goed te keuren uren
     *
     * @access public
     * @param string $username
     *            optional Username
     * @throws Exception
     * @return bool Succes vlag
     */
    public function readGoedTeKeuren($username = null)
    {
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
				ura_activiteiten ON ura_uren.activiteit_id = ura_activiteiten.id";
        
        if ($username) {
            $prep_stmt .= " 
				JOIN 
					ura_urengoedkeuren ON ura_uren.rol_id = ura_urengoedkeuren.rol_id";
        }
        
        $prep_stmt .= "
            WHERE akkoord < 1 ";
        
        if ($username) {
            $prep_stmt .= "
                AND ura_urengoedkeuren.username = ?";
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
     * Update uur
     *
     * @access public
     * @param Uur $uur
     *            Uur object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function update(Uur $uur_obj)
    {
        $prep_stmt = "
            UPDATE 
				ura_uren 
            SET 
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
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
            $stmt->bind_param('siisssdissi', $uur_obj->username, $uur_obj->activiteit_id, $uur_obj->rol_id, $uur_obj->datum, $uur_obj->start, $uur_obj->eind, $uur_obj->uren, $uur_obj->akkoord, $uur_obj->reden, $uur_obj->opmerking, $uur_obj->id);
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
     * Delete uur
     *
     * @access public
     * @param int $id
     *            Uur id
     * @param string $username Username
     * @throws Exception
     * @return bool Succes vlag
     */
    public function delete($id, $username = null)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        
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
     * Uur goedkeuren
     *
     * @access public
     * @param int $id
     *            Uur id
     * @throws Exception
     * @return bool Succes vlag
     */
    public function goedkeuren($id)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        
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
        
        return $result;
    }

    /**
     * Uur afkeuren
     *
     * @access public
     * @param int $id
     *            Uur id
     * @param string $reden Reden
     * @throws Exception
     * @return bool Succes vlag
     */
    public function afkeuren($id, $reden)
    {
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
     * Check time
     *
     * Check of er reeds uren zijn geboekt op het gevraagde tijdstip
     *
     * @param string $username
     *            Username
     * @param string $date
     *            Datum
     * @param string $start
     *            Start tijd
     * @param string $end
     *            End tijd
     * @return bool Succes vlag
     */
    private function _checkTime($username, $date, $start, $end)
    {
        $result = false;
        
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
				start < ?";
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('ssss', $username, $date, $start, $end);
            
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
}
