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
 * @version    1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Users_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (! $authenticate->authorisation_check(false)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'Unauthorized',
        'code' => 401
    ));
    exit();
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

switch ($input->get_method()) {
    // Insert a new record
    case 'POST':
        postUser($input);
        break;

    // Read one or all records
    case 'GET':
        getUsers($input);
        break;

    // delete an existing record
    case 'DELETE':
        deleteUser($input);
        break;

    default:
        http_response_code(501);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not implemented',
            'code' => 501
        ));
}

/**
 * Post User
 *
 * @param input $input
 *            Input object containing all input parameters (sanitized)
 * @return bool Successflag
 */
function postUser($input)
{
    global $mysqli;
    global $authenticate;

    $json = $input->get_JSON();

    // Only admin and super may update records
    if (! ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Forbidden',
            'code' => 403
        ));
        exit();
    }

    $boeker['username'] = $json->username;

    foreach ($json->rollen as $rol) {

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

    $response['username'] = $json->username;
    $response['firstname'] = $json->firstname;
    $response['lastname'] = $json->lastname;

    $users_obj = new Users($mysqli);
    try {
        $users_obj->setBoeker($boeker);
    } catch (Exception $e) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not found',
            'code' => 404
        ));
        print_r($e);
        exit();
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($response);

    return true;
}

/**
 * Get User
 *
 * @param input $input
 *            Input object containing all input parameters (sanitized)
 * @return bool Successflag
 */
function getUsers($input)
{
    global $mysqli;
    global $authenticate;

    // Only admin and super may update records
    if (! ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Forbidden',
            'code' => 403
        ));
        exit();
    }

    $username = '';
    if (is_array($input->get_pathParams())) {
        $username = array_keys($input->get_pathParams())[1];
    }

    if ($input->hasPathParams() && (array_keys($input->get_pathParams())[0] == 'boekers')) {

        if ($username != '') {

            // We are called for a single record

            $users_obj = new Users($mysqli);
            try {
                $users = $users_obj->getBoeker($username);
            } catch (Exception $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Not found row 145',
                    'code' => 404
                ));
                exit();
            }
        } else {
            // We are called for all records

            $users_obj = new Users($mysqli);
            try {
                $users = $users_obj->getBoekers();
            } catch (Exception $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Not found row 158',
                    'code' => 404
                ));
                exit();
            }
        }
    } elseif ($input->hasPathParams() && (array_keys($input->get_pathParams())[0] == 'goedkeurders')) {
        if ($username != '') {

            // Get the requested record
            http_response_code(501);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Not implemented',
                'code' => 501
            ));
            exit();
        } else {
            // We are called for all records

            $users_obj = new Users($mysqli);
            try {
                $users = $users_obj->getGoedkeurders();
            } catch (Exception $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Not found',
                    'code' => 404
                ));
                exit();
            }
        }
    } else {

        $users_obj = new Users($mysqli);

        try {
            $users = $users_obj->get();
        } catch (Exception $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Not found',
                'code' => 404
            ));
            exit();
        }
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($users);

    return true;
}

/**
 * Delete User
 *
 * @param input $input
 *            Input object containing all input parameters (sanitized)
 * @return bool Successflag
 */
function deleteUser($input)
{
    global $mysqli;
    global $authenticate;

    // No record to delete was provided
    if (! $input->get_pathParams()) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Bad Request',
            'code' => 400
        ));
        exit();
    }

    // Only admin and super may update records of other users
    if (! ($authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Forbidden',
            'code' => 403
        ));
        exit();
    }

    $username = '';
    if (is_array($input->get_pathParams())) {
        $username = array_keys($input->get_pathParams())[1];
    }

    if ($input->hasPathParams() && (array_keys($input->get_pathParams())[0] == 'boeker') && $username != '') {

        $users_obj = new Users($mysqli);
        try {
            $users_obj->deleteBoeker($username);
        } catch (Exception $e) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Not found',
                'code' => 404
            ));
            exit();
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'message' => 'User successfully deleted',
            'code' => 200
        ));
        exit();
    } else {

        http_response_code(501);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not implemented',
            'code' => 501
        ));
        exit();
    }
}
