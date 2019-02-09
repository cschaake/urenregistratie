<?php
/**
 * Service rapportage | rest/rapportage.php
 *
 * Rest service voor diverse rapportages
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
 * @since      File available since Release 1.0.0
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var input $input
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';
require_once '../includes/settings.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Rapportage_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (!$authenticate->authorisation_check(false)) {
    http_response_code(401);
	header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'code' => 401));
    exit;
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

if ($input->get_method() == 'GET') {
    // Read one or all records
    getRapport($input);
} else {
    http_response_code(501);
	header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
}

/**
 * Function getRapport
 *
 * Read rapport
 *
 * @param 	Input 	$input	Input object containing all input parameters (sanitized)
 *
 * @return 	bool	Successflag
 */
function getRapport(input $input)
{
    global $mysqli;
	global $authenticate;

	if ($input->hasPathParams()) {
		switch(array_keys($input->get_pathParams())[0]) {
			case 'certificaten':
				getCertificaten($authenticate->username);
				break;

			case 'goedtekeuren':
				getGoedtekeuren($authenticate->username);
				break;

			default:
				// Details for user in first param
				if ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super')) {
					getHuidigeUren(array_keys($input->get_pathParams())[0]);
				} else {
					http_response_code(403);
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
					exit;
				}
		}
	} else {
		// Overview of all users for admin and super, or details for current user
		if ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super')) {
			getHuidigeUren();
		} else {
			getHuidigeUren($authenticate->username);
		}
	}
}

/**
 * Function getCertificaten
 *
 * @param 	string $username
 *
 * @return 	bool	Successflag
 * 
 * @var Rapport $rapport_obj
 */
function getCertificaten($username)
{
	global $mysqli;

	$rapport_obj = new Rapport($mysqli);

	try {
		$rapport_obj->certificaten($username);
	} catch(Exception $e) {
		if ($e->getCode() != '404') {
			http_response_code(400);
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
			exit;
		}
	}

    http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($rapport_obj);

	return true;
}

/**
 * Function getGoedtekeuren
 *
 * @param 	string $username
 *
 * @return 	bool	Successflag
 * 
 * @var Rapport $rapport_obj
 */
function getGoedtekeuren($username)
{
	global $mysqli;

	$rapport_obj = new Rapport($mysqli);

	try {
		$rapport_obj->goedtekeuren($username);
	} catch(Exception $e) {
		if ($e->getCode() != '404') {
			http_response_code(400);
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
			exit;
		}
	}

    http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($rapport_obj);

	return true;
}

/**
 * Function getHuidigeUren
 *
 * @param 	string $username
 *
 * @return 	bool	Successflag
 * 
 * @var Rapport $rapport_obj
 */
function getHuidigeUren($username = null)
{
	global $mysqli;

	$rapport_obj = new Rapport($mysqli);

	try {
		$rapport_obj->gebruikersUren($username);
	} catch(Exception $e) {
		if ($e->getCode() != '404') {
			http_response_code(400);
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
			exit;
		}
	}

    http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($rapport_obj);

	return true;
}
