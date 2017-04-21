<?php

/**
 * Uur Object
 *
 * Object voor een uur record
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
 * @since      File available since Release 1.0.7
 * @version    1.0.9
 */

/**
 * Uur object
 *
 * Single uur
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.7
 * @version 1.0.9
 */
class Uur
{

    /**
     * Id van het uur verplicht numeriek 5 lang
     *
     * @var int Id
     * @access public
     */
    public $id;

    /**
     * Gebruikernaam verplicht alfanumeriek 10 lang
     *
     * @var string Gebruikersnaam
     * @access public
     */
    public $username;

    /**
     * Voornaam
     *
     * @var string Gebruikersnaam
     * @access public
     */
    public $voornaam;

    /**
     * Achternaam
     *
     * @var string Gebruikersnaam
     * @access public
     */
    public $achternaam;

    /**
     * Activiteit id verplicht numeriek 5 lang
     *
     * @var int Activiteit id
     * @access public
     */
    public $activiteit_id;

    /**
     * Activiteit alfanumeriek 30 lang
     *
     * @var string Activiteit naam
     * @access public
     */
    public $activiteit;

    /**
     * Rol ID verplicht numeriek 5 lang
     *
     * @var int Rol id
     * @access public
     */
    public $rol_id;

    /**
     * Rol alfanumeriek 30 lang
     *
     * @var string Rol naam
     * @access public
     */
    public $rol;

    /**
     * Groep id numeriek 5 lang
     *
     * @var int Groep id
     * @access public
     */
    public $groep_id;

    /**
     * Groep alfanumeriek 30 lang
     *
     * @var string Groep naam
     * @access public
     */
    public $groep;

    /**
     * Datum verplicht JSON datum formaat
     *
     * @var string Datum
     * @access public
     */
    public $datum;

    /**
     * Start tijd JSON tijd formaat
     *
     * @var string Start tijd
     * @access public
     */
    public $start;

    /**
     * Eind tijd JSON tijd formaat
     *
     * @var string Eind tijd
     * @access public
     */
    public $eind;

    /**
     * Uren verplicht numeriek 4,2
     *
     * @var float Uren
     * @access public
     */
    public $uren;

    /**
     * Akkoord boolean
     *
     * @var int Akkoord
     * @access public
     */
    public $akkoord;

    /**
     * Reden alfanumeriek 1024 lang
     *
     * @var string Reden
     * @access public
     */
    public $reden;

    /**
     * Opmerking alfanumeriek 1024 lang
     *
     * @var string Opmerking
     * @access public
     */
    public $opmerking;

    /**
     * Flag numeriek 1 lang
     *
     * @var int Flag
     * @access public
     */
    public $flag;

    /**
     * Creeer uren object
     *
     * @param string $username
     * @param int $activiteit_id
     * @param int $rol_id
     * @param string $datum
     * @param string $start
     * @param string $eind
     * @param float $uren
     * @param string $opmerking
     * @param int $akkoord
     * @param string $reden
     * @param bool $flag
     * @param int $id
     * @throws Exception
     * @return bool Succes vlag
     */
    public function __construct($username, $activiteit_id, $rol_id, $datum, $start, $eind, $uren, $opmerking = null, $akkoord = null, $reden = null, $flag = null, $id = null)
    {
        if ($id) {
            $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        } else {
            $this->id = null;
        }
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->activiteit_id = (int) filter_var($activiteit_id, FILTER_SANITIZE_STRING);
        $this->rol_id = (int) filter_var($rol_id, FILTER_SANITIZE_STRING);
        $this->datum = date('Y-m-d', strtotime($datum));
        $this->start = date('H:i', strtotime($start));
        $this->eind = date('H:i', strtotime($eind));
        $this->uren = (float) filter_var($uren, FILTER_SANITIZE_STRING);
        $this->akkoord = (int) filter_var($akkoord, FILTER_SANITIZE_STRING);
        $this->reden = filter_var($reden, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->opmerking = filter_var($opmerking, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        if ($flag) {
            $this->flag = true;
        }

        if ($this->eind < $this->start) {
            throw new Exception('Eindtijd ligt voor starttijd');
        }
        if ($this->akkoord != 2) {
            $this->reden = '';
        }

        return true;
    }

    /**
     * Voeg activiteit toe
     *
     * @param int $activiteit_id
     * @param string $activiteit
     * @throws Exception
     * @return bool Succes vlag
     */
    public function addActiviteit($activiteit_id, $activiteit)
    {
        if ($this->activiteit_id != $activiteit_id) {
            throw new Exception('Geen juiste activiteit');
        }
        $this->activiteit = filter_var($activiteit, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        return true;
    }

    /**
     * Voeg rol toe
     *
     * @param int $rol_id
     * @param string $rol
     * @throws Exception
     * @return bool Succes vlag
     */
    public function addRol($rol_id, $rol)
    {
        if ($this->rol_id != $rol_id) {
            throw new Exception('Geen juiste rol');
        }
        $this->rol = filter_var($rol, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        return true;
    }

    /**
     * Voeg groep toe
     *
     * @param int $groep_id
     * @param string $groep
     * @return bool Succes vlag
     */
    public function addGroep($groep_id, $groep)
    {
        $this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
        $this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        return true;
    }

    /**
     * Voor voor en achternaam toe
     *
     * @param string $firstname
     * @param string $lastname
     * @return bool Succes vlag
     */
    public function addName($firstname, $lastname)
    {
        $this->voornaam = filter_var($firstname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->achternaam = filter_var($lastname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        return true;
    }
}
