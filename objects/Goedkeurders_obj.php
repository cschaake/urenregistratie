<?php
/**
 * Goedkeurders Object
 *
 * Object voor goedkeurders
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
 * @since      File available since Release 1.0.6
 * @version    1.0.9
 */
include_once ('Goedkeurder_obj.php');

/**
 * Goedkeurders object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since Object available since Release 1.0.7
 * @version 1.0.9
 */
class Goedkeurders
{

    /**
     * Array met Goedkeurder objecten
     *
     * @var Goedkeurders[]
     * @access public
     */
    public $goedkeurders;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Creeer goedkeurders object
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
     * Creeer goedkeurder
     *
     * @access public
     * @param Goedkeurder $goedkeurder
     *            Goedkeurder object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function create(Goedkeurder $record)
    {
        $prep_stmt = "
            INSERT INTO
                ura_urengoedkeuren (username, groep_id, rol_id)
            VALUES
				(?,?,?)";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {

            foreach ($record->groepen as $groep) {
                $stmt->bind_param('sii', $record->username, $groep, $record->rollen[0]);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->affected_rows < 1) {
                    $stmt->close();
                    throw new Exception('Fout bij creeeren goedkeurder', 500);
                }
            }

            foreach ($record->rollen as $rol) {
                $stmt->bind_param('sii', $record->username, $record->groepen[0], $rol);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->affected_rows < 1) {
                    $stmt->close();
                    throw new Exception('Fout bij creeeren goedkeurder', 500);
                }
            }

            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->read($record->username);

        return true;
    }

    /**
     * Lees goedkeurders
     *
     * @access public
     * @param
     *            string optional $username
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
				ura_urengoedkeuren.username, users.firstName, users.lastName
            FROM
				ura_urengoedkeuren
			JOIN
				users ON ura_urengoedkeuren.username = users.username";

        if ($username) {
            $prep_stmt .= "
            WHERE ura_urengoedkeuren.username  = ? ";
        }

        $prep_stmt .= "
            ORDER BY ura_urengoedkeuren.username";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($username)) {
                $stmt->bind_param('s', $username);
            }
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname);

                while ($stmt->fetch()) {
                    $this->goedkeurders[] = new Goedkeurder($username, $firstname, $lastname, $this->_getGroepId($username), $this->getRolId($username));
                }
            } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen goedkeurder gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen goedkeurder', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Update goedkeurder
     *
     * @access public
     * @param Goedkeurder $goedkeurder
     *            Goedkeurder object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function update(Goedkeurder $record)
    {
        if (! $this->delete($record->username)) {
            throw new Exception('Fout bij verwijderen goedkeurder', 500);
        }
        if (! $this->create($record)) {
            throw new Exception('Foute bij toevoegen goedkeurder', 500);
        }

        return true;
    }

    /**
     * Delete activiteit
     *
     * @access public
     * @param string $username
     *            Username
     * @throws Exception
     * @return bool Succes vlag
     */
    public function delete($username)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        $prep_stmt = "
            DELETE FROM
                ura_urengoedkeuren
            WHERE
                username = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
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
     * Get groep_id's of user
     *
     * @access private
     * @param string $username
     *            Username
     * @throws Exception
     * @return array $groepen
     */
    private function _getGroepId($username)
    {
        $groepen = array();

        $prep_stmt = "
			SELECT DISTINCT
				groep_id
			FROM
				ura_urengoedkeuren
			WHERE
				username = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            // Validate and transform input
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $stmt->bind_result($groep);

                while ($stmt->fetch()) {
                    $groepen[] = $groep;
                }
            } else {
                $stmt->close();
                throw new Exception('Fout bij selecteren groepen', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $groepen;
    }

    /**
     * Get rol_id's of user
     *
     * Get the rol_id's of the user
     *
     * @access public
     * @param string $username
     *            Username
     * @throws Exception
     * @return array $rollen
     */
    public function getRolId($username)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        $rollen = array();

        $prep_stmt = "
			SELECT DISTINCT
				rol_id
			FROM
				ura_urengoedkeuren
			WHERE
				username = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $stmt->bind_result($rol);

                while ($stmt->fetch()) {
                    $rollen[] = $rol;
                }
            } else {
                $stmt->close();
                throw new Exception('Fout bij selecteren rollen', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $rollen;
    }
}
