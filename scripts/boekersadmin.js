/**
 * boekers admin application
 *
 * Functies voor boekers admin pagina
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
 * @version    1.2.2
 */

//--------------------------------------------------------------------
//Custom App

angular.module('myApp')

//Filter for groupBy
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

//Controller for gebruikers
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

    $scope.uren={};

    $scope.spinner = false;

    $scope.boekers = '';
    
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
	$scope.spinner = true;
	$scope.loadBoekers();
	$scope.spinner = false;
    }

    // ------------------------------------------------------------
    // Data load functions

    $scope.loadBoekers = function() {

	// Load boekers
	$http({
	    method : 'GET',
	    url : 'rest/users.php/boekers/',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;
		$scope.spinner = false;
	    } else {
		$scope.boekers = response.data.boekers;

		if ($scope.boekers.length > 0) {
		    $scope.startItem = 0;
		}

		$scope.totalItems = $scope.boekers.length; 
		$scope.totalRecords = $scope.boekers.length;

		$scope.loadGroepen();
	    }
	}, function(response) {
	    $scope.message =  response.data.message;
	    $scope.spinner = false;
	});
    }	

    $scope.loadGroepen = function() {
	// Load groepen
	$http({
	    method : 'GET',
	    url : 'rest/groepen.php/',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;

	    } else {
		$scope.groepen = response.data;

		$scope.loadRollen();
	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    }

    $scope.loadRollen = function() {
	// Load rollen
	$http({
	    method : 'GET',
	    url : 'rest/rollen.php/',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;

	    } else {
		$scope.rollen = response.data.rollen;
	    }

	    $scope.loadCertificaten();

	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    }

    $scope.loadCertificaten = function() {
	// Load certificaten
	$http({
	    method : 'GET',
	    url : 'rest/certificaten.php',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;

	    } else {
		$scope.certificaten = response.data.certificaten;                                        

		$scope.loadUsers();
	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	}); 
    }

    $scope.loadUsers = function() {
	// Load users
	$http({
	    method : 'GET',
	    url : 'rest/users.php',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;
		$scope.spinner = false;
	    } else {
		$scope.users = response.data.users;                                        
	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    }

    $scope.loadSelf = function() {
	// Load self
	$http({
	    method : 'GET',
	    url : 'authenticate.php/self',
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;
		$scope.spinner = false;
	    } else {
		$scope.self = response.data;

		$scope.refresh();
	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    }

    // ------------------------------------------------------------
    // Form handing

    // Function to reset the form for a new record
    $scope.new = function() {
	$scope.form = {}; // Destroy the current form, if any
	$scope.form.groepen = [];
	$scope.form.rollen = [];
	$scope.original = angular.copy($scope.form); // Copy the current form to the temporary original object
    }

    // Function to fill the form with the record to be editted
    $scope.edit = function(index) {

	$scope.spinner = true;

	// Load boeker
	$http({
	    method : 'GET',
	    url : 'rest/users.php/boekers/' + $scope.boekers[index].username,
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {
	    if (response.data.message) {
		$scope.message = response.data.message;
		$scope.spinner = false;
	    } else {
		$scope.boeker = response.data.boekers[0];

		$scope.form = angular.copy($scope.boeker); 
		$scope.form.edit = true;     
		$scope.form.index = index; 
		$scope.form.fullname = $scope.form.firstname.concat(' ', $scope.form.lastname, ' (', $scope.form.username, ')');

		$scope.original = angular.copy($scope.form); 

		$scope.spinner = false;

	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    }

    // Function to reset the form to its original state
    $scope.reset = function() {
	$scope.$broadcast('show-errors-check-validity');
	$scope.$broadcast('show-errors-reset');
	$scope.message = '';

	$scope.form = angular.copy($scope.original); 
    }

    // Function to add a new record
    $scope.insert = function(index) {
	$scope.$broadcast('show-errors-check-validity');
	$scope.$broadcast('show-errors-reset');
	$scope.message = '';

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
		url : 'rest/users.php/addUrenboeken',
		data : $scope.form,
		headers : { 'Content-Type': 'application/json' }
	    }).then(function(response) {

		$scope.message = '';
		$scope.spinner = false;

		if (response.data.message) {
		    $scope.message = response.data.message;
		    $scope.spinner = false;

		} else {
		    // Update the local boekers object
		    if (edit === true) {
			$scope.boekers[index] = response.data.boekers[0];
		    } else {
			$scope.boekers.push(response.data.boekers[0]);
		    }

		    $scope.totalItems = $scope.boekers.length; // Get initial total records
		    $scope.totalRecords = $scope.boekers.length; // Get total unfiltered records

		    $scope.spinner = false;
		}
	    }, function(response) {
		$scope.message = response.data.message;
		$scope.spinner = false;
	    });

	    // Destroy temporary objects
	    $scope.form = null;
	    $scope.original = null;
	}
    };

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

    $scope.calcCertificaat = function() {

	var l = $scope.certificaten.length;
	for (var k=0; k<l; k++) {

	    if ($scope.certificaten[k].id === $scope.certificaat.id) {
	    	
		if ($scope.certificaat.gecertificeerd) {
		    var jaar = $scope.certificaat.gecertificeerd.getFullYear(); // Current yeara
		    var maand = $scope.certificaat.gecertificeerd.getMonth(); // Current month
		    var dag = $scope.certificaat.gecertificeerd.getDate(); // Current day
		    var addMaanden = $scope.certificaten[k].looptijd;

		    var jaren = Math.floor(addMaanden / 12); // Calculate whole years to be added
		    var maanden = addMaanden - (jaren * 12); // Calculate rest of months to be addded

		    var newMaand = maand + maanden; // Calculate new month (could be larger than 12)
		    var extraJaren = Math.floor(newMaand / 12); // Get the whole years from the newMaand
		    newMaand = newMaand - (extraJaren * 12); // Extract the whole years form the newMaand (now it should be ok)
		    var newJaar = jaar + jaren + extraJaren; // Calculate new year


		    $scope.certificaat.verloopt = $filter('date')(new Date(newJaar, newMaand, dag), "yyyy-MM-dd");
		} else {
		    $scope.certificaat.verloopt = '';
		}

		$scope.certificaat.uren = $scope.certificaten[k].uren;
		$scope.certificaat.groep_id = $scope.certificaten[k].groep_id;
	    }
	}
    }

    // Function to delete a single row based on index
    $scope.removeCertificaat = function(index) {
	$scope.form.rollen.splice(index,1);
    };


    $scope.addCertificaat = function(certificaat) {
	var l = $scope.rollen.length;
	for (var k=0; k<l; k++) {
	    if ($scope.rollen[k].id===certificaat.id) {
		certificaat.rol = $scope.rollen[k].rol;
	    }
	}

	$scope.form.rollen.push(certificaat);

	$('#addcertificaat').modal('hide'); // Close the modal
	$scope.certificaat = null;

    }

    $scope.cancelCertificaat = function() {
	$scope.certificaat = null;
    }

    // Function to delete a single row based on index
    $scope.delete = function(index) {
	// Insert code to backend here - Delete record
	$http({
	    method : 'DELETE',
	    url : 'rest/users.php/boeker/' + $scope.boekers[index].username,
	    headers : { 'Content-Type': 'application/json' }
	}).then(function(response) {

	    $scope.message = '';
	    $scope.spinner = false;

	    if (!response.data.success) {
		$scope.message = response.data.message;
		$scope.spinner = false;

	    } else {
		$scope.boekers.splice(index,1);

		$scope.totalItems = $scope.boekers.length; // Get initial total records
		$scope.totalRecords = $scope.boekers.length; // Get total unfiltered records

		$scope.spinner = false;
	    }
	}, function(response) {
	    $scope.message = response.data.message;
	    $scope.spinner = false;
	});
    };

    // Function to close the edit modal after update or insert
    $scope.closeEditModal = function() {
	$('#editrecord').modal('hide'); // Close the modal
	$scope.$broadcast('show-errors-check-validity');
	$scope.$broadcast('show-errors-reset');
	$scope.message = '';

	$scope.form = null;
	$scope.original = null;
	$scope.spinner = false;
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
	$scope.totalItems = $filter('filter')($scope.users,$scope.search).length;
	if ($scope.totalItems < 1) {
	    $scope.startItem = -1;
	} else {
	    $scope.startItem = 0;
	}
    };

    // Function to reset search and recalculate total items
    $scope.resetSearch = function() {
	$scope.search = '';
	$scope.totalItems = $filter('filter')($scope.users,$scope.search).length;
    }

    $scope.loadConfig();
    $scope.loadSelf();

});
