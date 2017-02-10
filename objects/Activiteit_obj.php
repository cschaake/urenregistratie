<?php
/**
 * Activiteit Object
 *
 * Object voor Activiteit
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
 * @version    1.0.7
 */

/**
 * Activiteit object
 *
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @since      Object available since Release 1.0.0
 */
class Activiteit 
{
    /**
     * id van de activiteit verplicht numeriek 5 lang
     *
     * @var int           Id van de activiteit
     * @access public
     */
    public $id;

    /**
     * Naam van de activiteit verplicht alfanumeriek 30 lang
     *
     * @var string        Naam van de activiteit
     * @access public
     */
    public $activiteit;

    /**
     * Group_id verplicht numeriek 5 lang (link naar ura_groepen tabel)
     *
     * @var int            Groep id
     * @access public
     */
    public $groep_id;

    /**
     * Naam van de groep (uit ura_groepen tabel)
     *
     * @var string        Name van de groep
     * @access public
     */
    public $groep;

    /**
     * Opmerking verplicht
     *
     * @var bool          Configuratie parameter of opmerking verplicht is bij boeken van deze activiteit
     * @access public
     */
    public $opmerkingVerplicht;
	
    /**
     * Creeer activtiteit object
     *
     * @param 	int 	$id			Id van de activiteit
     * @param 	string 	$activiteit	naam van de activiteit
     * @param 	int 	$group_id   Id van de groep
     * @param 	string 	$group      Optioneel naam van de groep
     *
     * @return bool Succes vlag
     */
    public function __construct($id, $activiteit, $groep_id, $groep = null, $opmerkingVerplicht = null) 
	{
		if ($id) {
			$this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
		} else {
			$this->id = null;
		}
		$this->activiteit = filter_var($activiteit, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
		$this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
		$this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
		$this->opmerkingVerplicht = (bool) $opmerkingVerplicht;

        return true;
    }
}
