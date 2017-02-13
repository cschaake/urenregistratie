<?php
/**
 * Goedkeurders
 *
 * Rest service voor goedkeurders pagina
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
 * @version    1.0.6
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Groepen_obj.php';
include_once '../objects/Rollen_obj.php';
include_once '../objects/Users_obj.php';
include_once '../objects/goedkeurders_obj.php';


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
	
	postGoedkeurder($record);

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
		
		getGoedkeurders();

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
		
		putGoedkeurder($record);

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
		
		deleteGoedkeurder($request); 

	} else {
        // No uren record is specified
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Bad request', 'code' => 400));
        exit;
    }
}

/**
 * Post goedkeurder
 *
 * @param object $record
 *
 * @return bool
 */
function postGoedkeurder($record)
{
	global $authenticate;
	global $mysqli;
	
	// Only admin and super may update records of other users
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
	
    $goedkeurders_obj = new Goedkeurders($mysqli);
	$goedkeurder = new Goedkeurder($record->username, $record->firstname, $record->lastname, $record->groepen, $record->rollen);
	
    try {
        $goedkeurders_obj->create($goedkeurder);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error ' . $e->getMessage(), 'code' => 500));
        exit;
    }

    echo json_encode($goedkeurders_obj->goedkeurders[0]);
	
	return true;
}

/**
 * Get goedkeurders
 *
 * @return bool
 */
function getGoedkeurders()
{
	global $authenticate;
	global $mysqli;
	
	$users_obj = new Users($mysqli);
	$result['users'] = $users_obj->get();
	
	$groepen_obj = new groepen($mysqli);
	$groepen_obj->read();
	$result['groepen'] = $groepen_obj->groepen;
	
	$rollen_obj = new rollen($mysqli);
	$rollen_obj->read();
	$result['rollen'] = $rollen_obj->rollen;
	
	$goedkeurders_obj = new Goedkeurders($mysqli);
	$goedkeurders_obj->read();
	$result['goedkeurders'] = $goedkeurders_obj->goedkeurders;
	
    echo json_encode($result);
	
	return true;
}

/**
 * Put goedkeurder
 *
 * @param object $record
 *
 * @return bool
 */
function putGoedkeurder($record)
{
	global $authenticate;
	global $mysqli;
	
    // Only admin and super may update records of other users
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
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
 * Delete goedkeurder
 *
 * @param object $request
 *
 * @return bool
 */
function deleteGoedkeurder($request)
{
	global $mysqli;
	global $authenticate;
	
	// Only admin and super may update records of other users
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }

    $goedkeurders_obj = new Goedkeurders($mysqli);

    // Update record
    try {
        $goedkeurders_obj->delete($request);
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 404));
        exit;
    }

    echo json_encode(array('success' => true));
		
	return true;
}