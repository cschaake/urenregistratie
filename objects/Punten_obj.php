<?php
/**
 * Class Punten | objects/Punten_obj.php
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
 * @since      File available since Release 1.2.1
 * @version    1.2.1
 */

/**
 * Required files
 */
require_once ('Punt_obj.php');

/**
 * Class Punten - Collection van Punt objecten
 *
 * @since Class available since Release 1.0.0
 * @version 1.2.1
 */
class Punten
{
    
    /**
     * Array met Punt objecten
     *
     * @var Punt[]
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
     * Method constructor - Creeer punten object
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
     * Method create - Creeer punt
     *
     * @access public
     * @param Punt $punt_obj Punt object
     * @throws Exception
     * @return bool Succes vlag
     *
     * @var array $time
     * @var string $prep_stmt SQL Statement
     * @var SQLite3Stmt $stmt
     */
    public function create(Punt $punt_obj)
    {
        $prep_stmt = null;
        $stmt = null;
        
        // Bepaal werkelijke uren door te controleren of er al geen uren waren geboekt
        $punt_obj->punten = $this->_checkTime($punt_obj->username, $punt_obj->datum, $punt_obj->start, $punt_obj->eind, $punt_obj->uur_id);
        
        $prep_stmt = "
            INSERT
				ura_punten
            SET
                username = ?,
                datum = ?,
                start = ?,
                eind = ?,
                uur_id = ?,
                createDate = ?,
                punten = ?,
				waardePunten = ?,
                puntenGebruikt = 0";
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('ssssisdd', $punt_obj->username, $punt_obj->datum, $punt_obj->start, $punt_obj->eind, $punt_obj->uur_id, $punt_obj->createDate, $punt_obj->punten, $punt_obj->waardePunten);
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
        $this->read($punt_obj->username, $id);
        
        return true;
    }
    
    /**
     * Method read - Lees punt of punten
     *
     * @access public
     * @param string $username optional Username
     * @param int $id optional Punt id
     * @throws Exception
     * @return bool Succes vlag
     *
     * @var int $id
     * @var string $username
     * @var string $datum 
     * @var string $start
     * @var string $eind
     * @var int $uur_id
     * @var string $createDate
     * @var number $punten 
     * @var number $waardePunten
     * @var number $puntenGebruikt
     * @var Punt $punt_obj
     * @var string $prep_stmt SQL Statement
     * @var SQLite3Stmt $stmt
     */
    public function read($username = null, $id = null)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $id = null;
        $username = null;
        $datum = null;
        $start = null;
        $eind = null; 
        $uur_id = null; 
        $createDate = null;
        $punten = null;
        $waardePunten = null;
        $puntenGebruikt = null;
        
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($id)) {
            $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        }
        
        $prep_stmt = null;
        
        $prep_stmt = "
            SELECT
                ura_punten.id,
                ura_punten.username,
                ura_punten.datum,
                ura_punten.start,
                ura_punten.eind,
                ura_punten.uur_id,
                ura_punten.createDate,
                ura_punten.punten,
                ura_punten.waardePunten,
                ura_punten.puntenGebruikt
            FROM
                ura_punten";
        if ($username) {
            $prep_stmt .= " WHERE ura_uren.username = ? ";
        }
        if ($id) {
            $prep_stmt .= " AND ura_uren.id = ?";
        }
        $prep_stmt .= " ORDER BY ura_punten.datum";
        
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
                $stmt->bind_result($id, $username, $datum, $start, $eind, $uur_id, $createDate, $punten, $waardePunten, $puntenGebruikt);
                
                while ($stmt->fetch()) {
                    $punt_obj = new Punt($id, $username, $datum, $start, $eind, $uur_id, $createDate, $punten, $waardePunten, $puntenGebruikt);
                    $this->punten[] = $punt_obj;
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
     * Method update - Update punt
     *
     * @access public
     * @param Punt $punt_obj Punt object
     * @throws Exception
     * @return bool Succes vlag
     *
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function update(Punt $punt_obj)
    {
        $prep_stmt = null;
        $stmt = null;
        
        // Bepaal werkelijke uren door te controleren of er al geen uren waren geboekt
        $punt_obj->punten = $this->_checkTime($punt_obj->username, $punt_obj->datum, $punt_obj->start, $punt_obj->eind, $punt_obj->uur_id);
        
        $prep_stmt = "
            UPDATE
				ura_punten
            SET
                username, 
                datum, 
                start, 
                eind, 
                uur_id, 
                createDate, 
                punten, 
                waardePunten
            WHERE
                id = ?";
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('ssssisddi', $punt_obj->username, $punt_obj->datum, $punt_obj->start, $punt_obj->eind, $punt_obj->uur_id, $punt_obj->createDate, $punt_obj->punten, $punt_obj->waardePunten, $punt_obj->id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Punt record niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        
        $this->read($punt_obj->username, $punt_obj->id);
        
        return true;
    }
    
    /**
     * Method delete - Delete punt
     *
     * @access public
     * @param int $id Punt id
     * @param string $username Username
     * @throws Exception
     * @return int Number of deleted rows
     *
     * @var int $result
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
				ura_punten
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
     * Method _checkTime - Check time
     *
     * Check of er reeds uren zijn geboekt op het gevraagde tijdstip en geen array met start en eindtijden terug.
     *
     * @param string $username Username
     * @param string $date Datum
     * @param string $start Start tijd
     * @param string $end End tijd
     * @param int $uur_id optioneel te wijzigen uur_id
     * @return bool Succes vlag
     *
     * @var string start2
     * @var string eind2
     * @var string prep_stmt
     * @var mysqli_stmt stmt
     */
    private function _checkTime($username, $date, $start, $eind, $uur_id = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $start2 = null;
        $eind2 = null;
        
        if ($start > $eind) {
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
                akkoord = 1";
        
        if ($uur_id) {
            $prep_stmt .= "
            AND
                id != ?";
        }
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            if ($uur_id) {
                $stmt->bind_param('ssssii', $username, $date, $start, $eind, $uur_id);
            } else {
                $stmt->bind_param('ssssi', $username, $date, $start, $eind);
            }
            
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Fout bij bepalen tijds overlap', 500);
            } elseif ($stmt->affected_rows > 0) {
                $stmt->bind_result($start2, $eind2);
                while ($stmt->fetch()) {
                    
                    if (($eind2 > $start) && ($eind2 < $eind)) {
                        $start = $eind2;   
                    }
                    
                    if (($start2 < $eind) && ($start2 > $start)) {
                        $eind = $start2;
                    }
                }
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        
        return $eind - $start;
    }
}
