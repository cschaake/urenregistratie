/**
 * Configuratie app
 *
 * AngularJS application for configuratie pagina
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
 * @since      File available since Release 1.0.6
 * @version    1.0.9
 */
// --------------------------------------------------------------------
// Custom App

angular.module('myApp')

// Filter to display akkoord
.filter('boolFilter', function() {
	var output;
	return function(input) {
		if (input === true) {
			output = 'ja';
		} else {
			output = 'nee';
		}
		
		return output;
	}
})
.controller('configuratieCtrl',function($scope,$filter,$http) {

	// ------------------------------------------------------------
	// Initiate Form handing
	$scope.groepen = {};
	$scope.groepen.form = null; 
	$scope.groepen.original = null; 
	
	$scope.rollen = {};
	$scope.rollen.form = null; 
	$scope.rollen.original = null; 
	
	$scope.certificaten = {};
	$scope.certificaten.form = null; 
	$scope.certificaten.original = null; 
	
	$scope.activiteiten = {};
	$scope.activiteiten.form = null; 
	$scope.activiteiten.original = null; 
	
	// ------------------------------------------------------------
	// Initiate sort
	$scope.groepen.sortType = ''; // Field to be sorted
	$scope.groepen.sortReverse = false; // Reverse sort order
	
	$scope.loadConfiguratie = function() {
		$scope.spinner = true;
		
		// Load Configuratie
		$http({
			method : 'GET',
			url : 'rest/configuratie.php',
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (response.data.message) {
				// Error message was returned
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				// We got a good response
				$scope.groepen = response.data.groepen;
				$scope.rollen = response.data.rollen;
				$scope.activiteiten = response.data.activiteiten;
				$scope.certificaten = response.data.certificaten;
				$scope.spinner = false;
			}
		}, function() {
			// Call failed with http error
			if (response.data.message) {
				$scope.message = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.message = 'Er is iets fout gegaan, probeer opnieuw';
				$scope.spinner = false;
			}
		});
	}
	
	// Function to refresh the table data (retrieve data again from backend)
	$scope.refresh = function() {
		$scope.loadConfiguratie();
	}

	$scope.reset = function() {
		$scope.$broadcast('show-errors-reset');
		
		$scope.messageGroepen = '';
		$scope.messageRollen = '';
		$scope.messageCertificaten = '';
		$scope.messageActiviteiten = '';
		
		$scope.groepen.form = angular.copy($scope.groepen.original);
		$scope.rollen.form = angular.copy($scope.rollen.original);
		$scope.certificaten.form = angular.copy($scope.certificaten.original);
		$scope.activiteiten.form = angular.copy($scope.activiteiten.original);
	}
	
	$scope.new = function() {
		$scope.$broadcast('show-errors-reset');
		$scope.groepen.form = null;
		$scope.rollen.form = null;
		$scope.certificaten.form = null;
		$scope.activiteiten.form = null;
	}
	
	// Function fot editing groep
	$scope.editgroep = function(index) {
		$scope.groepen.form = angular.copy($scope.groepen[index]); 
        $scope.groepen.form.index = index; 
        $scope.groepen.form.edit = true; 
                    
        $scope.groepen.original = angular.copy($scope.groepen.form); // Copy the current form to the temporary original object
	}
	
	// Function to add a new record
	$scope.insertgroep = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		if ($scope.editgroepForm.$valid) {
			
			if ($scope.groepen.form.edit === true) {
				// The true value is set in the $scope.edit function
				
				$http({
					method : 'PUT',
					url : 'rest/groepen.php/' + $scope.groepen.form.id,
					data : $scope.groepen.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					$scope.message = '';
					if (response.data.message) {
						$scope.messageGroepen = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.groepen[index] = response.data.groepen[0];
						// Destroy temporary objects
						$scope.groepen.form = null;
						$scope.groepen.original = null;
						$('#editgroep').modal('hide'); // Close the modal
						$scope.spinner = false;
					}
				}, function(response) {
					$scope.messageGroepen = response.data.message;
					$scope.spinner = false;
				});
				
			} else {
				
				$http({
					method : 'POST',
					url : 'rest/groepen.php',
					data : $scope.groepen.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					$scope.message = '';
					if (response.data.message) {
						$scope.messageGroepen = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.groepen.push(response.data.groepen[0]);
						// Destroy temporary objects
						$scope.groepen.form = null;
						$scope.groepen.original = null;
						$('#editgroep').modal('hide'); // Close the modal
						$scope.spinner = false;
					}
				}, function(response) {
					$scope.messageGroepen = response.data.message;
					$scope.spinner = false;
				});
				
			}
						
			
		}
	};
	
	// Function to delete a single row based on index
	$scope.deletegroep = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/groepen.php/' + $scope.groepen.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.messageGroepen = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.groepen.splice(index,1);
				// Destroy temporary objects
				$scope.groepen.form = null;
				$scope.groepen.original = null;
				$('#deletegroep').modal('hide'); // Close the modal
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.messageGroepen = response.data.message;
			$scope.spinner = false;
		});
	};

	// Function fot editing rol
	$scope.editrol = function(index) {
		$scope.rollen.form = angular.copy($scope.rollen[index]); 
        $scope.rollen.form.index = index; 
        $scope.rollen.form.edit = true; 
        $scope.rollen.original = angular.copy($scope.rollen.form); // Copy the current form to the temporary original object
	}
	
	// Function to add a new rol
	$scope.insertrol = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		if ($scope.editrolForm.$valid) {
			
			if ($scope.rollen.form.edit === true) {
				// The true value is set in the $scope.edit function
				$http({
					method : 'PUT',
					url : 'rest/rollen.php/' + $scope.rollen.form.id,
					data : $scope.rollen.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messageRollen = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.rollen[index] = response.data.rollen[0];
						$scope.rollen.form = null;
						$scope.rollen.original = null;
						$('#editrol').modal('hide'); // Close the modal
						$scope.spinner = false;
					}
				}, function(response) {
					$scope.messageRollen = response.data.message;
					$scope.spinner = false;
				});
			} else {
				$http({
					method : 'POST',
					url : 'rest/rollen.php',
					data : $scope.rollen.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messageRollen = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.rollen.push(response.data.rollen[0]);
						$scope.rollen.form = null;
						$scope.rollen.original = null;
						$('#editrol').modal('hide'); // Close the modal
						$scope.spinner = false;
					}
				}, function(response) {
					$scope.messageRollen = response.data.message;
					$scope.spinner = false;
				});
			}
		}
	};
	
	// Function to delete a single row based on index
	$scope.deleterol = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/rollen.php/' + $scope.rollen.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.messageRollen = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.rollen.splice(index,1);
				$scope.groepen.form = null;
				$scope.groepen.original = null;
				$('#deleterol').modal('hide'); // Close the modal
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.messageRollen = response.data.message;
			$scope.spinner = false;
		});
	};
	
	// Function for certificaat editing 
	$scope.editcertificaat = function(index) {

	$scope.certificaten.form = angular.copy($scope.certificaten[index]); 
        $scope.certificaten.form.index = index; 
        $scope.certificaten.form.edit = true; 

        $scope.certificaten.original = angular.copy($scope.certificaten.form); // Copy the current form to the temporary original object
	}
	
	// Function to add a new certificaat
	$scope.insertcertificaat = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		if ($scope.editcertificaatForm.$valid) {
			if ($scope.certificaten.form.edit === true) {
				// The true value is set in the $scope.edit function
				
				$http({
					method : 'PUT',
					url : 'rest/certificaten.php/' + $scope.certificaten.form.id,
					data : $scope.certificaten.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					$scope.message = '';
					if (response.data.message) {
						$scope.messageCertificaten = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.certificaten[index] = response.data.certificaten[0];
						$scope.certificaten.form = null;
						$scope.certificaten.original = null;
						$scope.spinner = false;
						$('#editcertificaat').modal('hide'); // Close the modal
					}
				}, function(response) {
					$scope.messageCertificaten = response.data.message;
					$scope.spinner = false;
				});
			} else {
				$http({
					method : 'POST',
					url : 'rest/certificaten.php',
					data : $scope.certificaten.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					$scope.message = '';
					if (response.data.message) {
						$scope.messageCertificaten = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.certificaten.push(response.data.certificaten[0]);
						$scope.certificaten.form = null;
						$scope.certificaten.original = null;
						$scope.spinner = false;
						$('#editcertificaat').modal('hide'); // Close the modal
					}
				}, function(response) {
					$scope.messageCertificaten = response.data.message;
					$scope.spinner = false;
				});
			}
		}
	};
	
	// Function to delete a single row based on index
	$scope.deletecertificaat = function(index) {
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/certificaten.php/' + $scope.certificaten.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			$scope.message = '';
			if (!response.data.success) {
				$scope.messageCertificaten = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.certificaten.splice(index,1);
				$scope.certificaten.form = null;
				$scope.certificaten.original = null;
				$scope.spinner = false;
				$('#deletecertificaat').modal('hide'); // Close the modal
			}
		}, function(response) {
			$scope.messageCertificaten = response.data.message;
			$scope.spinner = false;
		});
	};
	
	// Function for activiteit editing 
	$scope.editactiviteit = function(index) {
	$scope.activiteiten.form = angular.copy($scope.activiteiten[index]); 
        $scope.activiteiten.form.index = index; 
        $scope.activiteiten.form.edit = true; 
        $scope.activiteiten.original = angular.copy($scope.activiteiten.form); // Copy the current form to the temporary original object
	}
	
	// Function to add a new record
	$scope.insertactiviteit = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		if ($scope.editactiviteitForm.$valid) {
			
			if ($scope.activiteiten.form.edit === true) {
				// The true value is set in the $scope.edit function
				$http({
					method : 'PUT',
					url : 'rest/activiteiten.php/' + $scope.activiteiten.form.id,
					data : $scope.activiteiten.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messageActiviteiten = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.activiteiten[index] = response.data.activiteiten[0];
						$scope.activiteiten.form = null;
						$scope.activiteiten.original = null;
						$scope.spinner = false;
						$('#editactiviteit').modal('hide'); // Close the modal
					}
				}, function(response) {
					$scope.messageActiviteiten = response.data.message;
					$scope.spinner = false;
				});
				
			} else {
				
				$http({
					method : 'POST',
					url : 'rest/activiteiten.php',
					data : $scope.activiteiten.form,
					headers : { 'Content-Type': 'application/json' }
				}).then(function(response) {
					if (response.data.message) {
						$scope.messageActiviteiten = response.data.message;
						$scope.spinner = false;
					} else {
						$scope.activiteiten.push(response.data.activiteiten[0]);
						$scope.activiteiten.form = null;
						$scope.activiteiten.original = null;
						$scope.spinner = false;
						$('#editactiviteit').modal('hide'); // Close the modal
					}
				}, function(response) {
					$scope.messageActiviteiten = response.data.message;
					$scope.spinner = false;
				});
				
			}
		}
	};
	
	// Function to delete a single row based on index
	$scope.deleteactiviteit = function(index) {
		$scope.$broadcast('show-errors-check-validity');
		$scope.$broadcast('show-errors-reset');
		$scope.spinner = true;
		
		$http({
			method : 'DELETE',
			url : 'rest/activiteiten.php/' + $scope.activiteiten.form.id,
			headers : { 'Content-Type': 'application/json' }
		}).then(function(response) {
			if (!response.data.success) {
				$scope.messageActiviteiten = response.data.message;
				$scope.spinner = false;
			} else {
				$scope.activiteiten.splice(index,1);
				$scope.activiteiten.form = null;
				$scope.activiteiten.original = null;
				$('#deleteactiviteit').modal('hide'); // Close the modal
				$scope.spinner = false;
			}
		}, function(response) {
			$scope.messageActiviteiten = response.data.message;
			$scope.spinner = false;
		});
	};
	
	$scope.refresh();
	
});
