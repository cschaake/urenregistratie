<?php
/**
 * Class Groepen | objects/Groepen_obj.php
 *
 * Object voor Groepen tabel
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
 * @version    1.2.0
 */
include_once ('Groep_obj.php');

/**
 * Class Groepen
 *
 * @since File available since Release 1.0.0
 * @version 1.2.0
 */
class Groepen
{

    /**
     * Array met Groep objecten
     *
     * @var Groep[]
     * @access public
     */
    public $groepen;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Method constructor - Creeer nieuw groepen object
     *
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
     * Method create - Create a new groepen record
     *
     * @access public
     * @param Groep $groep Groep object
     * @throws Exception
     * @return bool Success flag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function create(Groep $groep)
    {
        $prep_stmt = null;
        $stmt = null;
        
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
                $this->groepen[] = $groep;
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
     * Method read - Lees alle groepen
     *
     * @access public
     * @param int $id Groep id
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $groep
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function read($id = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $groep = null;
        
        if (isset($id)) {
            $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        }
        $prep_stmt = "
			SELECT
				id, groep
			FROM
				ura_groepen
			ORDER BY
				ura_groepen.groep";

        if (isset($id)) {
            $prep_stmt .= "WHERE
                id = ?";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($id)) {
                $stmt->bind_param('i', $id);
            }
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
     * Method update - Update groep record
     *
     * @access public
     * @param Groep $record Groep object
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function update(Groep $record)
    {
        $prep_stmt = null;
        $stmt = null;
        
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
     * Method delete - Delete groep
     *
     * @access public
     * @param int $id
     *            Groep id
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     * @var bool $result
     */
    public function delete($id)
    {
        $prep_stmt = null;
        $stmt = null;
        $result = null;
        
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);

        $result = false;
        if (! $this->_canDelete($id)) {
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
     * Method _canDelete - Controleer of groep verwijderd mag worden
     *
     * @access private
     * @param int $id
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var int $count
     * @var bool $result
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    private function _canDelete($id)
    {
        $prep_stmt = null;
        $stmt = null;
        
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

                $result = (! $count > 0);
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
