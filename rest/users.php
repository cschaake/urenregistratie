<?php
/**
 * Service users | rest/users.php
 *
 * Rest service voor Users
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
 * @version    1.2.2
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var input $input
 */

/**
 * Required files
 */
require_once '../includes/db_connect.php';
require_once '../includes/configuration.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Users_obj.php';
require_once '../objects/Boekers_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
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
 * Function postUser
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $json
 * @var User $boeker_obj
 * @var Rol $rol_obj
 * @var Boekers $boekers_obj
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

    $boeker_obj = new User($json->username, $json->firstname, $json->lastname);

    foreach ($json->rollen as $rol) {
        $rol_obj = new Rol($rol->id, $rol->rol, $rol->gecertificeerd, $rol->verloopt);
        $boeker_obj->addRollen($rol_obj);

        if (isset($rol->groep_id)) {
            $groep_obj = new Groep($rol->groep_id);
            $boeker_obj->addGroepen($groep_obj);
        }
    }

    $boekers_obj = new Boekers($mysqli);
    try {
        $boekers_obj->update($boeker_obj);
    } catch (Exception $e) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Not found (#A01)',
            'code' => 404
        ));
        print_r($e);
        exit();
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($boekers_obj);

    return true;
}

/**
 * Function getUsers
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $username
 * @var Boekers $users
 * @var Users $users_obj
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

            $users = new Boekers($mysqli);
            try {
                $users->read($username);
            } catch (Exception $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Boeker niet gevonden',
                    'code' => 404
                ));
                exit();
            }
        } else {
            // We are called for all records

            $users = new Boekers($mysqli);
            try {
                $users->read();
            } catch (Exception $e) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Geen boekers gevonden',
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

        $users = new Users($mysqli);

        try {
            $users->read();
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
 * Function deleteUser
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $username
 * @var Boekers $users_obj
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

        $users_obj = new Boekers($mysqli);
        try {
            $users_obj->delete($username);
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
