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
 * @version       1.0.6
 */

/**
 * Opleidingsuren object
 *
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2015 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @since      File available since Release 1.0.0
 */
class Opleidingsuren
{

    /**
     * Username
     *
     * @var string
     * @access private
     */
    private $username;

    /**
     * Voornaam
     *
     * @var string
     * @access private
     */
    private $voornaam;

    /**
     * Achternaam
     *
     * @var string
     * @access private
     */
    private $achternaam;

    /**
     * Activiteit
     *
     * @var string
     * @access private
     */
    private $activiteit;

    /**
     * rol
     *
     * @var string
     * @access private
     */
    private $rol;

    /**
     * datum
     *
     * @var string
     * @access private
     */
    private $datum;

    /**
     * start
     *
     * @var string
     * @access private
     */
    private $start;

    /**
     * eind
     *
     * @var string
     * @access private
     */
    private $eind;

    /**
     * aantal
     *
     * @var string
     * @access private
     */
    private $aantal;

    /**
     * akkoord
     *
     * @var int
     * @access private
     */
    private $akkoord;

    /**
     * Create the opleidingsuren object
     *
     * Creates the opleidingsuren object that will contain all uren stuff
     *
     * @param mysqli $mysqli Valid mysqli object
     *
     * @return bool Success flag
     */
    public function __construct($mysqli)
    {
        if (!is_a($mysqli, 'mysqli'))
        {
            throw new Exception('$mysqli is not a valid mysqli object');
        } else
        {
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
     * @param array $record Array containing a opleidingsuren record
     *
     * @return array Urenrecord
     */
    public function update($record)
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

        if ($stmt)
        {
            // Validate and transform input
            $record->username = filter_var($record->username, FILTER_SANITIZE_STRING);
            $record->activiteit_id = (int) filter_var($record->activiteit_id, FILTER_SANITIZE_STRING);
            $record->rol_id = (int) filter_var($record->rol_id, FILTER_SANITIZE_STRING);
            $record->datum = date('Y-m-d',strtotime($record->datum));
            $record->uren = filter_var($record->uren, FILTER_SANITIZE_STRING);
            $record->akkoord = (int) filter_var($record->akkoord, FILTER_SANITIZE_STRING);
			
            $stmt->bind_param('siisdii', $record->username, $record->activiteit_id, $record->rol_id, $record->datum, $record->uren, $record->akkoord, $record->id);

            // Execute the prepared query.
            $stmt->execute();
            $stmt->store_result();

            // Affected rows is 0 on no changes, or 1 on change. -1 when not found
            if ($stmt->affected_rows >= 0)
            {
                $stmt->close();
                return $record;
            } else
            {
                $stmt->close();
                throw new Exception('Error updating record');
            }
        } else
        {
            throw new Exception('Database error');
        }
    }

    /**
     * Get uren for opleidingsuren
     *
     * @param string $username Username of the current loggedin user
     *
     * @return array Uren records
     */
    private function _getUren($username)
    {
        $prep_stmt = "
            SELECT
                ura_uren.id, ura_uren.username, users.firstName, users.lastName, ura_uren.activiteit_id, ura_activiteiten.activiteit,
                    ura_uren.rol_id, ura_rollen.rol, ura_uren.datum, ura_uren.start, ura_uren.eind, ura_uren.uren, ura_uren.opmerking
            FROM ura_uren
            JOIN users ON ura_uren.username = users.username
            JOIN ura_rollen ON ura_uren.rol_id = ura_rollen.id
            JOIN ura_activiteiten ON ura_uren.activiteit_id = ura_activiteiten.id";

        if ($username)
        {
            $prep_stmt .=" JOIN ura_urengoedkeuren ON ura_uren.rol_id = ura_urengoedkeuren.rol_id";
        }

        $prep_stmt .= "
            WHERE
                flag = 1
            ";

        if ($username)
        {
            $prep_stmt .= " AND ura_urengoedkeuren.username = ?";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt)
        {
            if ($username)
            {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0)
            {
                $stmt->bind_result($id, $username, $firstname, $lastname, $activiteit_id, $activiteit, $rol_id, $rol, $datum, $start, $eind, $uren, $opmerking);

                $uren_array = array();

                while ($stmt->fetch())
                {
                    $uren_array[] = ['id'=>$id, 'username'=>$username, 'voornaam'=>$firstname, 'achternaam'=>$lastname, 'activiteit_id'=>$activiteit_id, 'activiteit'=>$activiteit, 'rol_id'=>$rol_id, 'rol'=>$rol, 'datum'=>$datum, 'start'=>$start, 'eind'=>$eind, 'aantal'=>$uren, 'opmerking'=>$opmerking];
                }
            } elseif ($stmt->num_rows == 0)
            {
                    $uren_array = null;
            } else
            {
                $stmt->close();
                throw new Exception('No uren found');
            }

        } else
        {
            throw new Exception('Database error');
        }

        $stmt->close();
        return $uren_array;
    }

    /**
     * Get users for opleidingsuren
     *
     * Get all the users that have group_id 1
     * @TODO parameterise group_id for opleidingen
     *
     * @return array Users records
     */
    private function _getUsers() {
        $prep_stmt = "
            SELECT ura_urenboeken.username, users.firstname, users.lastname
            FROM ura_urenboeken
            LEFT JOIN users
            ON ura_urenboeken.username = users.username
            WHERE ura_urenboeken.groep_id = " . OPLEIDINGS_GROEP_ID;

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt)
        {

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0)
            {
                $stmt->bind_result($username, $firstname, $lastname);

                $users_array = array();

                while ($stmt->fetch())
                {
                    $users_array[] = ['username'=>$username, 'voornaam'=>$firstname, 'achternaam'=>$lastname];
                }
            } elseif ($stmt->num_rows == 0)
            {
                    $users_array = null;
            } else
            {
                $stmt->close();
                throw new Exception('No users found');
            }

        } else
        {
            throw new Exception('Database error');
        }

        $stmt->close();
        return $users_array;
    }

    /**
     * Get the combined uren and users
     *
     * @param string $username Username of the current loggedin user
     *
     * @return array Records
     */
    public function get($username) {
        $array['uren'] = $this->_getUren($username);
        $array['users'] = $this->_getUsers();
        return $array;
    }

    /**
     * Insert a new uren record
     *
     * @param array $record Uren record
     *
     * @return array Record
     */
    public function insert($record)
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

        if ($stmt)
        {
            // Validate and transform input
            $record->username = filter_var($record->username, FILTER_SANITIZE_STRING);
            $record->activiteit_id = (int) filter_var($record->activiteit, FILTER_SANITIZE_STRING);
            $record->rol_id = (int) filter_var($record->rol, FILTER_SANITIZE_STRING);
            $record->datum = date('Y-m-d',strtotime($record->datum . '-01-01'));
            $record->uren = filter_var($record->uren, FILTER_SANITIZE_STRING);

            $stmt->bind_param('siisd', $record->username, $record->activiteit_id, $record->rol_id, $record->datum, $record->uren);

            $stmt->execute();
            $stmt->store_result();

            // Affected rows is 0 on no changes, or 1 on change. -1 when not found
            if ($stmt->affected_rows == 1)
            {
                $record->id = (int) $stmt->insert_id;
                $stmt->close();
                return $record;
            } else
            {
                $stmt->close();
                throw new Exception('Error updating record');
            }
        } else
        {
            throw new Exception('Database error');
        }
    }

    /**
     * Delete an uren record
     *
     * @param string $id Id of the record to be deleted
     *
     * @return bool
     */
    public function delete($id)
    {
        $prep_stmt = "
            DELETE FROM ura_uren
            WHERE id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt)
        {

            // Validate and transform input
            $stmt->bind_param('i', $id);

            $stmt->execute();
            $stmt->store_result();

            // Affected rows is 0 on no changes, or 1 on change. -1 when not found
            if ($stmt->affected_rows >= 1)
            {
                $stmt->close();
                return true;
            } else
            {
                $stmt->close();
                throw new Exception('Error deleting record');
            }
        } else
        {
            throw new Exception('Database error');
        }

    }
}
