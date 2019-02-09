<?php
/**
 * Modal helpModel | modals/urenregistratie_help_modal.php
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
				Deze pagina geeft het overzicht van alle geregistreerde uren binnen
				de certificerings periode voor de ingelogde gebruiker.<br /> <br />
				Dit scherm is geoptimaliseerd voor verschillende devices. Op een
				telefoon worden minder kolommen getoond als op een tablet. Op een PC
				of laptop worden alle kolommen getoond.<br /> <br /> Uren worden
				geregistreerd door op de knop 'nieuwe' te drukken. Kies vervolgens
				een activiteit en rol. Hierna kan één datum worden opgegeven en
				dient een start en eindtijd opgegeven te worden. De starttijd dient
				voor de eindtijd te liggen, en beide tijden dienen op dezelfde dag
				geregistreerd te worden. Ter verduidelijking voor de goedkeurder kan
				een opmerking worden meegegeven.<br /> <br /> Nieuwe uren worden ter
				goedkeuring aangeboden aan een goedkeurder. Zo lang uren niet zijn
				goedgekeurd kunnen zij nog gewijzigd worden. Eenmaal goedgekeurde
				uren kunnen niet gewijzigd worden.<br /> <br /> Uren welke zijn
				afgekeurd krijgen een rode achtergrond en de opmerking 'nee' in de
				kolom 'Akkoord'. De regel kan een opmerking bevatten van de
				goedkeurder met de reden van afkeuren. De regel kan worden aangepast
				waarna hij opnieuw ter goedkeuring wordt aangeboden. De opmerking
				'nee' en de rode achtergrond kleur verdwijnen dan.<br /> <br /> De
				knop 'Ververs tabel' haalt alle data opnieuw op. Dit kan nodig zijn
				wanneer de gegevens niet meer actueel zijn.<br /> <br /> De knop
				'Toon filter' laat boven de kolommen een filter zien. Hiermee zijn
				specifieke waarden in de betreffende kolom te filteren. Indien een
				filter gezet is kan met de knop 'Reset filter' het filter
				uitgeschakeld worden. De filter opties zelf kunnen via de knop
				'Verberg filter' worden verborgen.<br /> <br /> Door op de naam van
				de kolom te klikken wordt deze kolom gesorteerd. Via het symbool dat
				dan verschijnt rechts naast de kolom naam kan de volgorde van
				sortering worden aangepast.<br /> <br /> De zoek functie maakt het
				mogelijk vrij te zoeken in alle kolommen. Door het zoek venster leeg
				te maken worden alle regels weer getoond. <br /> <br />
				<button class="btn btn-primary center-block" data-dismiss="modal">Sluiten</button>
			</div>
		</div>
	</div>
</div>
