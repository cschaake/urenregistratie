<?php
/**
 * Class Boekers | objects/Boekers_obj.php
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
 * @since      File available since Release 1.0.9
 * @version    1.2.0
 */

/**
 * Required files
 */
include_once 'User_obj.php';
include_once 'Rol_obj.php';
include_once 'Groep_obj.php';

/**
 * Class Boekers - Collectie van gebruikers die kunnen boeken
 *
 * @since Class available since Release 1.0.9
 * @version 1.0.9
 */
class Boekers
{
    /**
     * Boekers
     *
     * @var User[]
     * @access public
     */
    public $boekers;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Method constructor - Create the activtiteiten object
     *
     * Creates the activiteiten object that will contain all activiteiten stuff
     *
     * @param mysqli $mysqli
     *            Valid mysqli object
     *
     * @return bool Success flag
     */
    public function __construct($mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object');
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Method create - Creeer nieuwe boeker
     *
     * @param User $boeker
     * @throws Exception
     * @return boolean
     * 
     * @var string $user
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function create(User $boeker)
    {
        $prep_stmt = null;
        $stmt = null;
        
        if (isset($boeker->rollen)) {
            foreach ($boeker->rollen as $rol) {
                // Insert records
                $prep_stmt = '
                    INSERT
                    INTO
                        ura_urenboeken
                        (username,
                            rol_id,
                            groep_id)
                    VALUES (?, ?, ?)';

                $stmt = $this->mysqli->prepare($prep_stmt);

                if ($stmt) {
                    $stmt->bind_param('sii', $boeker->username, $rol->id, $boeker->groepen[0]->id);

                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows < 0) {
                        $stmt->close();
                        throw new Exception('Fout bij updaten boeker (fout 2)', 500);
                    }
                } else {
                    throw new Exception('Database error', 500);
                }
                $stmt->close();

                // Insert records
                $prep_stmt = '
                    INSERT
                    INTO
                        ura_certificaat
                            (username,
                                rol_id,
                                gecertificeerd,
                                verloopt,
                                groep_id)
                    VALUES (?, ?, ?, ?, ?)';

                $stmt = $this->mysqli->prepare($prep_stmt);

                if ($stmt) {
                    $stmt->bind_param('sissi', $boeker->username, $rol->id, $rol->gecertificeerd, $rol->verloopt, $boeker->groepen[0]->id);

                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows < 0) {
                        $stmt->close();
                        throw new Exception('Fout bij updaten boeker (fout 3)', 500);
                    }
                } else {
                    throw new Exception('Database error', 500);
                }
                $stmt->close();
            }
        } else {
            // Insert records
            $prep_stmt = '
                INSERT
                INTO
                    ura_urenboeken
                    (username)
                VALUES (?)';

            $stmt = $this->mysqli->prepare($prep_stmt);

            if ($stmt) {
                $stmt->bind_param('s', $boeker->username);

                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows < 0) {
                    $stmt->close();
                    throw new Exception('Fout bij updaten boeker (fout 4)', 500);
                }
            } else {
                throw new Exception('Database error', 500);
            }
            $stmt->close();
        }
        $this->read($boeker->username);

        return true;
    }

    /**
     * Method read - Lees boeker(s)
     *
     * @param
     *            string optional $username
     * @throws Exception
     * @return bool Success flag
     * 
     * @var string $firstname
     * @var string $lastname
     * @var int $groep_id
     * @var int $rol_id
     * @var string $rol
     * @var string $gecertificeerd
     * @var string $verloopt
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function read($username = null)
    {
        $prep_stmt = null;
        $stmt = null;
        $firstname = null;
        $lastname = null;
        $groep_id = null;
        $rol_id = null;
        $gecertificeerd = null;
        $verloopt = null;

        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            SELECT
                users.username,
                users.firstName,
                users.lastName,
                ura_urenboeken.groep_id,
                ura_urenboeken.rol_id,
                ura_rollen.rol,
                ura_certificaat.gecertificeerd,
                ura_certificaat.verloopt
            FROM
                ura_urenboeken
                    LEFT JOIN
                users ON ura_urenboeken.username = users.username
                    LEFT JOIN
                ura_rollen ON ura_urenboeken.rol_id = ura_rollen.id
                    LEFT JOIN
                ura_certificaat ON ura_certificaat.rol_id = ura_urenboeken.rol_id
                    AND ura_certificaat.username = ura_urenboeken.username ";
        if (isset($username)) {
            $prep_stmt .= "
                   WHERE
                        users.username = ?";
        }

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($username)) {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname, $groep_id, $rol_id, $rol, $gecertificeerd, $verloopt);

                $count = 0;

                while ($stmt->fetch()) {

                    // Get the index of the current user if in object
                    $index = $this->_getIndexOf($username);

                    // Check for not existing boeker, and create a new one
                    if (! isset($index)) {
                        $this->boekers[$count] = new User($username, $firstname, $lastname);
                        $index = $count;
                        $count ++;
                    }

                    if ($rol_id) {
                        $this->boekers[$index]->addRollen(new Rol($rol_id, $rol, $gecertificeerd, $verloopt));
                    }

                    if ($groep_id) {
                        $this->boekers[$index]->addGroepen(new Groep($groep_id));
                    }
                }
            } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen boeker gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen boekers', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method update - Update een bestaande boeker
     *
     * @param User $boeker
     * @throws Exception
     * @return boolean
     */
    public function update(User $boeker)
    {
        try {
            $this->delete($boeker->username);
        } catch (Exception $e) {
            throw new Exception('Fout bij updaten boeker (fout 1)', 500);
        }

        try {
            $this->create($boeker);
        } catch (Exception $e) {
            throw new Exception('Fout bij updaten boeker (fout 5)', 500);
        }

        return true;
    }

    /**
     * Method delete - Delete een bestaande boeker
     *
     * @param string $username
     * @throws Exception
     * @return boolean
     * 
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function delete($username)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        // Delete all records
        $prep_stmt = '
                DELETE
                FROM
                    ura_urenboeken
                WHERE
                    username = ?';

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

        if ($result) {
            $prep_stmt = '
                    DELETE
                    FROM
                        ura_certificaat
                    WHERE
                        username = ?';

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
        }

        return $result;
    }

    /**
     * Method _getIndexOf - Geeft de index van de geselecteerde username in de boekers array
     *
     * @param string $username
     * @return NULL|int
     */
    private function _getIndexOf($username)
    {
        $index = null;

        // Loop over all existing boekers
        if (is_array($this->boekers)) {
            foreach ($this->boekers as $key => $boeker) {
                if ($boeker->username == $username) {
                    $index = $key;
                }
            }
        }

        return $index;
    }
}
