<?php
/**
 * Certificaten
 *
 * Rest service voor Certificaten
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
 * @since      File available since Release 1.0.0
 * @version       1.0.6
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
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


/**
 * POST method (CREATE)
 *
 * We need to insert a new record
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
	
    // Get the post info from the json call
    $postdata = file_get_contents('php://input');
    $request = json_decode($postdata);

	postCertificaat($request);
    
/**
 * GET method (READ)
 *
 * We need to retrieve one or more records
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Everyone may execute this method
    
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Get the requested record
        $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);
        
        http_response_code(501);
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
    } else {
        // We are called for all records
        
		getCertificaten();
	
    }


/**
 * PUT method (UPDATE)
 *
 * We need to updata / replace an existing record
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
        
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Get the post info from the json call
        $postdata = file_get_contents('php://input');
        $request = json_decode($postdata);
        
        // Update record
        
        putCertificaat($request);
        
    } else {
        // No user is specified, this is not allowed
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
    // Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
    
	// Delete selected record
    $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);
	
    deleteCertificaten($request);
}

/**
 * Post certificaat
 *
 * @param object $record
 *
 * @return bool
 */
function postCertificaat($record)
{
    // Insert a new record
    
    global $authenticate;
	global $mysqli;
 	
	$certificaten_obj = new Certificaten($mysqli);
		
	try {
		$certificaten_obj->create($record);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }
	
	echo json_encode($certificaten_obj->certificaten);
	
	return true;
	
}

/**
 * Get certificaten
 *
 * @param object $request
 *
 * @return bool
 */
function getCertificaten()
{
	global $mysqli;
	
    $certificaten_obj = new Certificaten($mysqli);
    try {
        $certificaten_obj->read();
    } catch(Exception $e) {
        http_response_code(404);
        echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
        exit;
    }
    echo json_encode($certificaten_obj);
		
	return true;
}

/**
 * Put certificaat
 *
 * Replace a record
 *
 * @param object $request
 *
 * @return bool
 */
function putCertificaat($request)
{
	global $authenticate;
	global $mysqli;
	
	$certificaten_obj = new Certificaten($mysqli);
	$certificaat = new Certificaat($request->id, $request->rol_id, $request->rol, $request->looptijd, $request->uren, 
		$request->groep_id, $request->groep);
	
	try {
		$certificaten_obj->update($certificaat);
	} catch(Exception $e) {
		http_response_code(500);
		echo json_encode(array('success' => false, 'message' => 'Internal Server Error, ' . $e->getMessage(), 'code' => 500));
		exit;
	}
	
	echo json_encode($certificaten_obj->certificaten);
	
	return true;
}

/**
 * Delete certificaat
 *
 * Delete a record
 *
 * @param object $request
 *
 * @return bool
 */
function deleteCertificaten($request)
{
	global $authenticate;
	global $mysqli;
	
	$certificaten_obj = new Certificaten($mysqli);
	
	try {
		$certificaten_obj->delete($request);
	} catch(Exception $e) {
		http_response_code($e->getCode());
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()));
        exit;
	}
	
	echo json_encode(array('success' => true));
	
	return true;
}      