/**
 * overzicht pagina application
 *
 * Functies voor overzicht pagina
 *
 * AngularJS 1.4.7
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
 * @since      File available since Release 1.0.0
 * @version    1.2.3
 */

// --------------------------------------------------------------------
// Custom App

angular.module('myApp')
.controller('gebuikersCtrl',function($scope,$filter,$http) {
	
	$scope.certificaten = '';
	$scope.goedtekeuren = '';
	$scope.punten = '';
	$scope.spinner = false;

	// Get application configuration
	function loadConfig() {
		$http({
			mehtod : 'GET',
			url : 'rest/config.php',
			headers : { 'Content-Type' : 'applicication/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
			} else {
				$scope.config = response.data;
			}
		})
	}
	
	// Load own data
	function loadOwn() {
		$scope.spinner = true;
		
		$http({
			method : 'GET',
			url : 'authenticate.php/self',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
			} else {
				$scope.self = response.data;
				refresh();
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
		});
	}
	
	// Load certificaten
	function loadCertificaten() {
		$scope.spinner = true;
						
		// Load uren
		$http({
			method : 'GET',
			url : 'rest/rapportage.php/certificaten/' + $scope.self.username,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.certificaten = response.data.records;
				
				loadPunten();
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	}
	
	// Load punten 	
	function loadPunten() {
		$http({
			method : 'GET',
			url : 'rest/punten.php/getTotals/' + $scope.self.username,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.punten = response.data.punten;

				loadGoedtekeuren();
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	}

	//Load goed te keuren uren
	function loadGoedtekeuren() {
		$http({
			method : 'GET',
			url : 'rest/rapportage.php/goedtekeuren/' + $scope.self.username,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (angular.isArray(response.data.records)) {
						$scope.goedtekeuren = response.data.records;
						$scope.showGoedtekeuren = true;
				} else {
					$scope.showGoedtekeuren = false;
				}
				
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	}
	
	// Refresh data
	function refresh() {
		loadCertificaten();
	}
	
	// ------------------------------------------------------------
	// Helper functions
	
	// Bereken uren nodig
	$scope.urenNodig = function(totaal, goedgekeurd) {
		if (totaal < goedgekeurd) {
			return 0;
		} else {
			return totaal - goedgekeurd;
		}
	}
	
	// Bereken max uren
	$scope.urenMax = function(uren, max) {
		if (uren > max) {
			return max;
		} else {
			return uren;
		}
	}

	// Bereken totaal
	$scope.getTotal = function(array,field) {
		var total = 0;
		for (var i = 0; i < array.length; i++) {
			var value = array[i];
			total += value[field];
		}
		return total;
	}

	// First load own data, refresh when load is complete
	
	loadConfig();
	loadOwn();
	
});
