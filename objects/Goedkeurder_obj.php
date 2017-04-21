<?php

/**
 * goedkeurder Object
 *
 * Object voor een goedkeurder
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

/**
 * Goedkeurder object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since Class available since Release 1.0.7
 * @version 1.0.9
 */
class Goedkeurder
{

    /**
     * username verplicht alfanumeriek 10 lang
     *
     * @var string
     * @access public
     */
    public $username;

    /**
     * firstname verplicht alfanumeriek 10 lang
     *
     * @var string
     * @access public
     */
    public $firstname;

    /**
     * lastname verplicht alfanumeriek 10 lang
     *
     * @var string
     * @access public
     */
    public $lastname;

    /**
     * array van groep_id's
     *
     * @var array
     * @access public
     */
    public $groepen;

    /**
     * array van rol_id's
     *
     * @var array
     * @access public
     */
    public $rollen;

    /**
     * Creeer activtiteit object
     *
     * @param string $username
     *            Username
     * @param array $groepen
     *            Array van groep_id's
     * @param array $rollen
     *            Array van rol_id's
     *
     * @return bool Succes vlag
     */
    public function __construct($username, $firstname, $lastname, $groepen, $rollen)
    {
        $this->username = $this->groep = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->firstname = $this->groep = filter_var($firstname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->lastname = $this->groep = filter_var($lastname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);

        foreach ($groepen as $groep) {
            $this->groepen[] = (int) filter_var($groep, FILTER_SANITIZE_STRING);
        }

        foreach ($rollen as $rol) {
            $this->rollen[] = (int) filter_var($rol, FILTER_SANITIZE_STRING);
        }

        return true;
    }
}
