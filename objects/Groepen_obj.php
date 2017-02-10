<?php
/**
 * Groepen Object
 *
 * Object voor Groepen tabel
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

include_once('Groep_obj.php');
 
/**
 * Groepen object
 * 
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * 
 * @since      File available since Release 1.0.0
 */ 
class Groepen {
    
    /**
     * Array met Groep objecten
     * 
     * @var    Groep[]
     * @access public
     */
    public $groepen;

    /**
     * Mysqli object
     * 
     * @var    mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Creeer nieuw groepen object
     *
     * @param mysqli $mysqli 
     * @throws Exception  
     * @return bool       Success flag
     */
    public function __construct($mysqli) {
        if (!is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object', 500);
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Create a new groepen record
     *
	 * @access public
     * @param  Groep 		$Groep Groep object
     * @throws Exception  
     * @return bool       	Success flag
     */
    public function create(Groep $groep)
    {
        $prep_stmt = "
            INSERT INTO 
				ura_groepen
            SET
                groep = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $groep->groep);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $groep->id = (int) $stmt->insert_id;
				$this->groepen = $groep;
            } else {
                $stmt->close();
                throw new Exception('Fout bij updaten groep', 500);
            }
			$stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
		return true;
    }
	
	/**
     * Lees alle groepen
	 *
	 * @access public
     * @throws Exception  
     * @return bool       Succes vlag
     */
	public function read() 
	{
        $prep_stmt = "
			SELECT 
				id, groep 
			FROM 
				ura_groepen
			ORDER BY 
				ura_groepen.groep";

        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $groep);
                
                while ($stmt->fetch()) {
                    $this->groepen[] = new groep($id, $groep);
                }
	        } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen groep gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen groep', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error');
        }
        return true;
    }

	/**
     * Update groep record
     *
	 * @access public
	 * @param Groep 	  $record Groep object
     * @throws Exception  
     * @return bool       Succes vlag
     */
    public function update($record)
    {
        $prep_stmt = "
            UPDATE 
				ura_groepen
            SET
                groep = ?
			WHERE	
				id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('si', $record->groep, $record->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Groep niet gevonden', 404);
            }
			$stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
		
		$this->groepen[] = $record;
		return true;
    }
	
	/**
     * Delete groep 
     *
	 * @access public
     * @param  int 		  $id 	Groep id
     * @throws Exception  
     * @return bool       Succes vlag
     */
    public function delete($id)
    {
		$result = false;
		if (!$this->_canDelete($id)) {
			throw new Exception('Kan groep niet verwijderen, nog in gebruik', 409);
		}
		
        $prep_stmt = "
            DELETE FROM 
				ura_groepen
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
     * Controleer of groep verwijderd mag worden
	 *
	 * @access private
     * @param  int        $id 
     * @throws Exception  
     * @return bool       Succes vlag
     */
	private function _canDelete($id) {
		$result = false;
		$prep_stmt = "SELECT COUNT(*) count
						FROM 
							ura_certificaat,
							ura_activiteiten,
							ura_certificering,
							ura_urenboeken,
							ura_urengoedkeuren
						WHERE
							ura_certificaat.groep_id = ?
						OR ura_activiteiten.groep_id = ?
						OR ura_certificering.groep_id = ?
						OR ura_urenboeken.groep_id = ?
						OR ura_urengoedkeuren.groep_id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('iiiii', $id, $id, $id, $id, $id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 0) {
				$stmt->bind_result($count);
				$stmt->fetch();
				
				$result = (!$count > 0);
            } else {
                $stmt->close();
                throw new Exception('Interne fout bij verwijderen groep', 500);
            }
			$stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        

		return $result;
	}
}
 ?>