/**
 * goedkeurders admin application
 *
 * Functies voor goedkeurders admin pagina
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
 * @since      File available since Release 1.0.6
 * @version    1.0.7
 */
 
// --------------------------------------------------------------------
// Custom App

angular.module('myApp')
.filter('groupBy', ['$parse', function ($parse) {
	return function (list, group_by) {

		var filtered = [];
		var prev_item = null;
		var group_changed = false;
		// this is a new field which is added to each item where we append "_CHANGED"
		// to indicate a field change in the list
		//was var new_field = group_by + '_CHANGED'; - JB 12/17/2013
		var new_field = 'group_by_CHANGED';

		// loop through each item in the list
		angular.forEach(list, function (item) {

			group_changed = false;

			// if not the first item
			if (prev_item !== null) {

				// check if any of the group by field changed

				//force group_by into Array
				group_by = angular.isArray(group_by) ? group_by : [group_by];

				//check each group by parameter
				for (var i = 0, len = group_by.length; i < len; i++) {
					if ($parse(group_by[i])(prev_item) !== $parse(group_by[i])(item)) {
						group_changed = true;
					}
				}


			}// otherwise we have the first item in the list which is new
			else {
				group_changed = true;
			}

			// if the group changed, then add a new field to the item
			// to indicate this
			if (group_changed) {
				item[new_field] = true;
			} else {
				item[new_field] = false;
			}

			filtered.push(item);
			prev_item = item;

		});

		return filtered;
	};
}])
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


	// Function load all required data
	$scope.load = function() {
		
		$scope.spinner = true;
						
		// Load uren
		$http({
			method : 'GET',
			url : 'rest/goedkeurders.php/',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
			} else {
				$scope.users = response.data.users;
				$scope.groepen = response.data.groepen;
				$scope.rollen = response.data.rollen;
				$scope.goedkeurders = response.data.goedkeurders;
				
				if (angular.isArray(response.data.goedkeurders)) {
					$scope.startItem = 0; // Set start item to first
				} else {
					$scope.startItem = -1; // Set start item to first
				}
				
				// Set paging
				$scope.totalItems = $scope.goedkeurders.length; // Get initial total records
				$scope.totalRecords = $scope.goedkeurders.length; // Get total unfiltered records
			}
		}, function() {
			$scope.message = 'Er is iets fout gegaan, probeer opnieuw';
			$scope.spinner = false;
		});

		$scope.spinner = false;
	}

	$scope.goedkeurders = '';
	$scope.spinner = true;
	
	
	
	// ------------------------------------------------------------
	// Form handing
	
	$scope.form = null; // Container for data within the form during edit
	$scope.original = null; // Container to store the original data before starting editing in form
					
	// Function to add a new record
	$scope.insert = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$('#editrecord').modal('hide'); // Close the modal
		
		var edit = $scope.form.edit;
		
		if (!$scope.form.lastname) {
			var l = $scope.users.length;
			for (var k=0; k<l; k++) {
				if ($scope.users[k].username === $scope.form.username) {
					$scope.form.firstname = $scope.users[k].firstname;
					$scope.form.lastname = $scope.users[k].lastname;
				}
			}
		}
		
		if ($scope.editForm.$valid) {
			
			// Now we can post the user and all his urenboeken data. The service is a combined insert or update

			$scope.spinner = true;
			$http({
				method : 'POST',
				url : 'rest/goedkeurders.php',
				data : $scope.form,
				headers : { 'Content-Type': 'application/json' }
			}).then(function(response) {
				$scope.message = '';
				if (response.data.message) {
					$scope.message = response.data.message;
				} else {
					// Update the local goedkeurders object
					if (edit === true) {
						$scope.goedkeurders[index] = response.data;
					} else {
						$scope.goedkeurders.push(response.data);
					}
					$scope.totalItems = $scope.goedkeurders.length; // Get initial total records
					$scope.totalRecords = $scope.goedkeurders.length; // Get total unfiltered records
				}
			}, function() {
				$scope.message = 'Request mislukt';
			});
			
			// Destroy temporary objects
			$scope.form = null;
			$scope.original = null;
			$scope.spinner = false;
		}
	};

	// Function to delete a single row based on index
	$scope.delete = function(index) {
		// Insert code to backend here - Delete record
		$http({
			method : 'DELETE',
			url : 'rest/goedkeurders.php/' + $scope.goedkeurders[index].username,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
					
			$scope.message = '';
			$scope.spinner = false;
	
			if (!response.data.success) {
				$scope.message = response.data.message;
				$scope.spinner = false;
						
			} else {
				$scope.goedkeurders.splice(index,1);
				
				$scope.totalItems = $scope.goedkeurders.length; // Get initial total records
				$scope.totalRecords = $scope.goedkeurders.length; // Get total unfiltered records
				
				$scope.spinner = false;
			}
		}, function() {
			$scope.message = 'Request mislukt';
			$scope.spinner = false;
		});
		
		
	};

	// Function to reset the form for a new record
	$scope.new = function() {
		$scope.form = {}; // Destroy the current form, if any
		$scope.form.groepen = [];
		$scope.form.rollen = [];
		$scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
	}
	
	// Function to fill the form with the record to be editted
	$scope.edit = function(index) {
		
		$scope.form = angular.copy($scope.goedkeurders[index]); 
        $scope.form.index = index; 
        $scope.form.edit = true; 
        
		$scope.form.fullname = $scope.form.firstname.concat(' ', $scope.form.lastname, ' (', $scope.form.username, ')');
        $scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
	}
	

	
	$scope.toggleGroep = function(groepId) {
		var idx = $scope.form.groepen.indexOf(groepId);
		
		// Is currently selected
		if (idx > -1) {
			$scope.form.groepen.splice(idx,1);

		
		// Is newly selected
		} else {
			$scope.form.groepen.push(groepId);
		}                    
		
	}
	
	// Function to reset the form to its original state
	$scope.reset = function() {
		$scope.$broadcast('show-errors-reset');
		
		$scope.form = angular.copy($scope.original); // Copy the temporary original object back to the current form
	}

	// ------------------------------------------------------------
	// Sort functions
	$scope.sortType = ''; // Field to be sorted
	$scope.sortReverse = false; // Reverse sort order
	
	// ------------------------------------------------------------
	// Configurable options
	$scope.tableListOptions = [10, 25, 100]; // Predefined options for page lengths
	$scope.itemsPerPage = 10; // Initial page length
	
	
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

	// Function to recalculate total items
	$scope.onSearch = function() {
		$scope.totalItems = $filter('filter')($scope.users,$scope.search).length;
	};

	$scope.refresh = function() {
		$scope.load();
	}
	
	$scope.refresh();
	
});
