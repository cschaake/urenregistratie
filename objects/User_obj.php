<?php

/**
 * User Object
 *
 * Object voor een User record
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
 * @since      File available since Release 1.0.9
 * @version    1.0.9
 */

/**
 * User object
 *
 * Single user
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.9
 * @version 1.0.9
 *
 */
class User
{

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
     * @var string Voornaam
     * @access public
     */
    public $firstname;

    /**
     * Achternaam
     *
     * @var string Achternaam
     * @access public
     */
    public $lastname;

    /**
     * Groepen
     *
     * @var array Groepen
     * @access public
     */
    public $groepen;

    /**
     * Rollen
     *
     * @var array Rollen
     * @access public
     */
    public $rollen;

    public function __construct($username, $firstname, $lastname, $groepen = null, $rollen = null)
    {
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->firstname = filter_var($firstname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->lastname = filter_var($lastname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        if (is_array($groepen)) {
            foreach ($groepen as $groep) {
                $this->groepen[] = (int) filter_var($groep, FILTER_SANITIZE_STRING);
            }
        }
        if (is_array($rollen)) {
            foreach ($rollen as $rol) {
                $this->rollen[] = (int) filter_var($rol, FILTER_SANITIZE_STRING);
            }
        }

        return true;
    }
}
