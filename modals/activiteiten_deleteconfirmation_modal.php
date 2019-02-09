<?php
/**
 * Modal deleterecord | modals/activiteiten_deleteconfirmation_modal.php
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
 * @copyright  2019 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.0
 * @version    1.2.0
 */
?>

<?php
    /**
     * Modal deleterecord
     */
?>
<div id="deleterecord" class="modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Regel verwijderen</h4>
			</div>
			<div class="modal-body">
				<div ng-show="messagelocal" class="alert alert-danger">{{
					messagelocal }}</div>
				<p>
					De activiteit '{{ form.activiteit }}' op {{ form.datum | date:
					"yyyy-MM-dd" }} wordt verwijderd.<br /> Weet u het zeker?
				</p>
				<br />

				<button class="btn btn-danger" ng-click="deleteActiviteit(form.index)">
					Delete</button>

				<button class="btn btn-default" data-dismiss="modal"
					ng-click="reset()">Annuleer</button>
			</div>
		</div>
	</div>
</div>
