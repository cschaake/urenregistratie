<?php
/**
 * Class Activiteit | objects/Activiteit_obj.php
 *
 * Bevat object Activiteit. Op een activiteit kunnen uren geboekt worden.
 * Een activiteit heeft een datum en begin- en eindtijd.
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
 * @since      File available since Release 1.0.6
 * @version    1.2.0
 */

/**
 * Class Activiteit - Enkele activiteit
 * 
 * Bevat een activiteit welke gepersisteerd is of wordt in de database.
 *
 * @since Object available since Release 1.0.0
 * @version 1.2.0
 */
class Activiteit
{

    /**
     * id van de activiteit verplicht numeriek 5 lang.
     *
     * @var int Id van de activiteit
     * @access public
     */
    public $id;

    /**
     * Datum van de activiteit.
     *
     * @var string Datum van activiteit
     * @access public
     */
    public $datum;
    
    /**
     * Begintijd van de activiteit.
     *
     * @var string Begintijd van activiteit
     * @access public
     */
    public $begintijd;
    
    /**
     * Eindtijd van activiteit.
     *
     * @var string Eindtijd van activiteit
     * @access public
     */
    public $eindtijd;
    
    /**
     * Naam van de activiteit verplicht alfanumeriek 30 lang.
     *
     * @var string Naam van de activiteit
     * @access public
     */
    public $activiteit;

    /**
     * Group_id verplicht numeriek 5 lang (link naar ura_groepen tabel).
     *
     * @var int Groep id
     * @access public
     */
    public $groep_id;

    /**
     * Naam van de groep (uit ura_groepen tabel).
     *
     * @var string Name van de groep
     * @access public
     */
    public $groep;

    /**
     * Opmerking verplicht.
     *
     * @var bool Configuratie parameter of opmerking verplicht is bij boeken van deze activiteit
     * @access public
     */
    public $opmerkingVerplicht;

    /**
     * Method constructor - Creeer activtiteit object.
     *
     * @param int $id Id van de activiteit
     * @param string $datum Datum waarop activiteit plaats vindt
     * @param string $begintijd Begin tijd van activiteit (dit is exclusief voorbereidingstijd)
     * @param string $eindtijd Eind tijd van acitviteit (dit is exlcusief opruimtijd)
     * @param string $activiteit naam van de activiteit
     * @param int $groep_id Id van de groep
     * @param string $groep Optioneel naam van de groep
     * @param bool $opmerkingVerplicht Opmerking verplicht
     *
     * @return bool Succes vlag
     */
    public function __construct($id, $datum, $begintijd, $eindtijd, $activiteit, $groep_id, $groep = null, $opmerkingVerplicht = null)
    {
        // Sanitize input
        if ($id) {
            $this->id = (int) filter_var($id, FILTER_SANITIZE_STRING);
        } else {
            $this->id = null;
        }
        $this->datum = date('Y-m-d', strtotime($datum));
        $this->begintijd = date('H:i', strtotime($begintijd));
        $this->eindtijd = date('H:i', strtotime($eindtijd));
        $this->activiteit = filter_var($activiteit, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
        $this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->opmerkingVerplicht = (bool) $opmerkingVerplicht;

        return true;
    }
}
