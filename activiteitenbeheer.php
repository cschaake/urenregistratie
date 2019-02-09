<?php
/**
 * Page activiteitenbeheer | activiteitenbeheer.php
 *
 * Skeleton for urenregistratie application
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
 * @package    Urenregistratie
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.0
 * @version    1.2.0
 *
 * @var Authenticate $authenticate Bevat het authenticatie object uit includes/login_functions.
 * @var mysqli $mysqli Represents a connection between PHP and a MySQL database.
 * @var string $javaScript Bevat te laden javaScript behorende bij pagina, wordt geset in aanroepen script.
 * @var string $pagina Bevat te laden pagina, wordt geset in aanroepen script.
 */

/**
 * Required files
 */
require_once 'includes/login_functions.php';
require_once 'includes/db_connect.php';

$authenticate = checkAuthenticate($mysqli);

$javaScript = 'scripts/activiteitenbeheer.js';
$pagina = 'includes/activeitenbeheer_pagina.php';

include_once ('includes/pageFramework.php');
