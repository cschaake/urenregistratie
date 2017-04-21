<?php

/**
 * FeedbackItem Object
 *
 * Object voor een uur record
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

/**
 * FeedbackItem object
 *
 * Single FeedbackItem
 *
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.8
 * @version 1.0.9
 */
class FeedbackItem
{

    /**
     * Id van het FeedbackItem verplicht numeriek 5 lang
     *
     * @var int Id
     * @access public
     */
    public $id;

    /**
     * Gebruikernaam verplicht alfanumeriek 10 lang
     *
     * @var string Gebruikersnaam
     * @access public
     */
    public $username;

    /**
     * Datetime
     *
     * @var date Datetime
     * @access public
     */
    public $datetime;

    /**
     * Star
     *
     * @var integer Star
     * @access public
     */
    public $star;

    /**
     * Subject
     *
     * @var string Subject
     * @access public
     */
    public $subject;

    /**
     * Comment
     *
     * @var string Comment
     * @access public
     */
    public $comment;

    /**
     * Status
     *
     * @var int Status
     * @access public
     */
    public $status;

    /**
     * Creeer uren object
     *
     * @param string $username
     * @param date $datetime
     * @param int $star
     * @param string $subject
     * @param string $comment
     * @param
     *            int optional $status
     * @param
     *            int optional $id
     *
     * @throws Exception
     *
     * @return bool Succes vlag
     */
    public function __construct($username, $datetime, $star, $subject, $comment, $status = null, $id = null)
    {
        if ($id) {
            $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        } else {
            $this->id = null;
        }
        $this->username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->datetime = date('Y-m-d H:i:s', strtotime($datetime));
        $this->star = (int) filter_var($star, FILTER_SANITIZE_STRING);
        $this->subject = filter_var($subject, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->comment = filter_var($comment, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        if ($status) {
            $this->status = (int) filter_var($status, FILTER_SANITIZE_STRING);
        } else {
            $this->status = null;
        }

        return true;
    }
}
