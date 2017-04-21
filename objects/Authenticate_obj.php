<?php

/**
 * Authentication object
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
 * @copyright  2015 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version	   1.0.9
 */

/**
 * Authenticate object
 *
 * @package authenticate
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2015 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 *
 * @since File available since Release 1.0.0
 * @version 1.0.9
 */
class Authenticate
{

    /**
     * Username required with length of 10 characters
     *
     * @var string
     * @access public
     */
    public $username;

    /**
     * Firstname of user (30 characters)
     *
     * @var string
     * @access public
     */
    public $firstName;

    /**
     * Lastname of user (30 characters)
     *
     * @var string
     * @access public
     */
    public $lastName;

    /**
     * Email address of user (256 characters)
     *
     * @var string
     * @access public
     */
    public $email;

    /**
     * Hash of the users password as stored in the database (128 characters)
     *
     * @var string
     * @access public
     */
    public $passwordHash;

    /**
     * Date and time the was created (datetime)
     *
     * @var datetime
     * @access public
     */
    public $status;

    /**
     * Status of the user (0 is initial; 1 is confirmed; 2 is locked; 3 is deleted)
     *
     * @var number
     * @access public
     */
    public $created;

    /**
     * Hash of the users session (128 characters)
     *
     * @var string
     * @access public
     */
    public $sessionHash;

    /**
     * Reset token send by email at users request, user will return valid token (128 characters)
     *
     * @var string
     * @access public
     */
    public $resetToken;

    /**
     * Number of failed logins after the initial creation or last succesfull login (1 number)
     *
     * @var number
     * @access public
     */
    public $failedLogin;

    /**
     * Last login attempt (both succesfull and failed) (datetime)
     *
     * @var datetime
     * @access public
     */
    public $lastLogin;

    /**
     * Boolean to indicate that user wants login to be remembered (remember cookie)
     *
     * @var bool
     * @access public
     */
    public $remember;

    /**
     * Users browser string
     *
     * @var string
     * @access public
     */
    public $browser;

    /**
     * Users clietn IP address
     *
     * @var string
     * @access public
     */
    public $ip;

    /**
     * Number of hits during current login session
     *
     * @var int
     * @access public
     */
    public $hits;

    /**
     * Last successful hit
     *
     * @var string
     * @access public
     */
    public $lastHit;

    /**
     * Array containing all groups user has access to
     *
     * @var array
     * @access public
     */
    public $group;

    /**
     * Mysql connect string
     *
     * @var mysqli _mysqli
     * @access private
     */
    private $mysqli;

    /**
     * Create the authenticate object
     *
     * Creates the authenticate object that will contain all authorisation stuff
     *
     * @param mysqli $mysqli
     *            Valid mysqli object
     *
     * @return bool Success flag
     */
    public function __construct(mysqli $mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object');
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }

    /**
     * Distroys authenticate object
     *
     * Destroys the authenticate object and cleanup some stuff
     */
    public function __destruct()
    {
        $this->username = null;
        $this->passwordHash = null;
        $this->resetToken = null;
        $this->sessionHash = null;
        $this->email = null;
    }

    /**
     * Check if username is current user
     *
     * @param string $username
     *
     * @return bool Success flag
     */
    function checkUsername($username)
    {
        return ($this->username == $username);
    }

    /**
     * Check if user is member of group
     *
     * @param string $group
     *
     * @return bool Success flag
     */
    function checkGroup($group)
    {
        if (isset($this->group)) {
            return (in_array($group, $this->group));
        } else {
            return false;
        }
    }

    /**
     * Get groups
     *
     * @todo verplaatsen naar groups object
     * Get a list of all known groups
     *
     * @return array groups
     */
    public function get_groups()
    {
        $prep_stmt = "SELECT groupname FROM groups";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($groupname);

                while ($stmt->fetch()) {
                    $groups[] = $groupname;
                }
                $stmt->close();
            } else {
                $stmt->close();
                throw new Exception('No groups found');
            }
        } else {
            throw new Exception('Database error');
        }

        return $groups;
    }

    /**
     * Check the login credentials
     *
     * Checks the login credentials from the users session and fill the object
     *
     * @param
     *            string username Username
     * @param
     *            string password1 First password input
     * @param
     *            string password2 Second password intput
     * @param
     *            string email Email address
     * @param
     *            string firstname [optional] firstname of user
     * @param
     *            string lastname [optional] lastname of user
     *
     * @return array Sessions array
     */
    function login_check($username, $sessionHash, $remember)
    {
        $this->remember = $remember;

        // Check if all session variables are set
        if (isset($username, $sessionHash)) {
            $prep_stmt = "
                    SELECT
                        username,
                        sessionHash,
                        browser,
                        ip,
                        hits,
                        lastHit
                    FROM
                        sessions
                    WHERE
                        username = ?
                    AND
                        sessionHash = ?";
            $stmt = $this->mysqli->prepare($prep_stmt);

            if ($stmt) {
                $stmt->bind_param('ss', $username, $sessionHash);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($this->username, $this->passwordHash, $this->browser, $this->ip, $this->hits, $this->lastHit);
                    $stmt->fetch();

                    // Set info for new cookie
                    $this->hits ++;
                    $this->sessionHash = md5(uniqid(rand(), true));
                    $this->browser = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
                    $this->ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_SANITIZE_STRING);
                    $this->lastHit = date('Y-n-j H:i:s');

                    $stmt->close();

                    // Update sessions table
                    $prep_stmt = "UPDATE sessions SET sessionHash = ?, browser = ?, ip = ?, hits = ?, lastHit=? WHERE username = ? AND sessionHash = ?";
                    $stmt = $this->mysqli->prepare($prep_stmt);

                    if ($stmt) {
                        $stmt->bind_param('sssisss', $this->sessionHash, $this->browser, $this->ip, $this->hits, $this->lastHit, $this->username, $sessionHash);
                        if (! $stmt->execute()) {
                            $stmt->close();
                            throw new Exception('Failure updating validation status');
                        }
                    } else {
                        throw new Exception('Failure preparing statement for update validation status');
                    }

                    // Renew sessionHash in session (and cookie)
                    $session['sessionHash'] = $this->sessionHash;
                    $session['username'] = $this->username;
                    $session['remember'] = $this->remember;
                } else {
                    // Not logged in
                    $stmt->close();
                    throw new Exception('User and sessionHash not valid');
                }
            } else {
                // Not logged in
                throw new Exception('Database error');
            }
        } else {
            // Not logged in
            throw new Exception('No session found');
        }
        if ($stmt) {
            $stmt->close();
        }

        // Get all user info
        $prep_stmt = "SELECT firstName, lastName, email, passwordHash, resetToken, failedLogin, lastLogin, status, created FROM users WHERE username = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($this->firstName, $this->lastName, $this->email, $this->passwordHash, $this->resetToken, $this->failedLogin, $this->lastLogin, $this->status, $this->created);
                $stmt->fetch();
            } else {
                $stmt->close();
                throw new Exception('User not found');
            }
        } else {
            throw new Exception('Database error');
        }

        $stmt->close();

        // Get all groups for user
        $prep_stmt = "SELECT groupname FROM users_groups WHERE username = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // If the user exists get variables from result.
                $stmt->bind_result($result);
                while ($stmt->fetch()) {
                    $this->group[] = $result;
                }
            }
        } else {
            throw new Exception('Database error');
        }

        $stmt->close();

        // @TODO Clean-up old user sessions (sessions which are not used for 30 days)

        return $session;
    }

    /**
     * Authorisation check
     *
     * Checks the authorisation of the user. Is login successfull, and optionally check admin rights
     *
     * @param
     *            bool admin [optional] If true check admin rights
     *
     * @return bool success flag
     */
    public function authorisation_check($admin = false)
    {
        // Get the session information
        if (isset($_SESSION['username'], $_SESSION['sessionHash'])) {
            $username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
            $sessionHash = filter_var($_SESSION['sessionHash'], FILTER_SANITIZE_STRING);
            $remember = isset($_SESSION['remember']);

            // Try validating user session
            try {
                $session = $this->login_check($username, $sessionHash, $remember);
            } catch (Exception $e) {
                // Proberbly session mismatch (session hijacking?)
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Authorization failure',
                    'code' => 401
                ));
                $this->logout($username, $sessionHash);
                session_destroy();
                exit();
            }

            // If we are logged in, set session with new parameters
            if (isset($this->username)) {
                $_SESSION = $session;
            }
        } else {
            http_response_code(401);
            echo json_encode(array(
                'success' => false,
                'message' => 'Unauthorized',
                'code' => 401
            ));
            exit();
        }

        // We are logged in, now check rights if required

        // Check authorisation
        if ($admin && ((! is_array($this->group)) || ! (in_array('admin', $this->group) || in_array('super', $this->group)))) {
            http_response_code(403);
            echo json_encode(array(
                'success' => false,
                'message' => 'Forbidden',
                'code' => 403
            ));
            exit();
        }

        return true;
    }

    /**
     * Register a new user
     *
     * Tries to register a new user. First check input, when input is valid submit new user to database and send confirmation email
     *
     * @param
     *            string username Username
     * @param
     *            string password1 First password input
     * @param
     *            string password2 Second password intput
     * @param
     *            string email Email address
     * @param
     *            string firstname [optional] firstname of user
     * @param
     *            string lastname [optional] lastname of user
     *
     * @return bool success flag
     */
    public function register($username, $password1, $password2, $email, $firstname = null, $lastname = null)
    {

        // Do some basic checks
        if (strlen($username) < MINIMUM_USERNAME_LENGTH) {
            throw new Exception('Username to short, minimum of ' . MINIMUM_USERNAME_LENGTH . ' characters required');
        }
        if (strlen($password1) < MINIMUM_PASSWORD_LENGTH) {
            throw new Exception('Password to short, minimum of ' . MINIMUM_PASSWORD_LENGTH . ' characters required');
        }
        if ($password1 != $password2) {
            throw new Exception('Password strings not equal');
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email not a valid email address');
        }

        // Fill the object
        $this->username = $username;
        $this->passwordHash = password_hash($password1, PASSWORD_DEFAULT);
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->status = ! SEND_USERCREATE_MAIL;
        $this->resetToken = md5(uniqid(rand(), true));
        $this->created = date('Y-n-j H:i:s');

        // Check if username already exists
        $prep_stmt = "SELECT username FROM users WHERE username = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // A user with this username already exists
                $stmt->close();
                throw new Exception('A user with username ' . $username . ' already exists');
            }
            $stmt->close();
        } else {
            throw new Exception('Database error in check existing username');
        }

        // Check if email already exists
        $prep_stmt = "SELECT username FROM users WHERE email = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // A user with this email address already exists
                $stmt->close();
                throw new Exception('A user with email address ' . $email . ' already exists');
            }
            $stmt->close();
        } else {
            throw new Exception('Database error in check existing email');
        }

        // Insert the new user in the database
        if ($stmt = $this->mysqli->prepare("INSERT INTO users (username, firstName, lastName, email, passwordHash, resetToken, status, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param('ssssssis', $this->username, $this->firstname, $this->lastname, $this->email, $this->passwordHash, $this->resetToken, $this->status, $this->created);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure inserting new user into database');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement for new user');
        }

        // Send confirmation email if required
        if (! $this->status) {
            try {
                $this->sendConfirmationMail();
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }

        return true;
    }

    /**
     * Send confirmation mail
     *
     * Send a confirmation mail to the new registered user
     *
     * @return bool success flag
     */
    private function sendConfirmationMail()
    {
        // Prepaire email
        $address = 'https://' . $_SERVER['HTTP_HOST'] . str_replace('/register', '', $_SERVER['REQUEST_URI']) . '/verify/' . $this->username . '?token=' . $this->resetToken;

        $headers["MIME-Version"] = "1.0";
        $headers["Content-type"] = "text/plain; charset=iso-8859-1";
        $headers["From"] = EMAILFROM;
        $headers["Reply-To"] = EMAILREPLYTO;
        $headers["Subject"] = "Valideer gebruiker voor " . TITLE;
        $headers["X-Mailer"] = "PHP/" . phpversion();
        $headers["Date"] = date("D, d M Y H:i:s O");

        $to = $this->email;

        $message = 'Hallo ' . $this->firstname . ' ' . $this->lastname . "\r\n";
        $message .= "\r\n";
        $message .= 'Valideer uw gebruikersnaam door de onderstaande link te openen in een webbrowser.' . "\r\n\r\n";
        $message .= $address . "\r\n\r\n";
        $message .= 'Dit is een automatisch gegenereerde email. Reageren op deze email is niet mogelijk.' . "\r\n";
        $message .= 'Met vriendelijke groet, Urenregistratie Reddingsbrigade Apeldoorn';

        try {
            $this->_sendMail($to, $headers, $message);
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ));
        }

        return true;
    }

    /**
     * Validate new user account
     *
     * Response on the validation mail to validate a new user account
     *
     * @param
     *            string username Username of the user to be validated
     * @param
     *            string token Stored resetToken
     *
     * @return bool success flag
     */
    public function validate($username, $token)
    {
        // Check if username and token exists
        $prep_stmt = "SELECT username FROM users WHERE username = ? AND resetToken = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $username, $token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows != 1) {
                // User not found with requested token
                $stmt->close();
                throw new Exception('Invalid username and token combination');
            }
            $stmt->close();
        } else {
            throw new Exception('Database error in check token');
        }

        // Update user
        $prep_stmt = "UPDATE users SET resetToken = '', status = 1 WHERE username = ? AND resetToken = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $username, $token);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure updating validation status');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement for update validation status');
        }

        return true;
    }

    /**
     * Passwordreset
     *
     * Send password reset email
     *
     * @param
     *            string email User email
     *
     * @return bool Success flag
     */
    public function passwordreset($email)
    {
        $this->resetToken = md5(uniqid(rand(), true));
        $this->email = $email;

        // Get user information
        $prep_stmt = "SELECT username, email, firstName, lastName, status FROM users WHERE email = ? OR username = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $this->email, $this->email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows < 1) {
                $stmt->close();
                throw new Exception('User does not exist');
            }
            $stmt->bind_result($this->username, $this->email, $this->firstName, $this->lastName, $this->status);
            $stmt->fetch();

            $stmt->close();
        } else {
            throw new Exception('Database error in check existing username');
        }

        if ($this->status == 0) {
            throw new Exception('User not validated yet, please validate email address');
        } elseif ($this->status == 2) {
            throw new Exception('User is locked');
        } elseif ($this->status == 3) {
            throw new Exception('User is deleted');
        } elseif ($this->status != 1) {
            throw new Exception('Unkown user status');
        }

        // Update user
        $prep_stmt = "UPDATE users SET resetToken = ? WHERE email = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $this->resetToken, $this->email);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure updating validation status');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement for update validation status');
        }

        // Send confirmation email if required
        try {
            $this->sendPasswordResetMail();
        } catch (Exception $e) {
            throw new Exception($e);
        }

        return true;
    }

    /**
     * Send password reset mail
     *
     * Send a password reset mail to the registered user
     *
     * @return bool success flag
     */
    private function sendPasswordResetMail()
    {
        // Prepaire email
        $address = 'https://' . $_SERVER['HTTP_HOST'] . str_replace('/authenticate.php/passwordreset', '', $_SERVER['REQUEST_URI']) . '/resetpassword.php?user=' . $this->username . '&token=' . $this->resetToken;

        $headers["MIME-Version"] = "1.0";
        $headers["Content-type"] = "text/plain; charset=iso-8859-1";
        $headers["From"] = EMAILFROM;
        $headers["Reply-To"] = EMAILREPLYTO;
        $headers["X-Mailer"] = "PHP/" . phpversion();
        $headers["Subject"] = 'Reset wachtwoord voor ' . TITLE;
        $headers["Date"] = date("D, d M Y H:i:s O");

        $to = $this->email;

        $message = 'Hallo ' . $this->firstName . ' ' . $this->lastName . "\r\n";
        $message .= "\r\n";
        $message .= 'U kunt uw wachtwoord resetten via onderstaande link te openen in uw webbrowser.' . "\r\n\r\n";
        $message .= $address . "\r\n\r\n";
        $message .= 'Dit is een automatisch gegenereerde mail. Antwoorden op deze mail is niet mogelijk.' . "\r\n";
        $message .= 'Met vriendelijke groet, Urenregistratie Reddingsbrigade Apeldoorn';

        try {
            $this->_sendMail($to, $headers, $message);
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ));
        }

        return true;
    }

    private function _sendMail($to, $headers, $message)
    {
        include_once ('Mail.php');

        $smtp_params["host"] = SMTP_HOST;
        $smtp_params["port"] = SMTP_PORT;
        $smtp_params["auth"] = SMTP_AUTH;

        $smtp = @Mail::factory("smtp", $smtp_params);
        $mail = @$smtp->send($to, $headers, $message);

        if (isset($mail->message)) {
            throw new Exception($mail->message, 500);
        }

        return true;
    }

    /**
     * Change password
     *
     * Change the users password in the database. Can be called with old password or token
     *
     * @param
     *            string username Username
     * @param
     *            string oldpassword Old password, or null if token is used
     * @param
     *            string password1 New password
     * @param
     *            string password2 New password again
     * @param
     *            string token optional Password reset token send by email
     *
     * @return bool Success flag
     */
    public function change_password($username, $password, $password1, $password2, $token = null)
    {
        if (strlen($password1) < MINIMUM_PASSWORD_LENGTH) {
            throw new Exception('Password to short, minimum of ' . MINIMUM_PASSWORD_LENGTH . ' characters required');
        }
        if ($password1 != $password2) {
            throw new Exception('Password strings not equal');
        }

        // Get user information
        $prep_stmt = "SELECT username, passwordHash, resetToken, status FROM users WHERE username = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows < 1) {
                $stmt->close();
                throw new Exception('User does not exist');
            }
            $stmt->bind_result($this->username, $this->passwordHash, $this->resetToken, $this->status);
            $stmt->fetch();

            $stmt->close();
        } else {
            throw new Exception('Database error in check existing username');
        }

        if ($this->status == 0) {
            throw new Exception('User not validated yet, please validate email address');
        } elseif ($this->status == 2) {
            throw new Exception('User is locked');
        } elseif ($this->status == 3) {
            throw new Exception('User is deleted');
        } elseif ($this->status != 1) {
            throw new Exception('Unkown user status');
        }
        // We are called without token, so check old password
        if ($token == null) {
            if (! password_verify($password, $this->passwordHash)) {
                throw new Exception('Password not correct');
            }
        } elseif ($token != $this->resetToken) {
            throw new Exception('Token not correct');
        }

        $passwordHash = password_hash($password1, PASSWORD_DEFAULT);

        // Set user data for failed login
        if ($stmt = $this->mysqli->prepare("UPDATE users SET passwordHash = ?, resetToken = '' WHERE username = ?")) {
            $stmt->bind_param('ss', $passwordHash, $username);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure changing password');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing update statement');
        }

        return true;
    }

    /**
     * Login
     *
     * Do login validation and set session when login succesfull
     *
     * @param
     *            string username Username
     * @param
     *            string password Password
     * @param
     *            bool remember Boolean to specify if login needs to be remembered
     *
     *            @session array Session information
     */
    public function login($loginname, $password, $remember, $sessionHash)
    {
        $this->remember = $remember;

        // Get user information
        $prep_stmt = "SELECT username, email, firstName, lastName, passwordHash, failedLogin, lastLogin, status FROM users WHERE username = ? OR email = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $loginname, $loginname);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows < 1) {
                $stmt->close();
                throw new Exception('User does not exist');
            }
            $stmt->bind_result($this->username, $this->email, $this->firstName, $this->lastName, $this->passwordHash, $this->failedLogin, $this->lastLogin, $this->status);
            $stmt->fetch();

            $stmt->close();
        } else {
            throw new Exception('Database error in check existing username');
        }

        if (! password_verify($password, $this->passwordHash)) {
            // Password is not correct, user not logged in

            $this->lastHit = date('Y-n-j H:i:s');
            $this->failedLogin ++;

            if ($this->failedLogin > 6) {
                $this->lock_account();
                // False means the account is locked due to too many login attempts
                return false;
            }

            // Set user data for failed login
            if ($stmt = $this->mysqli->prepare("UPDATE users SET failedLogin = ?, lastLogin = ? WHERE username = ?")) {
                $stmt->bind_param('iss', $this->failedLogin, $this->lastHit, $this->username);
                if (! $stmt->execute()) {
                    $stmt->close();
                    throw new Exception('Failure inserting user info');
                }
                $stmt->close();
            } else {
                throw new Exception('Failure preparing statement');
            }
            throw new Exception('Password not correct');
        }

        if ($this->status == 0) {
            throw new Exception('User not validated yet, please validate email address');
        } elseif ($this->status == 2) {
            throw new Exception('User is locked');
        } elseif ($this->status == 3) {
            throw new Exception('User is deleted');
        } elseif ($this->status != 1) {
            throw new Exception('Unkown user status');
        }

        // User is succesfully, destroy old session record in database
        if (isset($sessionHash)) {
            // Get session information
            $prep_stmt = "DELETE FROM sessions WHERE username = ? AND sessionHash = ?";
            $stmt = $this->mysqli->prepare($prep_stmt);

            if ($stmt) {
                $stmt->bind_param('ss', $username, $sessionHash);
                $stmt->execute();

                $stmt->close();
            } else {
                $stmt->close();
                throw new Exception('Database error');
            }
        }

        // Set some administration data
        $this->sessionHash = md5(uniqid(rand(), true));
        $this->browser = $_SERVER['HTTP_USER_AGENT'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->hits = 1;
        $this->lastHit = date('Y-n-j H:i:s');

        // Set the new session info
        $session['username'] = $this->username;
        $session['sessionHash'] = $this->sessionHash;
        $session['remember'] = $this->remember;

        // Insert session settings in database
        if ($stmt = $this->mysqli->prepare("INSERT INTO sessions (username, sessionHash, browser, ip, hits, lastHit) VALUES (?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param('ssssis', $this->username, $this->sessionHash, $this->browser, $this->ip, $this->hits, $this->lastHit);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure inserting session info');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement');
        }

        // Insert user data in database
        if ($stmt = $this->mysqli->prepare("UPDATE users SET failedLogin = 0, lastLogin = ? WHERE username = ?")) {
            $stmt->bind_param('ss', $this->lastHit, $this->username);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure inserting user info');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement');
        }

        return $session;
    }

    /**
     * Lock account
     *
     * Lock the account and send an unlock email
     *
     * @return bool Success flag
     */
    private function lock_account()
    {
        $this->resetToken = md5(uniqid(rand(), true));
        $this->sendUnlockMail();

        // Set user data for failed login
        if ($stmt = $this->mysqli->prepare("UPDATE users SET lastLogin = ?, resetToken = ?, status = 2 WHERE username = ?")) {
            $stmt->bind_param('sss', $this->lastHit, $this->resetToken, $this->username);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure inserting user info');
            }

            //
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement');
        }
        return true;
    }

    /**
     * Send account unlock mail
     *
     * Send an account unlock mail to the registered user
     *
     * @return bool success flag
     */
    public function sendUnlockMail()
    {
        // Prepaire email
        $address = 'https://' . $_SERVER['HTTP_HOST'] . str_replace('/login', '', $_SERVER['REQUEST_URI']) . '/unlock/' . $this->username . '?token=' . $this->resetToken;

        $headers["MIME-Version"] = "1.0";
        $headers["Content-type"] = "text/plain; charset=iso-8859-1";
        $headers["From"] = EMAILFROM;
        $headers["Reply-To"] = EMAILREPLYTO;
        $headers["Subject"] = "Ontgrendel account voor " . TITLE;
        $headers["X-Mailer"] = "PHP/" . phpversion();
        $headers["Date"] = date("D, d M Y H:i:s O");

        $to = $this->email;

        $message = 'Hello ' . $this->firstName . ' ' . $this->lastName . "\r\n";
        $message .= "\r\n";
        $message .= 'Uw account is vergrendeld door te veel login pogingen. U kunt uw account ontgrenderen via de onderstaande link.' . "\r\n\r\n";
        $message .= $address . "\r\n\r\n";
        $message .= 'Dit is een automatisch gegenereerde email, u kunt niet reageren op deze email.' . "\r\n";
        $message .= 'Met vriendelijke groet, Urenregistratie Reddingsbrigade Apeldoorn';

        try {
            $this->_sendMail($to, $headers, $message);
        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ));
        }

        return true;
    }

    /**
     * Unlock user account
     *
     * Unlock a locked user account
     *
     * @param
     *            string username
     * @param
     *            string token
     *
     * @return bool success flag
     */
    public function unlock($username, $token)
    {
        // Check if username and token exists
        $prep_stmt = "SELECT username FROM users WHERE username = ? AND resetToken = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $username, $token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows != 1) {
                // User not found with requested token
                $stmt->close();
                throw new Exception('Invalid username and token combination');
            }
            $stmt->close();
        } else {
            throw new Exception('Database error in check token');
        }

        // Update user
        $prep_stmt = "UPDATE users SET resetToken = '', failedLogin = 0, status = 1 WHERE username = ? AND resetToken = ?";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('ss', $username, $token);
            if (! $stmt->execute()) {
                $stmt->close();
                throw new Exception('Failure updating validation status');
            }
            $stmt->close();
        } else {
            throw new Exception('Failure preparing statement for update validation status');
        }

        return true;
    }

    /**
     * Login
     *
     * Do login validation and set session when login succesfull
     *
     * @param
     *            string username Username
     * @param
     *            string password Password
     * @param
     *            bool remember Boolean to specify if login needs to be remembered
     *
     * @return bool Success flag
     */
    public function logout($username, $sessionHash)
    {

        // Destroy old session record in database
        if (isset($sessionHash)) {
            // Get session information
            $prep_stmt = "DELETE FROM sessions WHERE username = ? AND sessionHash = ?";
            $stmt = $this->mysqli->prepare($prep_stmt);

            if ($stmt) {
                $stmt->bind_param('ss', $username, $sessionHash);
                $stmt->execute();

                $stmt->close();
            } else {
                throw new Exception('Database error');
            }
        }

        return true;
    }
}
