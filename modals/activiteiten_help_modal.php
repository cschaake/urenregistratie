<?php
/**
 * Modal helpModal | modals/activiteiten_help_modal.php
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
     * Modal helpModal
     */
?>
<div id="helpModal" class="modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					<span class="glyphicon glyphicon-question-sign"></span> Help
				</h4>
			</div>

			<div class="modal-body">
				Uren kunnen geboekt worden op een activiteit. Een activiteit is één evenement op één dag. Meerdaagse activiteiten
				bestaan uit meerdere activiteiten, één activiteit per dag. Dit geldt ook voor evenementen welke doorlopen tot na 24:00 uur. <br />
				Een activiteit hoort toe aan één groep.<br /><br />
				Bij het boeken van uren op een activiteit wordt automatisch extra tijd voor voorbereiding en afbouw van de activiteit berekend.
				Deze extra tijd wordt alleen toegekent wanneer de begintijd en/of de eindtijd ongewijzigd blijft. Wanneer men later aan het
				activiteit heeft deelgenomen of eerder is weggegaan dienen de begin- en/of eindtijd aangepast te worden. Dan vervallen automatisch
				ook de betreffende voorbereiding- en afbouw uren.<br/><br />
				Bij het boeken van uren wordt gezocht naar activiteiten die op de aangegeven dag hebben plaatsgevonden. De datum van het activiteit
				is bepalend of er op de bewuste datum geboekt kan worden.<br /><br />
				<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
			</div>
		</div>
	</div>
</div>
