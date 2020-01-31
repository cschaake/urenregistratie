<?php
/**
 * Script settings | includes/settings.php
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
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.1
 */

// Database connection
define("HOST", "localhost");
define("USER", "urenverantw");
define("PASSWORD", "urenverantw");
define("DATABASE", "urenverantw");

// Security settings
define("SECURE", true);
define("FILTER_CUSTOM", FILTER_FLAG_NO_ENCODE_QUOTES);
define("SESSIONNAME", 'session_id_urenverantw');

// Mail settings
define("SEND_USERCREATE_MAIL", true);
define("SMTP_HOST", "mercury.schaake.nu");
define("SMTP_PORT", 25);
define("SMTP_AUTH", false);


