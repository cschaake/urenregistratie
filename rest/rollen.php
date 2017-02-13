<?php
/**
 * Rollen
 *
 * Rest service voor Rollen
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
 * @since      File available since Release 1.0.0
 * @version    1.0.7
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Rollen_obj.php';

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
    $request = json_decode($postdata);
        
	postRol($request);
	
/**
 * GET method (READ)
 *
 * We need to retrieve one or more records
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
	getRollen();


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
        
        putRol($request);
        
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
    
    // Check if we are called for one record or all records
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Delete selected record
        $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);

		deleteRol($request);
        
    } else {
        // No user is specified, this is not allowed
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Bad request', 'code' => 400));
        exit;
    }
}

/**
 * Post rol
 *
 * Insert a new record
 *
 * @param object $request
 *
 * @return bool
 */
function postRol($record)
{
	global $authenticate;
	global $mysqli;
	
	// Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
 	$rol_obj = new Rol(null, $record->rol);
	
	$rollen_obj = new Rollen($mysqli);
		
	try {
		$rollen_obj->create($rol_obj);
	} catch(Exception $e) {
		http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Internal Server Error', 'code' => 500));
        exit;
    }
	
	echo json_encode($rollen_obj->rollen);
	
	return true;
}

/**
 * Get rollen
 *
 * Get all records
 *
 * @return bool
 */
function getRollen()
{
	global $authenticate;
	global $mysqli;

    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        // Get the requested record
        $request = substr(filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING),1);

        // check if we are called for a user, then only admin or super users get data of other users
        if (is_string($request) && ($request != $authenticate->username)) {
            if (!(is_array($authenticate->group) || in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
                http_response_code(403);
                echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
                exit;
            }
        }
        
        $rollen_obj = new Rollen($mysqli);
        try {
            $rollen_obj->read($request);
        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
            exit;
        }
        
    } else {
        // We are called for all records
        
        $rollen_obj = new Rollen($mysqli);
        try {
            $rollen_obj->read();
        } catch(Exception $e) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
            exit;
        }
        
    }
	
	echo json_encode($rollen_obj);
}

/**
 * Delete rol
 *
 * Delete a record
 *
 * @param object $request
 *
 * @return bool
 */
function deleteRol($request)
{
	global $authenticate;
	global $mysqli;
	
	$rollen_obj = new Rollen($mysqli);
	
	try {
		$rollen_obj->delete($request);
	} catch(Exception $e) {
		http_response_code($e->getCode());
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()));
        exit;
	}
	
	echo json_encode(array('success' => true));
	
	return true;
}       

/**
 * Put rol
 *
 * Replace a record
 *
 * @param object $record
 *
 * @return bool
 */
function putRol($record)
{
	global $authenticate;
	global $mysqli;
	
	$rol_obj = new Rol($record->id, $record->rol);
	$rollen_obj = new Rollen($mysqli);
	
	try {
		$rollen_obj->update($rol_obj);
	} catch(Exception $e) {
		http_response_code(500);
		echo json_encode(array('success' => false, 'message' => 'Internal Server Error, ' . $e->getMessage(), 'code' => 500));
		exit;
	}
	
	echo json_encode($rollen_obj->rollen);
	
	return true;
}