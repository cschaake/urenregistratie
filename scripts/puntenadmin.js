/**
 * Configuratie app
 *
 * AngularJS application for configuratie pagina
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
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.6
 * @version    1.2.1
 */
// --------------------------------------------------------------------
// Custom App

angular.module('myApp')

.controller('puntenadminCtrl',function($scope,$filter,$http) {
	
	$scope.puntenwaardes = {};
	
	$scope.loadPuntenWaardes = function() {
		$http({
			mehtod : 'GET',
			url : 'rest/puntenwaardes.php',
			headers : { 'Content-Type' : 'applicication/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
			} else {
				$scope.puntenwaardes = response.data.puntenwaardes;
			}
		})
	}
	
	// Function to delete a single row based on index
	$scope.herbereken = function(index) {
		$scope.log = $scope.log + "2e regel report\n";
		
		// Post herberekenen van punten
		$http({
			method : 'POST',
			url : 'rest/punten.php/herbereken',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			$scope.log = response.data;
			
		}, function() {
			$scope.message = 'Herberekenen punten mislukt';
			
		});
		$scope.spinner = false;
	}
	
	// Function to refresh the table data (retrieve data again from backend)
	$scope.refresh = function() {
		$scope.loadPuntenWaardes();
	}
	
	$scope.herberekenPunten = function() {
		console.log("Herbereken punten");
		$scope.log = "Start report\n";
		$scope.herbereken();
	}

	
	$scope.refresh();
});
