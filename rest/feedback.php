<?php
/**
 * Feedback composit rest service
 *
 * Rest service voor feedback.
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
 * @since      File available since Release 1.0.7
 * @version    1.0.9
 */

include_once '../includes/db_connect.php';
include_once '../includes/settings.php';
include_once '../objects/Authenticate_obj.php';
include_once '../objects/Input_obj.php';

include_once '../objects/Feedback_obj.php';

// Start or restart session
include_once '../includes/login_functions.php';
sec_session_start();

$authenticate = new Authenticate($mysqli);

// Check if we are authorized
if (!$authenticate->authorisation_check(false)) {
    http_response_code(401);
	header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Unauthorized', 'code' => 401));
    exit;
}
// We do have a valid user

// Get all input (sanitized)
$input = new Input();

switch ($input->get_method()) {
     // Insert a new record
    case 'POST':
        postFeedback($input);
        break;

    default:
        http_response_code(501);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'Not implemented', 'code' => 501));
}

/**
 * Post Feedback
 *
 * Create a new feedback
 *
 * @param 	input 	$input	Input object containing all input parameters (sanitized)
 *
 * @return 	bool	Successflag
 */
function postFeedback(input $input)
{
    global $mysqli;
	global $authenticate;

	$json = $input->get_JSON();

	if (!isset($json->star)) {
		$json->star = null;
	}

	if (!isset($json->subject)) {
		$json->subject = null;
	}

	if (!isset($json->comment)) {
		$json->comment = null;
	}

	try {
		$feedbackItem_obj = new FeedbackItem($authenticate->username, date("Y-m-d H:i:s"), $json->star, $json->subject, $json->comment);
	} catch(Exception $e) {
		http_response_code(400);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
        exit;
	}

	$feedback_obj = new Feedback($mysqli);

	try {
        $feedback_obj->create($feedbackItem_obj);
    } catch(Exception $e) {
        http_response_code(400);
		header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => $e->getMessage(), 'code' => 400));
        exit;
    }

	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($feedback_obj);

    return true;
}
