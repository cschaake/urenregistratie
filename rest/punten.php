<?php
/**
 * Service punten | rest/punten.php
 *
 * Rest service voor Punten
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
 * @since      File available since Release 1.2.1
 * @version    1.2.1
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

require_once '../objects/Punten_obj.php';

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
        // Read one or all records
    case 'GET':
        getPuntenTotaal($input);
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
 * Function getUsers
 *
 * @param Input $input Input object containing all input parameters (sanitized)
 * @return bool Successflag
 *
 * @var string $username
 * @var Boekers $users
 * @var Users $users_obj
 */
function getPuntenTotaal($input)
{
    /**
     * @var array $result Resultaat van bevragingen
     */
    $result = null;
    
    global $mysqli;
    
    if ($input->get_pathParams()) {
        
        $punten_obj = new Punten($mysqli);
        try {
            $punten_obj->readTotals(array_keys($input->get_pathParams())[0]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 500));
            exit;
        }
        
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Missing input',
            'code' => 400
        ));
        
    }
    $result['punten']['totaal'] = $punten_obj->totaalPunten;
    $result['punten']['gebruikt'] = $punten_obj->puntenGebruikt;
    
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($result);
    
    return true;
}
