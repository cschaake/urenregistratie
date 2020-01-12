/**
 * Urenregistratie app
 * 
 * AngularJS application for Urenregistratie pagina
 * 
 * PHP version 7.4
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
 * @copyright 2020 Schaake.nu
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @since File available since Release 1.2.0
 * @version 1.2.3
 */
// --------------------------------------------------------------------
// Custom App

angular.module('myApp')

.controller('gebuikersCtrl',function($scope,$filter,$http) {

	// ------------------------------------------------------------
	// Initiate Form handing
	$scope.form = null; // Container for data within the form during edit
	$scope.original = null; // Container to store the original data before
				// starting editing in form
	
	// ------------------------------------------------------------
	// Initiate sort
	$scope.sortType = ''; // Field to be sorted
	$scope.sortReverse = false; // Reverse sort order
	
	// ------------------------------------------------------------
	// Initiate paging
	$scope.tableListOptions = [10, 25, 100]; // Predefined options for
						    // page lengths
	$scope.itemsPerPage = 10; // Initial page length
	$scope.startItem = -1; // Set start item to first
	$scope.totalItems = 0; // Get initial total records
	$scope.totalRecords = 0; // Get total unfiltered records
	
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
	
	$scope.refresh = function() {
		refresh();
	}
	function refresh() {
		$scope.load();
	}
	
	// ------------------------------------------------------------
	// Data load functions
	
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
				$scope.refresh();
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
		});
	}

	$scope.load = function() {
		$scope.spinner = true;
						
		// Load uren
		$http({
			method : 'GET',
			url : 'rest/activiteiten.php',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (response.data.activiteiten) {
					$scope.activiteiten = convertJsonDate(response.data.activiteiten);
				} else {
					$scope.activiteiten = [];
				}
				$scope.activiteitenGroepen = response.data.groepen;
				$scope.rollen = response.data.rollen;

				// Set paging
				if ($scope.activiteiten.length > 0) {
					$scope.startItem = 0;
				}
				$scope.totalItems = $scope.activiteiten.length;
				$scope.totalRecords = $scope.activiteiten.length;
				
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
		
	}
	
	// Function to add a new record
	$scope.insert = function(index) {
		insert(index);
	}
	function insert(index) {
		$scope.spinner = true;
		
		console.log($scope.form);
		
		if ($scope.editForm.$valid) {
			
			if ($scope.form.edit === true) {
				// The true value is set in the $scope.edit
				// function
				$http({
					method : 'PUT',
					url : 'rest/activiteiten.php/' + $scope.form.id,
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messagelocal = response.data.message;
						$scope.spinner = false;
					} else {
						response.data.activiteiten[0].datum = new Date(response.data.activiteiten[0].datum);
						$scope.activiteiten[index] = response.data.activiteiten[0];
						
						$scope.totalItems = $scope.activiteiten.length;
						$scope.totalRecords = $scope.activiteiten.length;
						
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.messagelocal = response.data.message;
					$scope.spinner = false;
				});
				
			} else {
				$http({
					method : 'POST',
					url : 'rest/activiteiten.php',
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messagelocal = response.data.message;
						$scope.spinner = false;
					} else {
						response.data.activiteiten[0].datum = new Date(response.data.activiteiten[0].datum);
						$scope.activiteiten.push(response.data.activiteiten[0]);

						$scope.totalItems = $scope.activiteiten.length;
						$scope.totalRecords = $scope.activiteiten.length;
						
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.messagelocal = response.data.message;
					$scope.spinner = false;
				});
			}
		}
	}

	// Function to close the edit modal after update or insert
	$scope.closeEditModal = function() {
		closeEditModel();
	}
	function closeEditModel() {
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
	$scope.deleteActiviteit = function(index) {
		deleteActiviteit(index);
	}
	function deleteActiviteit(index) {
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/activiteiten.php/' + $scope.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.messagelocal = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.activiteiten.splice(index,1);
				
				$scope.totalItems = $scope.activiteiten.length;
				$scope.totalRecords = $scope.activiteiten.length;
				
				$scope.form = null;
				$scope.original = null;
				$('#deleterecord').modal('hide'); // Close the
								    // modal
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.messagelocal = response.data.message;
			$scope.spinner = false;
		});
	}

	// ------------------------------------------------------------
	// Form handing

	// Function to reset the form for a new record
	$scope.new = function() {
		newActiviteit();
	}
	function newActiviteit() {
		$scope.form = {}; // Destroy the current form, if any
		$scope.original = angular.copy($scope.form);
		
		$scope.startItem = ($scope.numberOfPages() - 1) * $scope.itemsPerPage
	}
	
	// Function to fill the form with the record to be editted
	$scope.edit = function(index) {
		$scope.form = angular.copy($scope.activiteiten[index]);
		$scope.form.index = index; 
		$scope.form.edit = true;
		$scope.original = angular.copy($scope.form);
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
		$scope.totalItems = $filter('filter')($scope.activiteiten,$scope.search).length;
		if ($scope.totalItems < 1) {
			$scope.startItem = -1;
		} else {
			$scope.startItem = 0;
		}
	};
	
	// Function to reset search and recalculate total items
	$scope.resetSearch = function() {
		$scope.search = '';
		$scope.totalItems = $filter('filter')($scope.activiteiten,$scope.search).length;
	}

	/**
	 * Zet default datum en tijd bij tijdloze activiteit
	 * 
	 */
	$scope.$watch("form.nodate", function(nodate) {
		if (nodate == true) {
			$scope.form.datum = new Date('1970', '01', '01');
			$scope.form.begintijd = '00:00';
			$scope.form.eindtijd = '00:00';
		}
	});
	
	// Helper function to convert the dates and time in uren object
	function convertJsonDate(array) {
		var l = array.length;
		for (var k=0; k<l; k++) {
			array[k].datum = new Date(array[k].datum);
			if (array[k].start) {
				array[k].start = array[k].start.substring(0,5);
			}
			if (array[k].eind) {
				array[k].eind = array[k].eind.substring(0,5);
			}
		}
		return array;
	}

	loadConfig();
	// First load own data, refresh when load is complete
	loadOwn();
	
});	
