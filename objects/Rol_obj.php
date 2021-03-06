<?php

/**
 * Rol Object
 *
 * Object voor Rol record
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
 * Rol object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since Class available since Release 1.0.6
 * @version 1.0.9
 */
class Rol
{

    /**
     * id verplicht numeriek lengte 5
     *
     * @var integer
     * @access public
     */
    public $id;

    /**
     * rol verplicht alfanumeriek lengte 30
     *
     * @var string
     * @access public
     */
    public $rol;

    /**
     * Gecertificeerd
     *
     * @var string
     * @access public
     */
    public $gecertificeerd;

    /**
     * Verloopt
     *
     * @var string
     * @access public
     */
    public $verloopt;

    /**
     * Creeer rol object
     *
     * @access public
     * @param int $id
     *            optional Rol id
     * @param string $rol
     *            optional Rol omschrijving
     * @param string $gecertificeerd
     * @param string $verloopt
     *
     * @return bool Succes vlag
     */
    public function __construct($id, $rol = null, $gecertificeerd = null, $verloopt = null)
    {
        if (isset($id)) {
            $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        } else {
            $this->id = null;
        }
        if (isset($rol)) {
            $this->rol = filter_var($rol, FILTER_SANITIZE_STRING);
        } else {
            $this->rol = null;
        }
        if (isset($gecertificeerd)) {
            $this->gecertificeerd = filter_var($gecertificeerd, FILTER_SANITIZE_STRING);
        } else {
            $this->gecertificeerd = null;
        }
        if (isset($verloopt)) {
            $this->verloopt = filter_var($verloopt, FILTER_SANITIZE_STRING);
        } else {
            $this->verloopt = null;
        }

        return true;
    }
}
