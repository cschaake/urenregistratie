<?php

/**
 * Rapportage Object
 *
 * Object voor Rapportages
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
 * @version    1.0.9
 */

/**
 * RaportageGoedTeKeuren object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.8
 * @version 1.0.9
 */
class RaportageGoedTeKeuren
{

    /**
     * Username
     *
     * @var string
     * @access public
     */
    public $username;

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
     * Uren
     *
     * @var float
     * @access public
     */
    public $uren;

    /**
     * Groep_id
     *
     * @var int
     * @access public
     */
    public $groep_id;

    /**
     * Groep
     *
     * @var int
     * @access public
     */
    public $groep;

    /**
     * Totaaluren
     *
     * @var float
     * @access public
     */
    public $totaaluren;

    /**
     * Creeer RaportageGoedTeKeuren object
     *
     * @param string $username
     * @param int $rol_id
     * @param string $rol
     * @param int $uren
     * @param int $groep_id
     * @param string $groep
     * @param float $totaaluren
     *
     * @throws Exception
     *
     * @return bool Succes vlag
     */
    public function __construct($username, $rol_id, $rol, $uren, $groep_id, $groep, $totaaluren)
    {
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->rol_id = (int) filter_var($rol_id, FILTER_SANITIZE_STRING);
        $this->rol = filter_var($rol, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->uren = (float) filter_var($uren, FILTER_SANITIZE_STRING);
        $this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
        $this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->totaaluren = (float) filter_var($totaaluren, FILTER_SANITIZE_STRING);

        return true;
    }
}
