<?php
/**
 * Certificaten Object
 *
 * Object voor Certificaten tabel
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

/**
 * Required files
 */
require_once ('Certificaat_obj.php');

/**
 * Method Certificaten
 *
 * @since Class available since Release 1.0.0
 * @version 1.0.9
 */
class Certificaten
{

    /**
     * Array met Certificaat objecten
     *
     * @var Certificaat[]
     * @access public
     */
    public $certificaten;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Method constructor - Creeer certificaten object
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
     * Method create - Creeer certificaat
     *
     * @access public
     * @param Certificaat $record Certificaat object
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function create(Certificaat $record)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $prep_stmt = "
            INSERT
				ura_certificering
            SET
                rol_id = ?,
				looptijd = ?,
				uren = ?,
				groep_id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('iiii', $record->rol_id, $record->looptijd, $record->uren, $record->groep_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $record->id = (int) $stmt->insert_id;
            } else {
                $stmt->close();
                throw new Exception('Fout bij updaten certificaat', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read($record->id);
        return true;
    }

    /**
     * Method read - Lees certificaat of certificaten
     *
     * @access public
     * @param int $id
     *            optional certificaat id
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var int $rol_id
     * @var string $rol
     * @var string $looptijd
     * @var string $uren
     * @var int $groep_id
     * @var string $groep
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function read($id = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $rol_id = null;
        $rol = null;
        $looptijd = null;
        $uren = null;
        $groep_id = null;
        $groep = null;
        
        if (isset($id)) {
            $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        }

        $prep_stmt = "
            SELECT
                ura_certificering.id,
                ura_certificering.rol_id,
                ura_rollen.rol,
                ura_certificering.looptijd,
                ura_certificering.uren,
				ura_certificering.groep_id,
				ura_groepen.groep
            FROM
                ura_certificering
            JOIN
                ura_rollen ON ura_rollen.id = ura_certificering.rol_id
			JOIN
				ura_groepen ON ura_groepen.id = ura_certificering.groep_id";

        if (isset($id)) {
            $prep_stmt .= " WHERE ura_certificering.id = ? ";
        }

        $prep_stmt .= " ORDER BY ura_groepen.groep, ura_rollen.rol";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($id)) {
                $stmt->bind_param('i', $id);
            }
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $rol_id, $rol, $looptijd, $uren, $groep_id, $groep);

                while ($stmt->fetch()) {
                    $this->certificaten[] = new certificaat($id, $rol_id, $rol, $looptijd, $uren, $groep_id, $groep);
                }
            } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen certificaat gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen certificaten', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method update - Update certificaat
     *
     * @access public
     * @param Certificaat $record certificaat object
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function update(Certificaat $record)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $prep_stmt = "
            UPDATE
				ura_certificering
			SET
				rol_id = ?,
				looptijd = ?,
				uren = ?,
				groep_id = ?
			WHERE
				id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('iiiii', $record->rol_id, $record->looptijd, $record->uren, $record->groep_id, $record->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('certificaat niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read($record->id);
        return true;
    }

    /**
     * Methode delete - Delete certificaat
     *
     * @access public
     * @param int $id certificaat id
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

        if (! $this->_canDelete($id)) {
            throw new Exception('Kan certificaat niet verwijderen, nog in gebruik', 409);
        }

        $prep_stmt = "
            DELETE FROM
				ura_certificering
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
     * Method _canDelete - Kan worden gedelete
     *
     * Controleer of certificaat nog in gebruik is
     *
     * @access private
     * @param int $id Certificaat_id
     * @return bool Succes vlag
     */
    private function _canDelete($id)
    {
        // @todo Werkelijk controleren of certificaat nog in gebruik is
        return true;
    }
}
