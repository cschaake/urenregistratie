<?php
/**
 * Rollen Object
 *
 * Object voor Rollen tabel
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
 * @version    1.0.9
 */
include_once ('Rol_obj.php');

/**
 * Rollen object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.0
 * @version 1.0.9
 */
class Rollen
{

    /**
     * Array met Rol objecten
     *
     * @var Rol[]
     * @access public
     */
    public $rollen;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Creeer rollen object
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
     * Creeer rol
     *
     * @access public
     * @param Rol $rol
     *            Rol object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function create(Rol $record)
    {
        $prep_stmt = "
            INSERT
				ura_rollen
            SET
                rol = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $record->rol);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $record->id = (int) $stmt->insert_id;
                $this->rollen[] = $record;
            } else {
                $stmt->close();
                throw new Exception('Fout bij updaten rol', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        return true;
    }

    /**
     * Lees rollen
     *
     * @access public
     * @throws Exception
     * @return bool Succes vlag
     */
    public function read($username = null)
    {
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            SELECT DISTINCT
                ura_rollen.id,
                ura_rollen.rol
            FROM
                ura_rollen";

        if (isset($username)) {
            $prep_stmt .= "
				JOIN
					ura_urenboeken ON ura_urenboeken.rol_id = ura_rollen.id
				WHERE
					ura_urenboeken.username = ?
			";
        }

        $prep_stmt .= "
			ORDER BY
				ura_rollen.rol
            ";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($username)) {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $rol);

                while ($stmt->fetch()) {
                    $this->rollen[] = new rol($id, $rol);
                }
            } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen rol gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen activiteit', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        return true;
    }

    /**
     * Update rol
     *
     * @access public
     * @param Rol $activiteit
     *            Rol object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function update(Rol $record)
    {
        $prep_stmt = "
            UPDATE
				ura_rollen
            SET
                rol = ?
			WHERE
				id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('si', $record->rol, $record->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Rol niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        $this->rollen[] = $record;
        return true;
    }

    /**
     * Delete rol
     *
     * @access public
     * @param int $id
     *            Rol id
     * @throws Exception
     * @return bool Succes vlag
     */
    public function delete($id)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);

        $result = false;
        if (! $this->_canDelete($id)) {
            throw new Exception('Kan rol niet verwijderen, nog in gebruik', 409);
        }

        $prep_stmt = "
            DELETE FROM
				ura_rollen
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

        return true;
    }

    /**
     * Kan worden gedelete
     *
     * Controleer of rol nog in gebruik is
     *
     * @access private
     * @param int $id
     *            Rol id
     * @return bool Succes vlag
     */
    private function _canDelete($id)
    {
        $result = false;
        $prep_stmt = "SELECT COUNT(*) count
						FROM
							ura_uren,
							ura_certificaat,
							ura_certificering,
							ura_urenboeken,
							ura_urengoedkeuren
						WHERE
							ura_uren.rol_id = ?
						OR
							ura_certificaat.rol_id = ?
						OR
							ura_certificering.rol_id = ?
						OR
							ura_urenboeken.rol_id = ?
						OR
							ura_urengoedkeuren.rol_id = ?";

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
                throw new Exception('Interne fout bij verwijderen rol', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $result;
    }
}
