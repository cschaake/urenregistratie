/**
 * Urenregistratie app
 * 
 * AngularJS application for Urenregistratie pagina
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
 * @since File available since Release 1.0.0
 * @version 1.0.7
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
	
	$scope.refresh = function() {
		$scope.load();
	}
	
	// ------------------------------------------------------------
	// Data load functions
	
	// Load own data
	$scope.loadOwn = function() {
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
			url : 'rest/urenboeken.php',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (response.data.uren) {
					$scope.uren = convertJsonDate(response.data.uren);
				} else {
					$scope.uren = [];
				}
				$scope.urenActiviteiten = response.data.activiteiten;
				$scope.urenRollen = response.data.rollen;

				$scope.opmerkingRequired = $scope.getOpmerkingRequired($scope.urenActiviteiten);
				
				// Set paging
				if ($scope.uren.length > 0) {
					$scope.startItem = 0;
				}
				$scope.totalItems = $scope.uren.length; // Get
									// initial
									// total
									// records
				$scope.totalRecords = $scope.uren.length; // Get
									    // total
									    // unfiltered
									    // records
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
		
	}
	
	// Function to add a new record
	$scope.insert = function(index) {
		$scope.spinner = true;
		
		if ($scope.editForm.$valid) {
			$scope.form.akkoord = null;
			$scope.form.uren = (((new Date('1970-01-01T' + $scope.form.eind)) - (new Date('1970-01-01T' + $scope.form.start))) / 3600000).toFixed(2);
			var urenActiviteitIndex = $scope.urenActiviteiten[getValueById($scope.urenActiviteiten,$scope.form.activiteit_id)];
			$scope.form.activiteit = urenActiviteitIndex.activiteit;
			$scope.form.groep_id = urenActiviteitIndex.groep_id;
			$scope.form.groep = urenActiviteitIndex.groep;
			$scope.form.rol = $scope.urenRollen[getValueById($scope.urenRollen,$scope.form.rol_id)].rol;
			
			if ($scope.form.edit === true) {
				// The true value is set in the $scope.edit
				// function
				$http({
					method : 'PUT',
					url : 'rest/urenboeken.php/' + $scope.form.id,
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messagelocal = response.data.message;
						$scope.spinner = false;
					} else {
						response.data.uren[0].datum = new Date(response.data.uren[0].datum);
						$scope.uren[index] = response.data.uren[0];
						
						$scope.totalItems = $scope.uren.length; // Get
											// initial
											// total
											// records
						$scope.totalRecords = $scope.uren.length; // Get
											    // total
											    // unfiltered
											    // records
						
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.messagelocal = response.data.message;
					$scope.spinner = false;
				});
				
			} else {
				$http({
					method : 'POST',
					url : 'rest/urenboeken.php',
					data : $scope.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messagelocal = response.data.message;
						$scope.spinner = false;
					} else {
						response.data.uren[0].datum = new Date(response.data.uren[0].datum);
						$scope.uren.push(response.data.uren[0]);

						$scope.totalItems = $scope.uren.length; // Get
											// initial
											// total
											// records
						$scope.totalRecords = $scope.uren.length; // Get
											    // total
											    // unfiltered
											    // records
						
						$scope.closeEditModal();
					}
				}, function(response) {
					$scope.messagelocal = response.data.message;
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
	$scope.deleteUur = function(index) {
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/urenboeken.php/' + $scope.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.messagelocal = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.uren.splice(index,1);
				$scope.totalItems = $scope.uren.length; // Get
									// initial
									// total
									// records
				$scope.totalRecords = $scope.uren.length; // Get
									    // total
									    // unfiltered
									    // records
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
	};

	// ------------------------------------------------------------
	// Form handing

	// Function to reset the form for a new record
	$scope.new = function() {
		$scope.form = {}; // Destroy the current form, if any
		$scope.form.username = $scope.self.username;
		$scope.original = angular.copy($scope.form); // Copy the
								// current form
								// to the
								// temporary
								// original
								// object
		
		$scope.startItem = ($scope.numberOfPages() - 1) * $scope.itemsPerPage
	}
	
	// Function to fill the form with the record to be editted
	$scope.edit = function(index) {
		$scope.form = angular.copy($scope.uren[index]); // Copy the
								// object row to
								// the temporary
								// form object
		$scope.form.index = index; // Store the index of the original
					    // record
		$scope.form.edit = true; // Set the edit variable to true,
					    // needed in the $scope.insert
					    // function
		$scope.original = angular.copy($scope.form); // Copy the
								// current form
								// to the
								// temporary
								// original
								// object
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
	
	// Function to reset search and recalculate total items
	$scope.resetSearch = function() {
		$scope.search = '';
		$scope.totalItems = $filter('filter')($scope.uren,$scope.search).length;
	}

	// ------------------------------------------------------------
	// Helper functions
	
	// Helper function to find an id in an array
	function getValueById(array,id) {
		var l = array.length;
		for (var k=0; k<l; k++) {
			if (array[k].id===id) {
				return k;
			}
		}
		return false;
	}
	
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

	// Helper to find activiteit_id that has opmerking required
	$scope.getOpmerkingRequired = function(activiteiten) {
		var i = activiteiten.length;
		for (var k = 0; k < i; k++) {
 			if (activiteiten[k].opmerkingVerplicht) {
				return activiteiten[k].id;
			}
		}
		return null;
	}
	
	// First load own data, refresh when load is complete
	$scope.loadOwn();
	
});	
