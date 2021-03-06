<?php
/**
 * Opleidingsuren Object
 *
 * Object voor opleidingsuren tabel
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
 * @copyright  2015 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.5
 * @version       1.0.9
 */
include_once ('Uur_obj.php');
include_once ('User_obj.php');

/**
 * Opleidingsuren object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2015 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.0
 */
class Opleidingsuren
{

    /**
     * Array met Uur objecten
     *
     * @var Uur[]
     * @access public
     */
    public $uren;

    /**
     * Array met User objecten
     *
     * @var User[]
     * @access public
     */
    public $users;

    /**
     * Mysqli
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Create the opleidingsuren object
     *
     * Creates the opleidingsuren object that will contain all uren stuff
     *
     * @param mysqli $mysqli
     *            Valid mysqli object
     *
     * @return bool Success flag
     */
    public function __construct(mysqli $mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object');
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Update the opleidingsuren object
     *
     * Update a opleidingsuren record
     * Wordt vermoedelijk niet gebruikt!
     *
     * @param Uur $record
     *            opleidingsuur object
     *
     * @return array Urenrecord
     */
    public function update(Uur $record)
    {
        $prep_stmt = "
            UPDATE ura_uren
            SET
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
                datum = ?,
                start = '00:00:00',
                eind = '00:00:00',
                uren = ?,
                akkoord = ?,
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('siisdii', $record->username, $record->activiteit_id, $record->rol_id, $record->datum, $record->uren, $record->akkoord, $record->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Opleidingsuur record niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read();

        return true;
    }

    /**
     * Get uren for opleidingsuren
     *
     * @param string $username
     *            Username of the current loggedin user
     *
     * @return array Uren records
     */
    private function _getUren($username = null)
    {
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            SELECT
                ura_uren.id, ura_uren.username, users.firstName, users.lastName, ura_uren.activiteit_id, ura_activiteiten.activiteit,
                    ura_uren.rol_id, ura_rollen.rol, ura_uren.datum, ura_uren.start, ura_uren.eind, ura_uren.uren, ura_uren.opmerking
            FROM ura_uren
            JOIN users ON ura_uren.username = users.username
            JOIN ura_rollen ON ura_uren.rol_id = ura_rollen.id
            JOIN ura_activiteiten ON ura_uren.activiteit_id = ura_activiteiten.id";

        if (isset($username)) {
            $prep_stmt .= " JOIN ura_urengoedkeuren ON ura_uren.rol_id = ura_urengoedkeuren.rol_id";
        }

        $prep_stmt .= "
            WHERE
                flag = 1
            ";

        if (isset($username)) {
            $prep_stmt .= " AND ura_urengoedkeuren.username = ?";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if ($username) {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $username, $firstname, $lastname, $activiteit_id, $activiteit, $rol_id, $rol, $datum, $start, $eind, $uren, $opmerking);

                while ($stmt->fetch()) {
                    $uur = $this->uren[] = new Uur($username, $activiteit_id, $rol_id, $datum, $start, $eind, $uren, $opmerking, null, null, null, $id);
                    $uur->addName($firstname, $lastname);
                    $uur->addActiviteit($activiteit_id, $activiteit);
                    $uur->addRol($rol_id, $rol);
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
     * Get users for opleidingsuren
     *
     * Get all the users that have group_id 1
     *
     * @return array Users records
     */
    private function _getUsers()
    {
        $prep_stmt = "
            SELECT ura_urenboeken.username, users.firstname, users.lastname
            FROM ura_urenboeken
            LEFT JOIN users
            ON ura_urenboeken.username = users.username
            WHERE ura_urenboeken.groep_id = " . OPLEIDINGS_GROEP_ID;

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($username, $firstname, $lastname);

                while ($stmt->fetch()) {
                    $this->users[] = new User($username, $firstname, $lastname);
                }
            } elseif ($stmt->num_rows == 0) {
                $stmt->close();
                throw new Exception('Geen user records gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen users', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Get the combined uren and users
     *
     * @param string $username
     *            Username of the current loggedin user
     * @param int $id
     *            Record id
     *
     * @return array Records
     */
    public function read($username = null)
    {
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $this->_getUren($username);
        $this->_getUsers();
        return true;
    }

    /**
     * Insert a new uren record
     *
     * @param Uur $record
     *            Uur object
     *
     * @return array Record
     */
    public function create(Uur $record)
    {
        $prep_stmt = "
            INSERT ura_uren
            SET
                username = ?,
                activiteit_id = ?,
                rol_id = ?,
                datum = ?,
                start = '00:00:00',
                eind = '00:00:00',
                uren = ?,
                akkoord = 1,
                flag = 1";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('siisd', $record->username, $record->activiteit_id, $record->rol_id, $record->datum, $record->uren);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows == 0) {
                $stmt->close();
                throw new Exception('Error updating record', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        $this->read();

        return true;
    }

    /**
     * Delete an uren record
     *
     * @param string $id
     *            Id of the record to be deleted
     *
     * @return bool
     */
    public function delete($id)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_STRING);

        $prep_stmt = "
            DELETE FROM
                ura_uren
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
}
