<?php
/**
 * Configuratie
 *
 * Rest service voor Configuratie
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
 * @since      File available since Release 1.0.6
 * @version       1.0.6
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Groepen_obj.php';
include_once '../objects/Rollen_obj.php';
include_once '../objects/Activiteiten_obj.php';
include_once '../objects/Certificaten_obj.php';

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


// Only admin or super may execute this serivce
if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
    http_response_code(403);
    echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
    exit;
}

/**
 * POST method (CREATE)
 *
 * We need to insert a new record
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
    // Get the post info from the json call
    $postdata = file_get_contents('php://input');
    $record = json_decode($postdata);

	postConfiguratie($record);
    
/**
 * GET method (READ)
 *
 * We need to retrieve one or more records
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // We are called for all records
    getConfiguratie();

/**
 * PUT method (UPDATE)
 *
 * We need to updata / replace an existing record
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the requested record
    $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);
    // Get the post info from the json call
    $postdata = file_get_contents('php://input');
    $record = json_decode($postdata);

	putConfiguratie($request, $record);
    
/**
 * DELETE method (DELETE)
 *
 * We need to delete an existing record
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Get the requested record
    $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);

    deleteConfiguratie($request);
}

/**
 * Post configuratie
 *
 * Insert a new record
 *
 * @param object $request
 *
 * @return bool
 */
function postConfiguratie($record)
{
	echo $record;
	global $mysqli;
	
	return true;
}

/**
 * Get configuratie
 *
 * Get all configuratie records
 *
 * @param object $request
 *
 * @return bool
 */
function getConfiguratie()
{
	global $mysqli;
	
    $groepen_obj = new groepen($mysqli);
    try {
        $groepen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Groepen not found', 'code' => 404));
        exit;
    }
	
	$rollen_obj = new rollen($mysqli);
    try {
        $rollen_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Rollen not found', 'code' => 404));
        exit;
    }
	
	$activiteiten_obj = new Activiteiten($mysqli);
    try {
        $activiteiten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Activiteiten not found', 'code' => 404));
        exit;
    }
	
	$certificaten_obj = new certificaten($mysqli);
    try {
        $certificaten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Certificaten not found', 'code' => 404));
        exit;
    }
	
	$result['groepen'] = $groepen_obj->groepen;
	$result['rollen'] = $rollen_obj->rollen;
	$result['activiteiten'] = $activiteiten_obj->activiteiten;
	$result['certificaten'] = $certificaten_obj->certificaten;
	
    echo json_encode($result);
		
	return true;
}

/**
 * Put configuratie
 *
 * Update an existing record
 *
 * @param object $request
 * @param object $record
 *
 * @return bool
 */
function putConfiguratie($request, $record)
{
	global $mysqli;
	echo $request;
	echo $record;
	
	return true;
}

/**
 * Delete configuratie
 *
 * Delete an existiging record
 *
 * @param object $request
 *
 * @return bool
 */
function deleteConfiguratie($request)
{
	global $mysqli;
	echo $request;
	return true;
}
