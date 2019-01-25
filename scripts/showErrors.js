/**
 * Error handling
 *
 * Handle form errors
 *
 * Angular JS version 1.4.7
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/mit-license.html  MIT License.  
 * If you did not receive a copy of the MIT License and are unable to 
 * obtain it through the web, please send a note to license@php.net so 
 * we can mail you a copy immediately.
 *
 * @package    authenticate
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.0.0
 * @version    1.0.7
 */
(function() {
	var showErrorsModule;

	showErrorsModule = angular.module('ui.bootstrap.showErrors', []);

	showErrorsModule.directive('showErrors', [
		'$timeout', 'showErrorsConfig', '$interpolate', function($timeout, showErrorsConfig, $interpolate) {
      
				var getShowSuccess, getTrigger, linkFn;
		
				getTrigger = function(options) {
					var trigger;
					trigger = showErrorsConfig.trigger;
					if (options && (options.trigger != null)) {
						trigger = options.trigger;
					}
					return trigger;
				}
    
				getShowSuccess = function(options) {
					var showSuccess;
					showSuccess = showErrorsConfig.showSuccess;
					if (options && (options.showSuccess != null)) {
						showSuccess = options.showSuccess;
					}
					return showSuccess;
				}
      
				linkFn = function(scope, el, attrs, formCtrl) {
				var blurred, inputEl, inputName, inputNgEl, options, showSuccess, toggleClasses, trigger;
				blurred = false;
				options = scope.$eval(attrs.showErrors);
				showSuccess = getShowSuccess(options);
				trigger = getTrigger(options);
				inputEl = el[0].querySelector('.form-control[name]');
				inputNgEl = angular.element(inputEl);
				inputName = $interpolate(inputNgEl.attr('name') || '')(scope);
        
				if (!inputName) {
					throw "show-errors element has no child input elements with a 'name' attribute and a 'form-control' class";
				}
				
				inputNgEl.bind(trigger, function() {
					blurred = true;
					return toggleClasses(formCtrl[inputName].$invalid);
				});
		
				scope.$watch(function() {
					return formCtrl[inputName] && formCtrl[inputName].$invalid;
				}, function(invalid) {
					if (!blurred) {
						return;
					}
					return toggleClasses(invalid);
				})
        
				scope.$on('show-errors-check-validity', function() {
					return toggleClasses(formCtrl[inputName].$invalid);
				})
		
				scope.$on('show-errors-reset', function() { // Reset show errors by removing all error and ok stuff
					return $timeout(function() {
						el.removeClass('has-error');
						el.removeClass('has-success');
						el.find('span#error-feedback').remove(); // Remove feedback icon
						el.parent().find('div.alert-danger').remove(); // Remove error description
						return blurred = false;
					}, 0, false);
				})
		
				return toggleClasses = function(invalid) {
					el.toggleClass('has-error', invalid);
          
					el.find('span#error-feedback').remove(); // Remove feedback icon
					el.parent().find('div#desciption_' + inputName).remove(); // Remove error description
					el.find('input').after('<span id="error-feedback" class="glyphicon glyphicon-remove form-control-feedback"></span>');
					el.find('select').after('<span id="error-feedback" style="margin-right : 1em;" class="glyphicon glyphicon-remove 		form-control-feedback"></span>');
					el.find('textarea').after('<span id="error-feedback" class="glyphicon glyphicon-remove form-control-feedback"></span>');
          
					if (el.find('input').attr('errorText')) { // If errortext show alert box
						el.after('<div id="desciption_' + inputName + '" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + el.find('input').attr('errorText') + '</div>');
					}
			
					if (el.find('select').attr('errorText')) { // If errortext show alert box
						el.after('<div id="desciption_' + inputName + '" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + el.find('select').attr('errorText') + '</div>');
					}
			
					if (el.find('textarea').attr('errorText')) { // If errortext show alert box
						el.after('<div id="desciption_' + inputName + '" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + el.find('textarea').attr('errorText') + '</div>');
					}
          
					if (showSuccess) {
						el.toggleClass('has-success', !invalid);
						if (!invalid) {
							el.find('span#error-feedback').remove(); // Remove feedback icon
							el.parent().find('div#desciption_' + inputName).remove(); // Remove error description
							el.find('input').after('<span id="error-feedback" class="glyphicon glyphicon-ok form-control-feedback"></span>');
							el.find('select').after('<span id="error-feedback" style="margin-right : 1em;" class="glyphicon glyphicon-ok form-control-feedback"></span>');
							el.find('textarea').after('<span id="error-feedback" class="glyphicon glyphicon-ok form-control-feedback"></span>');
						}
						return;
					}
				}
			}
			return {
				restrict: 'A',
				require: '^form',
				compile: function(elem, attrs) {
					if ((attrs['showErrors'].indexOf('skipFormGroupCheck') === -1) && 
						(!(elem.hasClass('form-group') || elem.hasClass('input-group')))) {
						throw "show-errors element does not have the 'form-group' or 'input-group' class";
					}
					return linkFn;
				}
			}
		}
	])

	showErrorsModule.provider('showErrorsConfig', function() {
		var _showSuccess, _trigger;
		_showSuccess = false;
		_trigger = 'blur';
		this.showSuccess = function(showSuccess) {
			return _showSuccess = showSuccess;
		}
		this.trigger = function(trigger) {
			return _trigger = trigger;
		}
		this.$get = function() {
			return {
				showSuccess: _showSuccess,
				trigger: _trigger
			}
		}
	})

}).call(this);