/**
 * Feedback app
 * 
 * AngularJS application for feedback widget
 * 
 * PHP version 5.4
 * 
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/mit-license.html MIT License. If you did
 * not receive a copy of the MIT License and are unable to obtain it through the
 * web, please send a note to license@php.net so we can mail you a copy
 * immediately.
 * 
 * @package Urenverantwoording
 * @author Christiaan Schaake <chris@schaake.nu>
 * @copyright 2017 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @since File available since Release 1.0.8
 * @version 1.0.8
 */
// --------------------------------------------------------------------
// Custom App
angular.module('myApp')

.controller('feedbackCtrl', function($scope, $http) {

    // ------------------------------------------------------------
    // Initiate Form handing
    $scope.star = null;
    $scope.starDescription = 'Selecteer een smiley';
    $scope.form = {};

    $scope.setStar = function(star) {

	switch (star) {
	case 1:
	    $scope.starDescription = 'Compleet onwerkbaar';
	    $scope.form.star = 1;
	    break;
	case 2:
	    $scope.starDescription = 'Kan beter';
	    $scope.form.star = 2;
	    break;
	case 3:
	    $scope.starDescription = 'Ach, het doet wat het moet';
	    $scope.form.star = 3;
	    break;
	case 4:
	    $scope.starDescription = 'Ziet er goed uit';
	    $scope.form.star = 4;
	    break;
	default:
	    $scope.starDescription = 'Helemaal te gek!';
	    $scope.form.star = 5;
	}

    }

    $scope.submitFeedback = function() {
	$scope.spinner = true;

	$http({
	    method : 'POST',
	    url : 'rest/feedback.php',
	    data : $scope.form,
	    headers : {
		'Content-Type' : 'application/json'
	    }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.messagefeedback = response.data.message;
		$scope.spinner = false;
	    } else {
		$('#feedbackModal').modal('hide'); // Close the modal
		$('#feedbackOkModal').modal('show'); // Close the modal
	    }
	}, function(response) {
	    $scope.messagefeedback = response.data.message;
	    $scope.spinner = false;
	});
    }
});
