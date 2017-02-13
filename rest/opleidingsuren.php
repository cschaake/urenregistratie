<?php
/**
 * Goedkeuren
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
 * @copyright  2015 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.5
 * @version       1.0.6
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/opleidingsuren_obj.php';


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

/**
 * POST method (CREATE)
 *
 * We need to insert a new record
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the post info from the json call
    $postdata = file_get_contents('php://input');
    $record = json_decode($postdata);
	
	postOpleidingsuren($record);

/**
 * GET method (READ)
 *
 * We need to retrieve one or more records
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Everyone may execute this method

    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        http_response_code(501);
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
        exit;

    } else {
        // We are called for all records
		
		getOpleidingsuren();

	}

/**
 * PUT method (UPDATE)
 *
 * We need to updata / replace an existing record
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Get the requested record
        $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);
        // Get the post info from the json call
        $postdata = file_get_contents('php://input');
        $record = json_decode($postdata);
		
		putOpleidingsuren($record);

    } else {
        // No uren record is specified
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Bad request', 'code' => 400));
        exit;
    }


/**
 * DELETE method (DELETE)
 *
 * We need to delete an existing record
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Get the requested record
        $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);
		
		deleteOpleidingsuren($request); 

	} else {
        // No uren record is specified
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Bad request', 'code' => 400));
        exit;
    }
}

/**
 * Checks the users rol
 *
 * Checks if the user has the right rol
 *
 * @param string $username
 * @param int	 $rol_id
 *
 * @return bool
 */
function checkRol($username, $rol_id)
{
	global $mysqli;
	
	include_once('../objects/goedkeurders_obj.php');
	
	$rollen = new Goedkeurders($mysqli);
	return (in_array($rol_id, $rollen->getRolId($username)));
}

/**
 * Post opleidingsuren
 *
 * @param object $record
 *
 * @return bool
 */
function postOpleidingsuren($record)
{
	global $authenticate;
	global $mysqli;
	
	// Check if we have the correct role to insert opleidingsuren
	if (!checkRol($authenticate->username,$record->rol)) {
		http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
	}
	
    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    // Update record
    try {
        $opleidingsuren = $opleidingsuren_obj->insert($record);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }

    echo json_encode($opleidingsuren);
	
	return true;
}

/**
 * Get opleidingsuren
 *
 * @return bool
 */
function getOpleidingsuren()
{
	global $authenticate;
	global $mysqli;
	
    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    $username = $authenticate->username;

    if ((is_array($authenticate->group)) && (in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        $username = null;
    }

    try {
        $opleidingsuren = $opleidingsuren_obj->get($username);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        print_r($e);
        exit;
    }
    echo json_encode($opleidingsuren);
	return true;
}

/**
 * Put opleidingsuren
 *
 * @param object $record
 *
 * @return bool
 */
function putOpleidingsuren($record)
{
	global $authenticate;
	global $mysqli;
	
    // Only admin and super may update records of other users
    if (($authenticate->username != $record->username) && 
	((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group)))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }

    $goedkeuren_obj = new goedkeuren($mysqli);

    // Update record
	//Get uren by user (if not admin or super only show own uren)
    try { 
        $goedkeuren_obj->update($record);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        exit;
    }

    echo json_encode($record);
		
	return true;
}

/**
 * Delete opleidingsuren
 *
 * @param object $request
 *
 * @return bool
 */
function deleteOpleidingsuren($request)
{
	global $mysqli;

    $opleidingsuren_obj = new Opleidingsuren($mysqli);

    // Update record
    try {
        $opleidingsuren_obj->delete($request);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 404));
        exit;
    }

    echo json_encode(array('success' => true));
		
	return true;
}