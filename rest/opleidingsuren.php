<?php
/**
 * Service opleidingsuren | rest/opleidingsuren.php
 *
 * Rest service voor goedkeuren van uren
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
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.5
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var input $input
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Opleidingsuren_obj.php';
include_once '../objects/Uur_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (!$authenticate->authorisation_check(false)) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'code' => 401));
    exit;
}
// We do have a valid user

// We do have a valid user

$input = new input();

switch ($input->get_method()) {
    /**
     * POST method (CREATE)
     *
     * We need to insert a new record
     */
    case 'POST':
        postOpleidingsuren($input);
        break;

        /**
         * GET method (READ)
         *
         * We need to read one or all records
         */
    case 'GET':
        getOpleidingsuren();
        break;

        /**
         * PUT method (UPDATE)
         *
         * We need to update a record
         */
    case 'PUT':
        putOpleidingsuren($input);
        break;

        /**
         * DELETE method (DELETE)
         *
         * We need to delete a record
         */
    case 'DELETE':
        deleteOpleidingsuren($input);
        break;

        /**
         * Other methods are not implemented
         *
         * Just return an error message
         */
    default:
        http_response_code(501);
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
}


/**
 * Function checkRol
 *
 * Checks if the user has the right rol
 *
 * @param string $username
 * @param int	 $rol_id
 *
 * @return bool
 * 
 * @var Goedkeurders $rollen
 */
function checkRol($username, $rol_id)
{
	global $mysqli;
	global $authenticate;

	include_once('../objects/Goedkeurders_obj.php');

	$rollen = new Goedkeurders($mysqli);
	if (in_array($rol_id, $rollen->getRolId($username))) {
		return true;
	} else {
		if (is_array($authenticate->group) && (in_array('admin',$authenticate->group) || (in_array('super', $authenticate->group)))) {
		  return true;
		}
	}
	
	return false;
}

/**
 * Function postOpleidinguren
 *
 * @param input $input
 *
 * @return bool
 * 
 * @var string $json
 * @var Uur $uur_obj
 * @var Opleidingsuren $opleidingsuren_obj
 */
function postOpleidingsuren($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();

	// Check if we have the correct role to insert opleidingsuren
	if (!checkRol($authenticate->username,$json->rol)) {
		http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Onjuiste rol of geen super of admin (146)', 'code' => 403));
        exit;
	}

	$uur_obj = new Uur($json->username, $json->activiteit, $json->rol, "1-1-" . $json->datum, "00:00", "00:00", $json->uren);
    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    // Update record
    try {
        $opleidingsuren_obj->create($uur_obj);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($opleidingsuren_obj);

    return true;
}

/**
 * Function getOpleidingsuren
 *
 * @return bool
 * 
 * @var Opleidingsuren $opleidingsuren_obj
 * @var string $username
 */
function getOpleidingsuren()
{
	global $authenticate;
	global $mysqli;

    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    $username = $authenticate->username;

    if ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super')) {
        $username = null;
    } else {
        $username = $authenticate->username;
    }

    try {
        $opleidingsuren_obj->read($username);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        print_r($e);
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($opleidingsuren_obj);

    return true;
}

/**
 * Function putOpleidingsuren
 *
 * @param Input $input
 *
 * @return bool
 * 
 * @var string $json
 * @var Goedkeurder $goedkeuren_obj
 * @var Uren $uren_obj
 */
function putOpleidingsuren($input)
{
	global $authenticate;
	global $mysqli;

	$json = $input->get_JSON();
	
	// Check if we have the correct role to insert opleidingsuren
	if (!checkRol($authenticate->username,$json->rol)) {
		http_response_code(403);
		echo json_encode(array('success' => false, 'message' => 'Onjuiste rol of geen super of admin (222)', 'code' => 403));
		exit;
	}

    $json = $input->get_JSON();

    $goedkeuren_obj = new Goedkeurder($mysqli);

    // Update record
	//Get uren by user (if not admin or super only show own uren)
    try {
        $goedkeuren_obj->update($json);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($uren_obj);
    return true;
}

/**
 * Function deleteOpleidinguren
 *
 * @param Input $input
 *
 * @return bool
 * 
 * @var Opleidingsuren $opleidingsuren_obj
 */
function deleteOpleidingsuren($input)
{
	global $authenticate;
    global $mysqli;

    // Check if we have the correct role to insert opleidingsuren
    if (!checkRol($authenticate->username,3)) {
    	http_response_code(403);
    	echo json_encode(array('success' => false, 'message' => 'Onjuiste rol of geen super of admin (263)', 'code' => 403));
    	exit;
    }

    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    // Update record
    try {
        $opleidingsuren_obj->delete(array_keys($input->get_pathParams())[0]);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 404));
        exit;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => true,
        'message' => 'Record successfully deleted',
        'code' => 200
    ));

    return true;
}
