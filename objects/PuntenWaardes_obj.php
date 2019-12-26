<?php
/**
 * Class PuntenWaardes | objects/PuntenWaardes_obj.php
 *
 * Bevat array van object PuntenWaardes. 
 * De array bevat de waarde van een punt per bepaalde startdatum
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
 * @copyright  2020 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @since      File available since Release 1.2.2
 * @version    1.2.2
 */

/**
 * Required files
 */
require_once ('PuntenWaarde_obj.php');

/**
 * Class PuntenWaardes - Array van PuntenWaarde
 *
 * Bevat een array van PuntenWaarde objecten.
 *
 * @since Object available since Release 1.2.2
 * @version 1.2.2
 */ 
 
class PuntenWaardes
{
    /**
     * Array met PuntenWaarde objecten
     *
     * @var PuntenWaarde[]
     * @access public
     */
    public $puntenwaardes;
    
    /**
     * Mysqli object
     *
     * @var mysqli
     * @access private
     */
    private $mysqli;
    
    /**
     * Method constructor - Creeer punten object
     *
     * @access public
     * @param mysqli $mysqli
     * @throws Exception
     * @return bool Success flag
     */
    public function __construct(mysqli $mysqli)
    {
        if (! is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object', 500);
        } else {
            $this->mysqli = $mysqli;
        }
        
        return true;
    }
    
    /**
     * Method read - Lees punten waarde uit database
     *
     * @access public
     * @throws Exception
     * @return bool Success flag
     *
     * @var int $id
     * @var string $datumVanaf
     * @var int $waarde
     * @var string $prep_stmt
     * @var mysqli_stmt $stmt
     */
    
    public function read()
    {
        $prep_stmt = null;
        $stmt = null;
        
        $id = null;
        $datumVanaf = null;
        $waarde = null;
     
        $prep_stmt = null;
        
        $prep_stmt = "
            SELECT
                ura_puntenwaardes.id,
                ura_puntenwaardes.datumVanaf,
                ura_puntenwaardes.waarde
            FROM
                ura_puntenwaardes
            ORDER BY ura_puntenwaardes.datumVanaf DESC"; // Descending sorting vanwege getPuntenWaarde formule
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->execute();
            $stmt->store_result();
            
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($id, $datumVanaf, $waarde);
                while ($stmt->fetch()) {
                    $puntenwaarde_obj = new PuntenWaarde($id, $datumVanaf, $waarde);
                    $this->puntenwaardes[] = $puntenwaarde_obj;
                }
            } elseif ($stmt->num_rows == 0) {
                $stmt->close();
                throw new Exception('Geen puntenwaarde record gevonden', 404);
            } else {
                $stmt->close();
                throw new Exception('Fout bij opvragen puntenwaarde', 500);
            }
            
            $stmt->close();
        } else {
            throw new Exception('Database error', 500);
        }
        
    }
    
    /**
     * Method getPuntenWaarde - Lees punten waarde uit voor bepaalde datum
     *
     * @access public
     * @param string $datum
     * @throws Exception
     * @return int Punten Waarde
     */
     
    public function getPuntenWaarde(string $datum)
    {
        foreach ($this->puntenwaardes as $puntenwaarde) {
         
            if (strtotime($datum) >= strtotime($puntenwaarde->datumVanaf)) {
                
                return $puntenwaarde->waarde;
            } 
        }
        
        return 0;
    }
}