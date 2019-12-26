/**
 * Urengoedkeuren app
 *
 * AngularJS app voor de urengoedkeuren pagina
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
 * @copyright  2020 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.2.2
 */

// --------------------------------------------------------------------
// Custom App

angular.module('myApp')

// Filter to display akkoord
.filter('akkoordFilter', function() {
	var output;
	return function(input) {
		if (input === 1) {
			output = 'ja';
		} 
		if (input === 2) {
			output = 'nee';
		}
		if (input < 1) {
			output = '';
		}
		return output;
	}
})

.controller('gebuikersCtrl',function($scope,$filter,$http) {
	
	// ------------------------------------------------------------
	// Initiate Form handing
	$scope.form = null; // Container for data within the form during edit
	$scope.original = null; // Container to store the original data before starting editing in form
	
	// ------------------------------------------------------------
	// Initiate sort
	$scope.sortType = ''; // Field to be sorted
	$scope.sortReverse = false; // Reverse sort order
	
	// ------------------------------------------------------------
	// Initiate paging
	$scope.tableListOptions = [10, 25, 100]; // Predefined options for page lengths
	$scope.itemsPerPage = 10; // Initial page length
	$scope.startItem = -1; // Set start item to first
	$scope.totalItems = 0; // Get initial total records
	$scope.totalRecords = 0; // Get total unfiltered records

	$scope.uren = '';

	$scope.spinner = false;
	
	// Get application configuration
	$scope.loadConfig = function() {
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
	
	$scope.refresh = function() {
		$scope.load();
	}
	
	// ------------------------------------------------------------
	// Data load functions
	
	$scope.load = function() {
		$scope.spinner = true;
						
		// Load uren
		$http({
			method : 'GET',
			url : 'rest/goedkeuren.php',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (angular.isArray(response.data.uren)) {
					$scope.uren = convertJsonDate(response.data.uren);
					$scope.startItem = 0; // Set start item to first
				} else {
					$scope.uren = '';
					$scope.startItem = -1; // Set start item to first
				}
				$scope.activiteiten = response.data.activiteiten;
				
				// @todo check voor opleidingsuren voor button
				$scope.opleidingBoeken = true;
				
				if (angular.isArray(response.data.groepen)) {
					$scope.groepen = response.data.groepen;
				} else {
					$scope.groepen = '';
				}
				$scope.spinner = false;
				
				// Non configurable options
				
				$scope.totalItems = $scope.uren.length; // Get initial total records
				$scope.totalRecords = $scope.uren.length; // Get total unfiltered records
				
				$scope.urenVoorNamen = $scope.uniqueCopy($scope.uren, 'voornaam');
				$scope.urenAchterNamen = $scope.uniqueCopy($scope.uren, 'achternaam');
				$scope.urenActiviteiten = $scope.uniqueCopy($scope.uren, 'activiteit');
				$scope.urenRollen = $scope.uniqueCopy($scope.uren, 'rol');

			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	}
					
	$scope.goedkeuren = function(index) {
		$http({
			method : 'POST',
			url : 'rest/goedkeuren.php/' + $scope.form.id + '/goedkeuren',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.uren[index].akkoord = 1;
				
				$scope.spinner = false;
				$scope.reset();
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	};
	
	$scope.afkeuren = function(index) {
		$http({
			method : 'POST',
			data: $scope.form,
			url : 'rest/goedkeuren.php/' + $scope.form.id + '/afkeuren',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.uren[index].akkoord = 2;
				$scope.uren[index].reden = $scope.form.reden;
				
				$scope.spinner = false;
				$scope.reset();
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
		
		
	};

	// ------------------------------------------------------------
	// Form handing
	
	// Function to fill the form with the record to be editted
	$scope.edit = function(index) {
		$scope.form = angular.copy($scope.uren[index]); // Copy the object row to the temporary form object
		$scope.form.index = index; // Store the index of the original record
		$scope.form.edit = true; // Set the edit variable to true, needed in the $scope.insert function
		
		$scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
	}

	// Function to reset the form to its original state
	$scope.reset = function() {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.message = '';
		$scope.messagelocal = '';

		$scope.form = angular.copy($scope.original);
	}

	// ------------------------------------------------------------
	// Page handing

	// Determine current page
	$scope.currentPage = function() {
		return Math.ceil($scope.startItem / $scope.itemsPerPage) + 1;
	};

	// Determine total number of pages
	$scope.numberOfPages = function() {
		return Math.ceil($scope.totalItems / $scope.itemsPerPage);
	};

	// Determine first item on last page
	$scope.lastItemPage = function() {
		if ($scope.startItem + $scope.itemsPerPage > $scope.totalItems ) {
			return $scope.totalItems;
		} else {
			return $scope.startItem + $scope.itemsPerPage;
		}
	}

	// Function to create pages list in pagination
	$scope.pagesList = function() {
		var rng = [];
		
		for (var i = 1; i < $scope.numberOfPages() + 1; i++) {
			rng.push(i);
		}
		return rng;
	};

	// ------------------------------------------------------------
	// Search handing
	
	// Function to recalculate total items
	$scope.onSearch = function() {
		$scope.totalItems = $filter('filter')($scope.uren,$scope.search).length;
		if ($scope.totalItems < 1) {
			$scope.startItem = -1;
		} else {
			$scope.startItem = 0;
		}
	};

	$scope.resetSearch = function() {
		$scope.search='';
		$scope.totalItems = $filter('filter')($scope.uren,$scope.search).length;
	};
	
	// ------------------------------------------------------------
	// Helper functions

	function convertJsonDate(array) {
		var l = array.length;
		for (var k=0; k<l; k++) {
			array[k].datum = new Date(array[k].datum);
			array[k].start = new Date('1970-01-01T' + array[k].start);
			array[k].eind = new Date('1970-01-01T' + array[k].eind);
			
		}
		return array;
	}

	$scope.uniqueCopy = function(o,key) {                    
		var out, i;
		out = [];
		for (i in o) {
			if (out.indexOf(o[i][key]) === -1) {
				out.push(o[i][key]);
			}
		}
		out.sort();
		return out;
	}
	
	$scope.loadConfig();

	$scope.refresh();
});
