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
 * @version    1.2.0
 */

// Database connection
define("HOST", "localhost");
define("USER", "urenverantw");
define("PASSWORD", "urenverantw");
define("DATABASE", "urenverantw");

// Security settings
define("SECURE", true);
define("SESSIONNAME", 'session_id_urenverantw');
define("MINIMUM_USERNAME_LENGTH", 5);
define("MINIMUM_PASSWORD_LENGTH", 8);

// Application name
define("TITLE", "Urenregistratie Reddingsbrigade Apeldoorn");
define("APPLICATION_VERSION", "1.2.0");
define("FILTER_CUSTOM", FILTER_FLAG_NO_ENCODE_QUOTES);

// Mail settings
define("EMAILFROM", "webmaster@schaake.nu");
define("EMAILREPLYTO", "noreply@schaake.nu");
define("SEND_USERCREATE_MAIL", true);
define("SMTP_HOST", "mercury.schaake.nu");
define("SMTP_PORT", 25);
define("SMTP_AUTH", false);

// Groep_id voor opleidings uren, wordt gebruikt voor bepalen groep voor vaste opleidingsuren.
define("OPLEIDINGS_GROEP_ID", 1);
// Activiteit_id waarbij verplicht opmerking invoeren
define("ACTIVITEIT_OPMERKING", 21);
// Toestaan van uren voor en na huidige systeem datum, in PHP formaat. B.v. P3M is 3 maanden.
define("INVOER_VOOR_HUIDIGE_DATUM", "P24M");
define("INVOER_NA_HUIDIGE_DATUM", "P3M");
