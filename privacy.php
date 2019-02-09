<?php
/**
 * Page privacy | privacy.php
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
 * @since      File available since Release 1.0.0
 * @version    1.2.0
 * 
 * @var Authenticate $authenticate
 * @var mysqli $mysqli
 * @var string $javaScript
 * @var string $pagina
 * @var bool $anonymous
 */

/**
 * Required files
 */
require_once 'includes/login_functions.php';
require_once 'includes/db_connect.php';

$authenticate = checkAuthenticate($mysqli);

$javaScript = null;
$pagina = 'includes/privacy_pagina.php';
$anonymous = true;

include_once('includes/pageFramework.php');
