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
     * Rollen.
     *
     * @var array rollen
     * @access public
     */
    public $rollen;
    
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
     * Opbouw en afbouw uren.
     *
     * @var bool Configuratie parameter of opbouw en afbouw uren worden meegerekend
     * @access public
     */
    public $opbouw;
    
    /**
     * Activity without date
     *
     * @var bool Configuratie parameter dat activiteit tijdloos is
     * @access public
     */
    public $nodate;
    
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
     * @param bool $opbouw Opbouw en afbouw uren
     *
     * @return bool Succes vlag
     */
    public function __construct($id, $datum, $begintijd, $eindtijd, $activiteit, $rollen, $groep_id, $groep = null, $opmerkingVerplicht = false, $opbouw = false)
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
        $this->rollen = $rollen; //@TODO juiste filter toepassen
        $this->groep_id = (int) filter_var($groep_id, FILTER_SANITIZE_STRING);
        $this->groep = filter_var($groep, FILTER_SANITIZE_STRING, FILTER_CUSTOM);
        $this->opmerkingVerplicht = (bool) $opmerkingVerplicht;
        $this->opbouw = (bool) $opbouw;
        
        // Database return 1970-01-01 for nulified date records. So we need te reset them to null 
        if ($this->datum < date('Y-m-d', strtotime('2000-01-01'))) {
            $this->datum = null;
            $this->begintijd = null;
            $this->eindtijd = null;
            $this->nodate = true;
        }

        return true;
    }
    
    /**
     * Insert of update rollen voor huidige activiteit
     * Maak records aan in activiteitrol tabel
     *
     * @param mysqli $mysqli MySQL object
     * @return bool Succes vlag
     */
    public function upsertRollen($mysqli) {
        //Verwijder eerst eventuele activiteit-rol koppelingen
        if ($this->deleteRollen($mysqli)) {
            //Loop over alle rollen
            foreach($this->rollen as $rol) {
                //Maak record aan
                $this->_insertRol($mysqli, $rol);
            }
        } else {
            throw new Exception('Error deleting activiteitrol', 500);
        }
        return true;
    }
    
    /**
     * Insert activiteitrol
     * Insert record in activiteitrol tabel
     *
     * @param mysqli $mysqli MySQL object
     * @param int $rol Rol id van huidige rol
     *
     * @return bool Succes vlag
     */
    private function _insertRol($mysqli, $rol) {
        $prep_stmt = null;
        $stmt = null;
        
        $prep_stmt = "
            INSERT INTO ura_activiteitrol (
                activiteit_id,
                rol_id )
            VALUES (?, ?)";
        
        $stmt = $mysqli->prepare($prep_stmt);
        if ($stmt) {
            $stmt->bind_param('ii', $this->id, $rol);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->affected_rows < 1) {
                $stmt->close();
                throw new Exception('Fout bij updaten activiteit', 500);
            }
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        return true;
    }
    
    /**
     * Verwijder rollen voor huidige activiteit
     * Verwijder records uit activiteitrol tabel
     * 
     * @param mysqli $mysqli MySQL object
     *
     * @return bool Succes vlag
     */
    public function deleteRollen($mysqli) {
        $prep_stmt = null;
        $stmt = null;
        
        $prep_stmt = "
            DELETE FROM
                ura_activiteitrol
            WHERE
                activiteit_id = ?";
        
        $stmt = $mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            $stmt->store_result();
            
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        return true;
    }
}
