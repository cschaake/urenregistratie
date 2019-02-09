<?php

/**
 * Class Rapport | objects/Rapportage_obj
 *
 * Object voor Rapportages
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
 * Class Rapport
 *
 * @since File available since Release 1.0.0
 * @version 1.2.0
 */
class Rapport
{

    /**
     * Array met RapportageRecord objecten
     *
     * @var RaportageGoedTeKeuren[] | RaportageCertificaat[] | Uren[]
     * @access public
     */
    public $records;

    /**
     * Array met Rollen objecten
     *
     * @var Rollen[]
     * @access public
     */
    public $rollen;

    /**
     * Array met Activiteiten objecten
     *
     * @var Activiteiten[]
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
     * Method constructor - Create the uren object
     *
     * Creates the uren object that will contain all uren stuff
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
     * Method certificaten - Lees certificaten statistiek
     *
     * @access public
     * @param string $username
     *            optional Username
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var int $id
     * @var string $voornaam
     * @var string $achternaam
     * @var string $laatstelogin
     * @var string $gecertificeerd
     * @var string $verloopt
     * @var int $rol_id
     * @var string $rol
     * @var string $looptijd
     * @var string $uren
     * @var string $ingevoerd
     * @var string $goedgekeurd
     * @var string $afgekeurd
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function certificaten($username = null)
    {
        $prep_stmt = null;
        $stmt = null;
        
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        include_once ('RapportageCertificaat_obj.php');

        $prep_stmt = "
            SELECT
				ura_certificaat.id,
				users.username,
				users.firstName,
				users.lastName,
				users.lastLogin,
				ura_certificaat.gecertificeerd,
				ura_certificaat.verloopt,
				ura_rollen.id,
				ura_rollen.rol,
				ura_certificering.looptijd,
				ura_certificering.uren,
				(
					SELECT SUM(ura_uren.uren)
					FROM ura_uren
					WHERE
						ura_uren.rol_id = ura_certificaat.rol_id
						AND ura_uren.datum BETWEEN ura_certificaat.gecertificeerd AND ura_certificaat.verloopt
						AND ura_uren.username = ura_certificaat.username
				) 'ureningevoerd',
				(
					SELECT SUM(ura_uren.uren)
					FROM ura_uren
					WHERE
						ura_uren.rol_id = ura_certificaat.rol_id
						AND ura_uren.datum BETWEEN ura_certificaat.gecertificeerd AND ura_certificaat.verloopt
						AND ura_uren.username = ura_certificaat.username
						AND ura_uren.akkoord = 1
				) 'urengoedgekeurd',
				(
					SELECT SUM(ura_uren.uren)
					FROM ura_uren
					WHERE
						ura_uren.rol_id = ura_certificaat.rol_id
						AND ura_uren.datum BETWEEN ura_certificaat.gecertificeerd AND ura_certificaat.verloopt
						AND ura_uren.username = ura_certificaat.username
						AND ura_uren.akkoord = 2
				) 'urenafgekeurd'
			FROM
				ura_certificaat
				LEFT JOIN ura_rollen ON ura_certificaat.rol_id = ura_rollen.id
				LEFT JOIN ura_certificering ON ura_certificering.rol_id = ura_certificaat.rol_id
				LEFT JOIN users ON users.username = ura_certificaat.username ";
        if ($username) {
            $prep_stmt .= " WHERE ura_certificaat.username = ? ";
        } else {
            $prep_stmt .= " ORDER BY ura_certificaat.rol_id ";
        }
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {

            if ($username) {
                $stmt->bind_param('s', $username);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $username, $voornaam, $achternaam, $laatstelogin, $gecertificeerd, $verloopt, $rol_id, $rol, $looptijd, $uren, $ingevoerd, $goedgekeurd, $afgekeurd);

                while ($stmt->fetch()) {
                    if ($username) {
                        $this->records[] = new RaportageCertificaat($id, $username, $voornaam, $achternaam, $laatstelogin, $gecertificeerd, $verloopt, $rol_id, $rol, $looptijd, $uren, $ingevoerd, $goedgekeurd, $afgekeurd);
                    }
                }
            } elseif ($stmt->num_rows == 0) {
                $stmt->close();
                throw new Exception('Geen data gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij genereren rapport', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method goedtekeuren - Lees goedtekeuren statistiek
     *
     * @access public
     * @param string $username optional Username
     * @throws Exception
     * @return bool Succes vlag
     * 
     * @var int $groep_id
     * @var string $totaaluren
     * @var string $groep
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    public function goedtekeuren($username)
    {
        $prep_stmt = null;
        $stmt = null;
        
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        include_once ('RapportageGoedTeKeuren_obj.php');

        $prep_stmt = "
            SELECT
                ura_urengoedkeuren.username,
                ura_uren.rol_id,
                ura_rollen.rol,
                SUM(ura_uren.uren),
                ura_groepen.id,
                ura_groepen.groep,
                (SELECT
                        SUM(ura_uren.uren)
                    FROM
                        ura_uren
                    WHERE
                        ura_uren.rol_id = ura_urengoedkeuren.rol_id) 'totaaluren'
            FROM
                ura_uren
                    JOIN
                ura_rollen ON ura_rollen.id = ura_uren.rol_id
                    JOIN
                ura_activiteiten ON ura_activiteiten.id = ura_uren.activiteit_id
                    JOIN
                ura_groepen ON ura_activiteiten.groep_id = ura_groepen.id
                    JOIN
                ura_urengoedkeuren ON ura_urengoedkeuren.rol_id = ura_uren.rol_id
            WHERE
                ura_urengoedkeuren.username = ?
                    AND (ura_uren.akkoord = 0
                    OR ura_uren.akkoord = NULL)
            GROUP BY ura_rollen.id";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $rol_id, $rol, $uren, $groep_id, $groep, $totaaluren);

                while ($stmt->fetch()) {
                    $this->records[] = new RaportageGoedTeKeuren($username, $rol_id, $rol, $uren, $groep_id, $groep, $totaaluren);
                }
            } elseif ($stmt->num_rows == 0) {
                $stmt->close();
                throw new Exception('Geen data gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij genereren rapport', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }

        return true;
    }

    /**
     * Method gebruikersUren - Lees gebruikers uren
     *
     * @access public
     * @param string $username optional Username
     * @throws Exception
     * @return bool Succes vlag
     */
    public function gebruikersUren($username = null)
    {
        if (isset($username)) {
            $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

            // Overzicht van de gebruiker, gegroepeerd op certificaat en alleen in huidige certificaat periode Uur objecten
            include_once ('Uren_obj.php');

            $uren_obj = new Uren($this->mysqli);

            try {
                $uren_obj->read($username, null, time());
            } catch (Exception $e) {
                if ($e->getCode() != '404') {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'message' => $e->getMessage(),
                        'code' => 400
                    ));
                    exit();
                }
            }

            $this->records = $uren_obj->uren;
        } else {
            // Overzicht van alle gebruikers, gegroeperd op certificaat en alleen in huidige certifcaat periode RapportageCertificaat objecten
            $this->certificaten();

            // Get rollen
            include_once ('Rollen_obj.php');

            $rollen_obj = new Rollen($this->mysqli);

            try {
                $rollen_obj->read();
            } catch (Exception $e) {
                if ($e->getCode() != '404') {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'message' => $e->getMessage(),
                        'code' => 400
                    ));
                    exit();
                }
            }

            $this->rollen = $rollen_obj->rollen;

            // Get activiteiten
            include_once ('Activiteiten_obj.php');

            $activiteiten_obj = new Activiteiten($this->mysqli);

            try {
                $activiteiten_obj->read();
            } catch (Exception $e) {
                if ($e->getCode() != '404') {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'message' => $e->getMessage(),
                        'code' => 400
                    ));
                    exit();
                }
            }

            $this->activiteiten = $activiteiten_obj->activiteiten;
        }
    }
}
