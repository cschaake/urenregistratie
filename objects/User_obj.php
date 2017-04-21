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
     * Email
     *
     * @var string Email
     * @access public
     */
    public $email;

    /**
     * passwordHash
     *
     * @var string passwordHash
     * @access private
     */
    private $passwordHash;

    /**
     * resetToken
     *
     * @var string resetToken
     * @access private
     */
    private $resetToken;

    /**
     * failedLogin
     *
     * @var int failedLogin
     * @access public
     */
    public $failedLogin;

    /**
     * lastLogin
     *
     * @var string lastLogin
     * @access public
     */
    public $lastLogin;

    /**
     * Status
     *
     * @var int $status
     * @access public
     */
    public $status;

    /**
     * created
     *
     * @var string created
     * @access public
     */
    public $created;

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

    /**
     * Groups
     *
     * @var array Groups
     * @access public
     */
    public $groups;

    public function __construct($username, $firstname, $lastname, $groepen = null, $rollen = null, $email = null, $passwordHash = null, $resetToken = null, $failedLogin = null, $lastLogin = null, $status = null, $created = null)
    {
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->firstname = filter_var($firstname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->lastname = filter_var($lastname, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        if (isset($email)) {
            $this->email = filter_var($email, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($passwordHash)) {
            $this->passwordHash = filter_var($passwordHash, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($resetToken)) {
            $this->resetToken = filter_var($resetToken, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($failedLogin)) {
            $this->failedLogin = (int) filter_var($failedLogin, FILTER_SANITIZE_STRING);
        }
        if (isset($lastLogin)) {
            $this->lastLogin = filter_var($lastLogin, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (isset($status)) {
            $this->status = (int) filter_var($status, FILTER_SANITIZE_STRING);
        }
        if (isset($created)) {
            $this->created = filter_var($created, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }
        if (is_array($groepen) && isset($groepen)) {
            foreach ($groepen as $groep) {
                $this->groepen[] = (int) filter_var($groep, FILTER_SANITIZE_STRING);
            }
        }
        if (is_array($rollen) && isset($rollen)) {
            foreach ($rollen as $rol) {
                $this->rollen[] = (int) filter_var($rol, FILTER_SANITIZE_STRING);
            }
        }

        return true;
    }

    /**
     * Add groups
     *
     * @param string[] $groups
     * @return boolean
     */
    public function addGroups($groups)
    {
        foreach ($groups as $group) {
            $this->groups[] = filter_var($group, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        }

        return true;
    }

    /**
     * Add rollen
     *
     * @param Rol $rol
     */
    public function addRollen(Rol $rol)
    {
        $this->rollen[] = $rol;
    }

    /**
     * Add groepen
     *
     * @param Groep $groep
     */
    public function addGroepen(Groep $groep)
    {
        $this->groepen[] = $groep;
    }

    /**
     * Return reset token for user
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }
}
