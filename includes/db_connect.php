<?php
/**
 * Script db_connect | includes/db_connect.php
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
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 */

/**
 * Required files
 */
require_once 'settings.php';

// Enable MySQL error throwing
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
	
	// Catch any other error
	if ($mysqli->connect_errno) {
		throw new mysqli_sql_exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}	
} catch (mysqli_sql_exception $e) {
	include_once('includes/maintenance_pagina.php');
	exit;
}
