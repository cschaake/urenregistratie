<?php
/**
 * Users
 *
 * Rest service voor Users
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
 * @version       1.0.4
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Users_obj.php';

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
        
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        
        $path = $id = filter_var(substr($_SERVER['PATH_INFO'],1), FILTER_SANITIZE_STRING);
    
        if ($path == 'addUrenboeken') {
            
            $boeker['username'] = $request->username;
            
            foreach($request->rollen as $rol) {
                
                $record['rol_id'] = $rol->rol_id;
                $record['gecertificeerd'] = $rol->gecertificeerd;
                $record['verloopt'] = $rol->verloopt;
				if (isset($rol->groep_id)) {
					$record['groep_id'] = $rol->groep_id;
				} else {
					$record['groep_id'] = null;
				}
                $boeker['rol'][] = $record;
                
            }
            
            $response['username'] = $request->username;
            $response['firstname'] = $request->firstname;
            $response['lastname'] = $request->lastname;
    
            $users_obj = new Users($mysqli);
            try {
                $user = $users_obj->setBoeker($boeker);
            } catch(Exception $e) {
                http_response_code(404);
                echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
                print_r($e);
                exit;
            }
            
            echo json_encode($response);
            exit;
            
        } else {
            http_response_code(501);
            echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
            exit;
        }
    }
    
    // Insert a new record
    // @TODO
    http_response_code(501);
    echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
    exit;
    
/**
 * GET method (READ)
 *
 * We need to retrieve one or more records
 */
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    // Only admin or super may execute this method
    if ((!is_array($authenticate->group)) || !(in_array('admin',$authenticate->group) || in_array('super',$authenticate->group))) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Forbidden', 'code' => 403));
        exit;
    }
    
    if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        
        $path = $id = filter_var(substr($_SERVER['PATH_INFO'],1), FILTER_SANITIZE_STRING);
        
        // Get the action name (first parameter)
        $action = substr($path,0,strpos($path,'/',2));
        
        // Get the username (second parameter)
        $username = substr($path,strpos($path,'/',2) + 1);
        
        if ($action == 'boekers') {
            if ($username != '') {

                // We are called for a single record
            
                $users_obj = new Users($mysqli);
                try {
                    $users = $users_obj->getBoeker($username);
                } catch(Exception $e) {
                    http_response_code(404);
                    echo json_encode(array('success' => false, 'message' => 'Not found row 145', 'code' => 404));
                    exit;
                }
                echo json_encode($users);
        
            } else {
                // We are called for all records
            
                $users_obj = new Users($mysqli);
                try {
                    $users = $users_obj->getBoekers();
                } catch(Exception $e) {
                    http_response_code(404);
                    echo json_encode(array('success' => false, 'message' => 'Not found row 158', 'code' => 404));
                    exit;
                }
                echo json_encode($users);
            }
        } elseif ($action == 'goedkeurders') {
            if ($username != '') {

                // Get the requested record
                http_response_code(501);
                echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
                exit;

            } else {
                // We are called for all records
            
                $users_obj = new Users($mysqli);
                try {
                    $users = $users_obj->getGoedkeurders();
                } catch(Exception $e) {
                    http_response_code(404);
                    echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
                    exit;
                }
                echo json_encode($users);
            }
        } else {
            // Get the requested record
            http_response_code(501);
            echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
            exit;
        }

        
    } else {
        $users_obj = new Users($mysqli);
            try {
                $users = $users_obj->get();
            } catch(Exception $e) {
                http_response_code(404);
                echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
                exit;
            }
            echo json_encode($users);
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
        // @TODO
        http_response_code(501);
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
        exit;
        
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
        
        $path = $id = filter_var(substr($_SERVER['PATH_INFO'],1), FILTER_SANITIZE_STRING);
        
        // Get the action name (first parameter)
        $action = substr($path,0,strpos($path,'/',2));
        
        // Get the username (second parameter)
        $username = substr($path,strpos($path,'/',2) + 1);
        
        if (($action == 'boeker') && ($username)) {
            
            $users_obj = new Users($mysqli);
            try {
                $users = $users_obj->deleteBoeker($username);
            } catch(Exception $e) {
                http_response_code(404);
                echo json_encode(array('success' => false, 'message' => 'Not found', 'code' => 404));
                exit;
            }
            
            http_response_code(200);
            echo json_encode(array('success' => true, 'code' => 200));
            exit;
            
        } else {
            
            http_response_code(501);
            echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
            exit;
        }
        
    } else {
        // No user is specified, this is not allowed
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Bad request', 'code' => 400));
        exit;
    }
}