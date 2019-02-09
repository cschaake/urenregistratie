<?php

/**
 * Class RaportageCertificaat | objects/RaportageCertificaat
 *
 * Object voor RapportageCertificaat
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
 * @since      File available since Release 1.0.8
 * @version    1.2.0
 */

/**
 * Class RaportageCertificaat
 *
 * @since File available since Release 1.0.8
 * @version 1.2.0
 */
class RaportageCertificaat
{
    /**
     * Id
     *
     * @var int
     * @access public
     */
    public $id;

    /**
     * Username
     *
     * @var string
     * @access public
     */
    public $username;

    /**
     * Voornaam
     *
     * @var string
     * @access public
     */
    public $voornaam;

    /**
     * Achternaam
     *
     * @var string
     * @access public
     */
    public $achternaam;

    /**
     * LaatsteLogin
     *
     * @var datetime
     * @access public
     */
    public $laatstelogin;

    /**
     * Gecertificeerd
     *
     * @var datetime
     * @access public
     */
    public $gecertificeerd;

    /**
     * Verloopt
     *
     * @var datetime
     * @access public
     */
    public $verloopt;

    /**
     * Rol_id
     *
     * @var int
     * @access public
     */
    public $rol_id;

    /**
     * Rol
     *
     * @var string
     * @access public
     */
    public $rol;

    /**
     * Looptijd
     *
     * @var int
     * @access public
     */
    public $looptijd;

    /**
     * Uren
     *
     * @var int
     * @access public
     */
    public $uren;

    /**
     * Ingevoerd
     *
     * @var float
     * @access public
     */
    public $ingevoerd;

    /**
     * Goedgekeurd
     *
     * @var float
     * @access public
     */
    public $goedgekeurd;

    /**
     * Afgekeurd
     *
     * @var float
     * @access public
     */
    public $afgekeurd;

    /**
     * Nodig
     *
     * @var float
     * @access public
     */
    public $nodig;

    /**
     * Method contructor - Creeer uren object
     *
     * @param int $id
     * @param string $username
     * @param string $voornaam
     * @param string $achternaam
     * @param string $laatstelogin
     * @param datetime $gecertificeerd
     * @param datetime $verloopt
     * @param int $rol_id
     * @param string $rol
     * @param int $looptijd
     * @param int $uren
     * @param float $ingevoerd
     * @param float $goedgekeurd
     * @param float $afgekeurd
     *
     * @throws Exception
     *
     * @return bool Succes vlag
     */
    public function __construct($id, $username, $voornaam, $achternaam, $laatstelogin, $gecertificeerd, $verloopt, $rol_id, $rol, $looptijd, $uren, $ingevoerd, $goedgekeurd, $afgekeurd)
    {
        $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->voornaam = filter_var($voornaam, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->achternaam = filter_var($achternaam, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->laatstelogin = date('Y-m-d H:i', strtotime($laatstelogin));
        $this->gecertificeerd = date('Y-m-d', strtotime($gecertificeerd));
        $this->verloopt = date('Y-m-d', strtotime($verloopt));
        $this->rol_id = (int) filter_var($rol_id, FILTER_SANITIZE_STRING);
        $this->rol = filter_var($rol, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->looptijd = (int) filter_var($looptijd, FILTER_SANITIZE_STRING);
        $this->uren = (int) filter_var($uren, FILTER_SANITIZE_STRING);
        $this->ingevoerd = (float) filter_var($ingevoerd, FILTER_SANITIZE_STRING);
        $this->goedgekeurd = (float) filter_var($goedgekeurd, FILTER_SANITIZE_STRING);
        $this->afgekeurd = (float) filter_var($afgekeurd, FILTER_SANITIZE_STRING);

        $this->nodig = $this->uren - $this->goedgekeurd;

        if ($this->nodig < 0) {
            $this->nodig = 0;
        }
        return true;
    }
}
