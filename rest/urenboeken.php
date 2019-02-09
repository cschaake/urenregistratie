<?php
/**
 * Service urenboeken | rest/urenboeken.php
 *
 * Rest service voor de uren boeken (registeren) pagina.
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
 * @since      File available since Release 1.0.7
 * @version    1.2.0
 * 
 * @var mysqli $mysqli
 * @var Authenticate $authenticate
 * @var input $input
 */

/**
 * Requred files
 */
require_once '../includes/db_connect.php';
require_once '../includes/settings.php';
require_once '../objects/Authenticate_obj.php';
require_once '../objects/Input_obj.php';

require_once '../objects/Activiteiten_obj.php';
require_once '../objects/Rollen_obj.php';
require_once '../objects/Uren_obj.php';

// Start or restart session
require_once '../includes/login_functions.php';
sec_session_start();
global $mysqli;

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
        postUrenboeken($input);
        break;

    // Read one or all records
    case 'GET':
        getUrenboeken($input);
        break;

    // Update an existing record
    case 'PUT':
        putUrenboeken($input);
        break;

    // delete an existing record
    case 'DELETE':
        deleteUrenboeken($input);
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
 * Function postUrenboeken
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $json
 * @var Uur $uur_obj
 * @var Uren $uren_obj
 */
function postUrenboeken(input $input)
{
    global $mysqli;
    global $authenticate;

    $json = $input->get_JSON();

    // Only admin and super may update records of other users
    if (! ($authenticate->checkUsername($json->username) || $authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Forbidden',
            'code' => 403
        ));
        exit();
    }

    if (! isset($json->reden)) {
        $json->reden = null;
    }

    if (! isset($json->opmerking)) {
        $json->opmerking = null;
    }

    try {
        $uur_obj = new Uur($json->username, $json->activiteit_id, $json->rol_id, $json->datum, $json->start, $json->eind, $json->uren, $json->opmerking, $json->akkoord, $json->reden);
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 400
        ));
        exit();
    }

    $uren_obj = new Uren($mysqli);

    try {
        $uren_obj->create($uur_obj);
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 400
        ));
        exit();
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($uren_obj);

    return true;
}

/**
 * Function getUrenboeken
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $username
 * @var int $id
 * @var Uren $uren_obj
 * @var Activiteiten $activiteiten_obj
 * @var Rollen $rollen_obj
 * @var array $result
 *      @param Activiteit[] "activiteiten"
 *      @param Rol[] "rollen"
 *      @param Uur[] "uren"
 */
function getUrenboeken(input $input)
{
    global $mysqli;
    global $authenticate;
    $result = null;
    /*
     * Check how we are called.
     * 1. /urenboeken.php -> gives all uren of current user
     * 2. /urenboeken.php/1 -> gives uur id 1 (checks current user rights)
     * 3. /urenboeken.php/user -> gives all uren for user (other than currentuser) (admin and super only)
     * 4. /urenboeken.php/all -> gives all uren for all users (admin and super only)
     */

    if ($input->hasPathParams() && (is_int(array_keys($input->get_pathParams())[0]))) {
        // Call method 2
        // Only admin and super may update records of other users
        if (! $authenticate->checkGroup('admin') || ! $authenticate->checkGroup('super')) {
            $username = $authenticate->username;
        } else {
            $username = null;
        }

        $id = array_keys($input->get_pathParams())[0];
    } elseif ($input->hasPathParams() && (array_keys($input->get_pathParams())[0] == 'all')) {
        // Call method 4
        // Only admin and super may update records of other users
        if (! $authenticate->checkGroup('admin') || ! $authenticate->checkGroup('super')) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Forbidden',
                'code' => 403
            ));
            exit();
        }

        $username = null;
        $id = null;
    } elseif ($input->hasPathParams()) {
        // Call method 3
        if (! ($authenticate->checkUsername(array_keys($input->get_pathParams())[0]) || $authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Forbidden',
                'code' => 403
            ));
            exit();
        }
        $username = array_keys($input->get_pathParams())[0];
        $id = null;
    } else {
        // Call method 1
        $username = $authenticate->username;
        $id = null;
    }

    $uren_obj = new Uren($mysqli);
    try {
        $uren_obj->read($username, $id);
    } catch (Exception $e) {
        if ($e->getCode() != '404') {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ));
            exit();
        }
    }

    $result['uren'] = $uren_obj->uren;

    if (! $id) {
        $activiteiten_obj = new Activiteiten($mysqli);
        try {
            $activiteiten_obj->read();
        } catch (Exception $e) {
            if ($e->getCode() != '404') {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 400
                ));
                exit();
            }
        }

        $result['activiteiten'] = $activiteiten_obj->activiteiten;

        $rollen_obj = new Rollen($mysqli);
        try {
            $rollen_obj->read($username);
        } catch (Exception $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ));
            exit();
        }

        $result['rollen'] = $rollen_obj->rollen;
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($result);

    return true;
}

/**
 * Function putUrenboeken
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $json
 * @var Uur $uur_obj
 * @var Uren $uren_obj
 */
function putUrenboeken(input $input)
{
    global $mysqli;
    global $authenticate;

    $json = $input->get_JSON();

    // Only admin and super may update records of other users
    if (! ($authenticate->checkUsername($json->username) || $authenticate->checkGroup('admin') || $authenticate->checkGroup('super'))) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Forbidden',
            'code' => 403
        ));
        exit();
    }

    if (! isset($json->reden)) {
        $json->reden = null;
    }

    if (! isset($json->opmerking)) {
        $json->opmerking = null;
    }

    try {
        $uur_obj = new Uur($json->username, $json->activiteit_id, $json->rol_id, $json->datum, $json->start, $json->eind, $json->uren, $json->opmerking, $json->akkoord, $json->reden, null, $json->id);
    } catch (Exception $e) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 400
        ));
        exit();
    }

    $uren_obj = new Uren($mysqli);

    try {
        $uren_obj->update($uur_obj);
    } catch (Exception $e) {
        if ($e->getCode() == 404) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ));
            exit();
        }
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 500
        ));
        exit();
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($uren_obj);

    return true;
}

/**
 * Function detelUrenboeken
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 * 
 * @var string $username
 * @var Uren $uren_obj
 */
function deleteUrenboeken(input $input)
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
    if (! $authenticate->checkGroup('admin') || ! $authenticate->checkGroup('super')) {
        $username = $authenticate->username;
    } else {
        $username = null;
    }

    $uren_obj = new Uren($mysqli);

    try {
        $uren_obj->delete(array_keys($input->get_pathParams())[0], $username);
    } catch (Exception $e) {
        if ($e->getCode() == 404) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ));
            exit();
        }
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => 500
        ));
        exit();
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
