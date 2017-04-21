/**
 * Authenticate script
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
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.9
 */

angular.module('myApp')
.directive('pwCheck', [function () {
    return {
        require: 'ngModel',
        link: function (scope, elem, attrs, ctrl) {
            var firstPassword = '#' + attrs.pwCheck;
            elem.add(firstPassword).on('keyup', function () {
                scope.$apply(function () {
                    var v = elem.val()===$(firstPassword).val();
                    ctrl.$setValidity('pwmatch', v);
                });
            });
        }
    }
}])
.controller('loginCtrl', function($scope, $http) {
    
    // Initiate some parameters
    $scope.showPassword = false;
    $scope.showPassword1 = false;
    $scope.showPassword2 = false;
    $scope.message = null;
    
    $scope.register = {};
    
    /**
     * processLoginForm
     *
     * Processes the information returned by the login form
     *
     * @param {object} login - Form parameters
     */
    $scope.processLoginForm = function(login) {
        $scope.spinner = true;
        
        $http({
            method : 'POST',
            url : 'authenticate.php/login',
            data : login,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
        
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                if (response.data.code === 2) {
                    $('#loginModal').modal('hide');
                    $('#accountLockedModal').modal('show');
                } else {
                    $scope.loginForm.username.$setValidity(false);
                    $scope.loginForm.password.$setValidity(false);
                
                    $scope.message = 'Gebruikersnaam is onbekend of wachtwoord is onjuist.';
                }
            } else {
                $('#loginModal').modal('hide');
                $('#loginSuccessModal').modal('show');
                document.location.reload(true);
            }
        }, function(response) {
            $scope.message = 'Login mislukt';
        });
    };
    
    /**
     * processLogoutForm
     *
     * Process the information returned by the logout form
     *
     */
    $scope.processLogoutForm = function() {
        $scope.spinner = true;
        $http({
            method : 'POST',
            url : 'authenticate.php/logout',
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
        
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                $scope.message = response.data.message;
            } else {
                $('#logoutModal').modal('hide');
                $('#logoutSuccessModal').modal('show');
                window.location = "index.php";
                
            }
        }, function(response) {
            $scope.message = 'Logout mislukt';
        });
    };

    /**
     * processForgotPasswordForm
     *
     * Progress the forget password form
     *
     */
    $scope.progressForgotPassword = function(forgotPassword) {
        $scope.spinner = true;
        
        $http({
            method : 'POST',
            url : 'authenticate.php/passwordreset',
            data : forgotPassword,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
            
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                $scope.forgotPasswordForm.email.$setValidity(false);
                $scope.message = response.data.message;
            } else {
                $('#forgotPasswordModal').modal('hide');
                $('#forgotPasswordSuccessModal').modal('show');
            }
        }, function(response) {
            $scope.forgotPasswordForm.email.$setValidity(false);
            $scope.message = 'Request mislukt';
        });
    };

    /**
     * processChangePasswordForm
     *
     * Process the change passsword form
     *
     */
    $scope.processChangePasswordForm = function(password) {
        $scope.spinner = true;
        
        $http({
            method : 'POST',
            url : 'authenticate.php/passwordchange',
            data : password,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
            
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                $scope.message = response.data.message;
                
            } else {
                $('#changePasswordModal').modal('hide');
                $('#changePasswordSuccessModal').modal('show');
                window.location = "index.php";
            }
        }, function(response) {
            $scope.message = 'Request mislukt';
        });
    }

    /**
     * processRegisterForm
     *
     * Process the registration form
     *
     */
    $scope.processRegisterForm = function(register) {
        $scope.spinner = true;
        
        $http({
            method : 'POST',
            url : 'authenticate.php/register',
            data : register,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
            
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                $scope.message = response.data.message;
                
            } else {
                $('#registerModal').modal('hide');
                $('#registerSuccessModal').modal('show');
            }
        }, function(response) {
            $scope.message = 'Registratie mislukt';
        });
    };
    
    /**
     * reset
     *
     * Reset variables after reset button is pressed
     *
     */
    $scope.reset = function() {
        $scope.$broadcast('show-errors-reset');
        $scope.spinner = false;
        
        $scope.login = null;
        $scope.register = null;
        $scope.message = null;
    };
    
    /**
     * showForgotPassword
     *
     * Hides the loginModal and shows the forgotPasswordModal
     *
     */
    $scope.showForgotPassword = function() {
        $('#loginModal').modal('hide');
        $('#forgotPasswordModal').modal('show');
    };
    
    /**
     * showProfileModal
     *
     * Get user info and show profileModal
     *
     */
    $scope.showProfileModal = function(username) {
        $scope.spinner = true;
        
        $http({
            method : 'GET',
            url : 'authenticate.php/' + username,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
            
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data) {
                $scope.message = 'Gebruikers informatie niet gevonden.';
                
            
            } else {
                $scope.profile = response.data.users[0];
                console.log($scope.profile);
                $('#profileModal').modal('show');
            }
        }, function(response) {
            $scope.message = 'Request mislukt';
        });
    }
    
    /**
     * processProfileModal
     *
     * Process the info returned by the profileModal
     *
     */
    $scope.processProfileForm = function(profile) {
        $scope.spinner = true;
        
        $http({
            method : 'PUT',
            url : 'authenticate.php/' + profile.username,
            data : profile,
            headers : { 'Content-Type': 'application/json' }
        }).then(function(response) {
            
            $scope.message = '';
            $scope.spinner = false;
            
            if (!response.data.success) {
                $scope.message = response.data.message;
                
            } else {
                $('#profileModal').modal('hide');
                $('#profileSuccessModal').modal('show');
            }
        }, function(response) {
            $scope.message = 'Request mislukt';
        });
    }
    
});
