/**
 * Rapportage pagina application
 *
 * Functies voor rapportage pagina
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
 * @since      File available since Release 1.0.8
 * @version    1.2.1
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
	// Initiate sort
	$scope.sortType = 'username'; // Field to be sorted
	$scope.sortReverse = false; // Reverse sort order
	
	// ------------------------------------------------------------
	// Initiate paging
	$scope.tableListOptions = [10, 25, 100]; // Predefined options for page lengths
	$scope.itemsPerPage = 10; // Initial page length
	$scope.startItem = -1; // Set start item to first
	$scope.totalItems = 0; // Get initial total records
	$scope.totalRecords = 0; // Get total unfiltered records
	
	$scope.startItemDetails = -1; // Set start item to first
	$scope.totalItemsDetails = 0; // Get initial total records
	$scope.totalRecordsDetails = 0; // Get total unfiltered records

	$scope.rapportage = '';
	$scope.showRapportagePanel = true;
	$scope.showDetailPanel = false;
	
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
		$scope.rapportage();
	}
	
	$scope.refreshDetail = function($username) {
		$scope.loadDetail($username);
	}

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
	
	$scope.rapportage = function() {
		$http({
			method : 'GET',
			url : 'rest/rapportage.php',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (angular.isArray(response.data.records)) {
						$scope.rapport = response.data.records;
				} else {
					$scope.rapport = false;
				}
				if (angular.isArray(response.data.rollen)) {
						$scope.rollen = response.data.rollen;
				} else {
					$scope.rollen = false;
				}
				if (angular.isArray(response.data.activiteiten)) {
						$scope.activiteiten = response.data.activiteiten;
				} else {
					$scope.activiteiten = false;
				}
				
				// Set paging
				if ($scope.rapport.length > 0) {
					$scope.startItem = 0;
				}
				$scope.totalItems = $scope.rapport.length; // Get initial total records
				$scope.totalRecords = $scope.rapport.length; // Get total unfiltered records

				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
	}
	
	$scope.loadDetail = function($username) {
		$http({
			method : 'GET',
			url : 'rest/rapportage.php/' + $username,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				if (angular.isArray(response.data.records)) {
						$scope.details = response.data.records;
				} else {
					$scope.details = false;
				}
				
				// Set paging
				if ($scope.details.length > 0) {
					$scope.startItemDetails = 0;
				}
				$scope.totalItemsDetails = $scope.details.length; // Get initial total records
				$scope.totalRecordsDetails = $scope.details.length; // Get total unfiltered records

				$scope.spinner = false;
			}
		}, function(response) {
			$scope.message = response.data.message;
			$scope.spinner = false;
		});
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
	
	// Second panel
	
	// Determine current page
	$scope.currentPageDetails = function() {
		return Math.ceil($scope.startItemDetails / $scope.itemsPerPage) + 1;
	};
	
	// Determine total number of pages
	$scope.numberOfPagesDetails = function() {
		return Math.ceil($scope.totalItemsDetails / $scope.itemsPerPage);
	};

	// Determine first item on last page
	$scope.lastItemPageDetails = function() {
		if ($scope.startItemDetails + $scope.itemsPerPage > $scope.totalItemsDetails ) {
			return $scope.totalItemsDetails;
		} else {
			return $scope.startItemDetails + $scope.itemsPerPage;
		}
	}

	// Function to create pages list in pagination
	$scope.pagesListDetails = function() {
		var rng = [];
		
		for (var i = 1; i < $scope.numberOfPagesDetails() + 1; i++) {
			rng.push(i);
		}
		return rng;
	};

	// ------------------------------------------------------------
	// Search handing
	
	// Function to recalculate total items
	$scope.onSearch = function() {
		$scope.totalItems = $filter('filter')($scope.rapport,$scope.search).length;
		if ($scope.totalItems < 1) {
			$scope.startItem = -1;
		} else {
			$scope.startItem = 0;
		}
	};
	
	// Function to reset search and recalculate total items
	$scope.resetSearch = function() {
		$scope.search = '';
		$scope.totalItems = $filter('filter')($scope.rapport,$scope.search).length;
	}

	
	// ------------------------------------------------------------
	// Helper functions

	$scope.showDetail = function($username) {
		$scope.showRapportagePanel = false;
		$scope.showDetailPanel = true;
		
		$scope.loadDetail($username);
	}
	
	$scope.showRapport = function() {
		$scope.showRapportagePanel = true;
		$scope.showDetailPanel = false;
	}
	
	$scope.loadConfig();
	
	// First load own data, refresh when load is complete
	$scope.loadOwn();
	
});
