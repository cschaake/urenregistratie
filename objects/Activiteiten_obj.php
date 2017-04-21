<?php
/**
 * Activiteiten Object
 *
 * Object voor Activiteiten tabel
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
include_once ('Activiteit_obj.php');

/**
 * Activiteiten object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since Object available since Release 1.0.0
 * @version 1.0.7
 */
class Activiteiten
{

    /**
     * Array met Activiteit objecten
     *
     * @var Activiteit[]
     * @access public
     */
    public $activiteiten;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Creeer activtiteiten object
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
     * Creeer activiteit
     *
     * @access public
     * @param Activiteit $activiteit
     *            Activiteit object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function create(Activiteit $activiteit)
    {
        // Controleer of groep_id bestaat en geef groepnaam terug
        if (! $this->_groepExists($activiteit)) {
            throw new Exception('Niet bestaande groep geselecteerd', 400);
        }

        // Insert de nieuwe activiteit
        $prep_stmt = "
            INSERT INTO
                ura_activiteiten (activiteit, groep_id)
            VALUES
				(?,?)";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('si', $activiteit->activiteit, $activiteit->groep_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows >= 1) {
                $activiteit->id = (int) $stmt->insert_id;
                $this->activiteiten[] = $activiteit;
            } else {
                $stmt->close();
                throw new Exception('Fout bij updaten activiteit', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        return true;
    }

    /**
     * Lees activiteit of activiteiten
     *
     * De functie bevat één parameter met 2 functies:
     * + integer - Selecteer een speficieke activiteit
     * + string - Selecteer alle activiteiten voor een specifieke gebruiker
     *
     * @access public
     * @param int|string $id
     *            optional Integer is activiteit_id, string is username
     * @throws Exception
     * @return bool Succes vlag
     */
    public function read($id = null)
    {
        if (isset($id)) {
            $id = filter_var($id, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        $prep_stmt = "
            SELECT
                distinct (ura_activiteiten.id),
                ura_activiteiten.activiteit,
                ura_activiteiten.groep_id,
                ura_groepen.groep
            FROM
                ura_activiteiten
            JOIN
                ura_groepen ON ura_activiteiten.groep_id = ura_groepen.id";

        if (isset($id) && is_numeric($id)) {
            $prep_stmt .= " WHERE ura_activiteiten.id = ?";
        } elseif (isset($id) && is_string($id)) {
            $prep_stmt .= " JOIN ura_urenboeken ON ura_activiteiten.groep_id = ura_urenboeken.groep_id";
            $prep_stmt .= " WHERE ura_urenboeken.username = ?";
        }

        $prep_stmt .= " ORDER BY ura_groepen.groep, ura_activiteiten.activiteit";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            if (isset($id)) {
                $stmt->bind_param('s', $id);
            }
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $activiteit, $groep_id, $groep);

                while ($stmt->fetch()) {
                    $this->activiteiten[] = new activiteit($id, $activiteit, $groep_id, $groep, (ACTIVITEIT_OPMERKING == $id));
                }
            } elseif ($stmt->num_rows == 0) {
                throw new Exception('Geen activiteit gevonden', 404);
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
     * Update activiteit
     *
     * @access public
     * @param Activiteit $activiteit
     *            Activiteit object
     * @throws Exception
     * @return bool Succes vlag
     */
    public function update(Activiteit $activiteit)
    {
        // Controleer of groep_id bestaat en geef groepnaam terug
        if (! $this->_groepExists($activiteit)) {
            throw new Exception('Niet bestaande groep geselecteerd', 400);
        }

        $prep_stmt = "
            UPDATE ura_activiteiten
            SET
                activiteit = ?,
                groep_id = ?
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('sii', $activiteit->activiteit, $activiteit->groep_id, $activiteit->id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 0) {
                $stmt->close();
                throw new Exception('Activiteit niet gevonden', 404);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        $this->activiteiten[] = $activiteit;
        return true;
    }

    /**
     * Delete activiteit
     *
     * @access public
     * @param int $id
     *            Activiteit id
     * @throws Exception
     * @return bool Succes vlag
     */
    public function delete($id)
    {
        if (isset($id)) {
            $id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        }

        $result = false;
        if (! $this->_canDelete($id)) {
            throw new Exception('Kan activiteit niet verwijderen, nog in gebruik', 409);
        }

        $prep_stmt = "
            DELETE FROM
                ura_activiteiten
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
     * Kan worden gedelete
     *
     * Controleer of activiteit nog in gebruik is
     *
     * @access private
     * @param int $id
     *            Activiteit_id
     * @return bool Succes vlag
     */
    private function _canDelete($id)
    {
        $result = false;
        $prep_stmt = "SELECT COUNT(*) count
						FROM
							ura_uren,
							ura_urenboeken
						WHERE
							ura_uren.activiteit_id = ?
						OR
							ura_urenboeken.activiteit_id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ii', $id, $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 0) {
                $stmt->bind_result($count);
                $stmt->fetch();

                $result = (! $count > 0);
            } else {
                $stmt->close();
                throw new Exception('Interne fout bij verwijderen activiteit', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return $result;
    }

    /**
     * Controleer of groep bestaat
     *
     * @access private
     * @param Activiteit $activiteit
     * @return bool Succes vlag
     */
    private function _groepExists($activiteit)
    {
        $result = false;
        $prep_stmt = "
            SELECT
                groep
            FROM
                ura_groepen
            WHERE
                id = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {

            $stmt->bind_param('i', $activiteit->groep_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($groep);
                $stmt->fetch();
                $activiteit->groep = $groep;
                $result = true;
            }
            $stmt->close();
        } else {
            throw new Exception('Database error');
        }

        return $result;
    }
}
