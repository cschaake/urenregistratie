/**
 * User app
 *
 * AngularJS application for users pagina
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
 * @since      File available since Release 1.0.0
 * @version    1.2.1
 */
 
angular.module('myApp')
.filter('filterStatus', function() {
    return function(status) {
        switch(status) {
            case 0:
                return 'Nieuw';
            case 1:
                return 'Actief';
            case 2:
                return 'Gelocked';
            case 3:
                return 'Verwijderd';
            default:
                return 'Onbekend';
        }
    };
})
.controller('usersCtrl',function($scope,$filter,$http) {
        $scope.spinner = true;
        
        $scope.message = null;
        $scope.users = null;
        
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
        
        // Get all user information
        $http.get("authenticate.php")
        .then(function(response) {
            $scope.users = response.data.users;
            // Use cascading load to avoid timing problems
            $http.get("authenticate.php/groups")
            .then(function(response) {
                $scope.groups = response.data;
                // ------------------------------------------------------------
                // Configurable options
                $scope.tableListOptions = [10, 25, 100]; // Predefined options for page lengths
                $scope.itemsPerPage = 10; // Initial page length
                
                // Non configurable options
                $scope.startItem = 0; // Set start item to first
                $scope.totalItems = $scope.users.length; // Get initial total records
                $scope.totalRecords = $scope.users.length; // Get total unfiltered records
                $scope.spinner = false;
            });
        });        
        
        $scope.statusses = [
            {id: 0, name: 'Nieuw'},
            {id: 1, name: 'Actief'},
            {id: 2, name: 'Gelocked'},
            {id: 3, name: 'Verwijderd'}
        ];
        
        // ------------------------------------------------------------
        // Sort functions
        $scope.sortType = ''; // Field to be sorted
        $scope.sortReverse = false; // Reverse sort order
    
        // Function to recalculate total items
        $scope.onSearch = function() {
            $scope.totalItems = $filter('filter')($scope.users,$scope.search).length;
        };
        
        // Function to refresh the table data (retrieve data again from backend)
        $scope.refresh = function() {
            $scope.spinner = true;
            
            $http.get("authenticate.php")
            .then(function(response) {
                $scope.users = response.data;
                
                $scope.spinner = false;
            });
            $scope.message = null;
            $scope.totalItems = $scope.users.length; // Get initial total records
            $scope.totalRecords = $scope.users.length; // Get total unfiltered records    
        }
        
        // Function to delete a single row based on index
        $scope.delete = function(index) {
            $scope.spinner = true;
            $http({
                method : 'DELETE',
                url : 'authenticate.php/' + $scope.users[index].username,
                headers : { 'Content-Type': 'application/json' }
            }).then(function(response) {

                $scope.message = '';
                $scope.spinner = false;
            
                if (!response.data.success) {
                    $scope.message = response.data.message;
                } 
            }, function() {
                $scope.refresh();
            });
            
            $scope.users.splice(index,1);

            $scope.totalItems = $scope.users.length; // Get initial total records
            $scope.totalRecords = $scope.users.length; // Get total unfiltered records

        };
                
        // Function to add a new record
        $scope.insert = function(index) {
            $scope.$broadcast('show-errors-check-validity');
            $scope.$broadcast('show-errors-reset');

            if ($scope.editForm.$valid) {
                
                if ($scope.form.edit === true) {
                    // We need to edit an existing record
                    // The true value is set in the $scope.edit function
                    $scope.spinner = true;
        
                    $http({
                        method : 'PUT',
                        url : 'authenticate.php/' + $scope.form.username,
                        data : $scope.form,
                        headers : { 'Content-Type': 'application/json' }
                    }).then(function(response) {
            
                        $scope.message = '';
                        $scope.spinner = false;
            
                        if (!response.data.success) {
                            $scope.message = response.data.message;
                
                        } else {
                            $('#editrecord').modal('hide'); // Close the modal
                            // Update local data
                            $scope.users[index] = angular.copy($scope.form);
                            // Destroy temp stuff
                            $scope.form = null;
                            $scope.original = null;
                        }
                    }, function(response) {
                        $scope.message = response.data.message;
                    });
                    
                    
                } else {
                    // We need to insert a new record
                    $scope.spinner = true;
                    $scope.form.status = 0;
        
                    $http({
                        method : 'POST',
                        url : 'authenticate.php/register',
                        data : $scope.form,
                        headers : { 'Content-Type': 'application/json' }
                    }).then(function(response) {
            
                        $scope.message = '';
                        $scope.spinner = false;
            
                        if (!response.data.success) {
                            $scope.message = response.data.message;
                
                        } else {
                            $('#editrecord').modal('hide'); // Close the modal
                            // Update local data
                            $scope.users.push($scope.form);
                            // Destroy temporary objects
                            $scope.form = null;
                            $scope.original = null;
                        }
                    }, function(response) {
                        $scope.message = response.data.message;
                    })
                }
                
                $scope.totalItems = $scope.users.length; // Get initial total records
                $scope.totalRecords = $scope.users.length; // Get total unfiltered records
            }
        }
                                
        // Function to reset the form for a new record
        $scope.new = function() {
            $scope.form = null; // Destroy the current form, if any
            $scope.original = null; // Destroy any original object
                    
            $scope.startItem = ($scope.numberOfPages() - 1) * $scope.itemsPerPage
        }
                
        // Function to fill the form with the record to be editted
        $scope.edit = function(index) {
            $scope.form = angular.copy($scope.users[index]); // Copy the object row to the temporary form object
            $scope.form.index = index; // Store the index of the original record
            $scope.form.edit = true; // Set the edit variable to true, needed in the $scope.insert function
                    
            $scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
        }
                
        // Function to reset the form to its original state
        $scope.reset = function() {
            $scope.$broadcast('show-errors-reset');
            $scope.$broadcast('show-errors-check-validity');
                    
            $scope.form = angular.copy($scope.original); // Copy the temporary original object back to the current form
        }


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
        
        $scope.loadConfig();

});
