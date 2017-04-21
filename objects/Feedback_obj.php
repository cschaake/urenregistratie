<?php
/**
 * Feedback Object
 *
 * Object voor Feedback tabel
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
 * @since      File available since Release 1.0.8
 * @version    1.0.9
 */
require_once ('FeedbackItem_obj.php');

/**
 * Feedback object
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since Class available since Release 1.0.8
 * @version 1.0.9
 */
class Feedback
{

    /**
     * Array met FeedbackItem objecten
     *
     * @var FeedbackItem[]
     * @access public
     */
    public $FeedbackItem;

    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Creeer feedback object
     *
     * @access public
     * @param mysqli $mysqli
     * @throws Exception
     * @return bool Success flag
     */
    public function __construct(mysqli $mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object', 500);
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Creeer feedback
     *
     * @access public
     * @param FeedbackItem $feedbackItem_obj
     * @throws Exception
     * @return bool Succes vlag
     */
    public function create(FeedbackItem $feedbackItem_obj)
    {
        $prep_stmt = "
            INSERT
				feedback
            SET
                username = ?,
				date = ?,
                star = ?,
                subject = ?,
                comment = ?";

        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ssiss', $feedbackItem_obj->username, $feedbackItem_obj->datetime, $feedbackItem_obj->star, $feedbackItem_obj->subject, $feedbackItem_obj->comment);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->affected_rows < 1) {
                $stmt->close();
                throw new Exception('Fout bij toevoegen feedback', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error');
        }

        return true;
    }
}
