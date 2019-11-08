/**
 * Opleidingsuren AngularJS applicatie
 *
 * AngularJS applicatie voor opleidingsuren pagina
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
 * @since      File available since Release 1.0.5
 * @version    1.2.1
 */

// --------------------------------------------------------------------
// Custom App

angular.module('myApp')
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
	
	// Function to refresh the table data (retrieve data again from backend)
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
			url : 'rest/opleidingsuren.php',
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
				if (angular.isArray(response.data.users)) {
					$scope.users = response.data.users;
				} else {
					$scope.users = '';
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

	// Function to add a new record
	$scope.insert = function(index) {
		$scope.spinner = true;
		
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		
		
		if ($scope.editForm.$valid) {
			$scope.form.akkoord = 1;
			$scope.form.activiteit = 2;
			$scope.form.groep_id = 1;
			$scope.form.rol = 3;
			
			if ($scope.form.edit === true) {
				// The true value is set in the $scope.edit function
				
				// Insert code to backend here - Update existing record
				$scope.spinner = true;

				$http({
					method : 'PUT',
					url : 'rest/opleidingsuren.php/' + $scope.form.id,
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					
					$scope.message = '';
					$scope.spinner = false;
					
					if (response.data.message) {
						$scope.message = response.data.message;
						$scope.spinner = false;
						
					} else {
						$scope.uren[index] = response.data;
						$scope.uren[index].datum = new Date(response.data.datum);
						
						$scope.totalItems = $scope.uren.length; // Get initial total records
						$scope.totalRecords = $scope.uren.length; // Get total unfiltered records
		
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.message = response.data.message;
					$scope.spinner = false;
				});

			} else {
				$http({
					method : 'POST',
					url : 'rest/opleidingsuren.php',
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					
					$scope.message = '';
					$scope.spinner = false;

					if (response.data.message) {
						$scope.message = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.refresh();
					
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.message = response.data.message;
					$scope.spinner = false;
				});
				
			}
		}
	};
	
	// Function to close the edit modal after update or insert
	$scope.closeEditModal = function() {
		$('#editrecord').modal('hide'); // Close the modal
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.message = '';
		$scope.messagelocal = '';
		
		$scope.form = null;
		$scope.original = null;
		$scope.spinner = false;
	}

	// Function to delete a single row based on index 
	$scope.delete = function(index) {
		$scope.spinner = true;

		$http({
			method : 'DELETE',
			url : 'rest/opleidingsuren.php/' + $scope.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
					
			$scope.message = '';
			$scope.spinner = false;
	
			if (!response.data.success) {
				$scope.message = response.data.message;
				$scope.spinner = false;
						
			} else {
				$scope.uren.splice(index,1);
				
				$scope.totalItems = $scope.uren.length; // Get initial total records
				$scope.totalRecords = $scope.uren.length; // Get total unfiltered records
				
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
		
		
	};

	// ------------------------------------------------------------
	// Form handing

	// Function to reset the form for a new record
	$scope.new = function() {
		$scope.form = {}; // Destroy the current form, if any
		$scope.form.uren = 25;
		$scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
		$scope.startItem = ($scope.numberOfPages() - 1) * $scope.itemsPerPage
	}
	
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

		$scope.form = angular.copy($scope.original); // Copy the temporary original object back to the current form
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

	function convertJsonDate(array) {
		var l = array.length;
		for (var k=0; k<l; k++) {
			array[k].datum = new Date(array[k].datum);
		}
		return array;
	}
	
	$scope.loadConfig();
	
	$scope.refresh();
	
});
