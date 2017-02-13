<?php
/**
 * users Object
 *
 * Object voor users tabel
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
 * @since      File available since Release 1.0.0
 * @version    1.0.8
 */
 
/**
 * Rollen object
 * 
 * @package    Urenverantwoording
 * @author     Christiaan Schaake <chris@schaake.nu>
 * @copyright  2017 Schaake.nu
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * 
 * @since      Class available since Release 1.0.0
 */ 
class Users {
    
    /**
     * id required with length of 5 characters
     * 
     * @var integer
     * @access private
     */
    private $id;

    /**
     * rol required with length of 30 characters
     * 
     * @var string
     * @access private
     */
    private $rol;

    /**
     * Create the activtiteiten object
     *
     * Creates the activiteiten object that will contain all activiteiten stuff
     *
     * @param mysqli $mysqli Valid mysqli object
     *
     * @return bool Success flag
     */
    public function __construct($mysqli) {
        if (!is_a($mysqli, 'mysqli')) {
            throw new Exception('$mysqli is not a valid mysqli object');
        } else {
            $this->mysqli = $mysqli;
        }
        return true;
    }
    
    /**
     * Get all boekers
     *
     * @throws Exception
     * @return NULL|unknown[][]
     */
    public function getBoekers() {
        $prep_stmt = "
            SELECT 
                ura_urenboeken.username, users.firstName, users.lastName
            FROM
                ura_urenboeken
                    LEFT JOIN
                 users ON ura_urenboeken.username = users.username 
            GROUP BY users.username
            ";
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname);
                
                $rollen = array();
                
                while ($stmt->fetch()) {
                    $rollen[] = ['username'=>$username, 'firstname'=>$firstname, 'lastname'=>$lastname];
                }
            } elseif ($stmt->num_rows == 0) {
                $rollen = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
            
        } else {
            throw new Exception('Database error');
        }
        
        $stmt->close();
        return $rollen;
    }
    
    /**
     * Get a single user
     *
     * @throws Exception
     * @return NULL|unknown[][]
     */
    public function get() {
        $prep_stmt = "
            SELECT 
                users.username, users.firstName, users.lastName
            FROM
                users
            ";
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname);
                
                $rollen = array();
                
                while ($stmt->fetch()) {
                    $rollen[] = ['username'=>$username, 'firstname'=>$firstname, 'lastname'=>$lastname];
                }
            } elseif ($stmt->num_rows == 0) {
                $rollen = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
            
        } else {
            throw new Exception('Database error');
        }
        
        $stmt->close();
        return $rollen;
    }
    
    /**
     * Get a single boeker
     *
     * @param string $username
     * @throws Exception
     * @return NULL|unknown[]|unknown[][]|unknown[][][]
     */
    public function getBoeker($username) {
        $prep_stmt = "
            SELECT 
                users.username,
                users.firstName,
                users.lastName,
                ura_urenboeken.groep_id,
                ura_urenboeken.rol_id,
                ura_rollen.rol,
                ura_certificaat.gecertificeerd,
                ura_certificaat.verloopt
            FROM
                users
                    LEFT JOIN
                ura_urenboeken ON ura_urenboeken.username = users.username
                    LEFT JOIN
                ura_rollen ON ura_urenboeken.rol_id = ura_rollen.id
                    LEFT JOIN
                ura_certificaat ON ura_certificaat.rol_id = ura_urenboeken.rol_id
                    AND ura_certificaat.username = ura_urenboeken.username
            WHERE
                users.username = ?
            ";
        
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('s',$username);
            
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname, $groep_id, $rol_id, $rol, $gecertificeerd, $verloopt);
                
                $rollen = array();
                $groepen = array();
                
                
                while ($stmt->fetch()) {
                    if ($rol_id) {
                        $rollen[] = ['rol_id'=>$rol_id,  'rol'=>$rol, 'gecertificeerd'=>$gecertificeerd, 'verloopt'=>$verloopt];
                    }
                    
                    if ($groep_id) {
                        $groepen[] = $groep_id;
                    }
                }
                $user = ['username'=>$username, 'firstname'=>$firstname, 'lastname'=>$lastname, 'groepen'=>$groepen, 'rollen'=>$rollen];
            } elseif ($stmt->num_rows == 0) {
                $user = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
            
        } else {
            throw new Exception('Database error');
        }
        
        $stmt->close();
        return $user;
    }
    
	
    /**
     * Set a single boeker
     *
     * @param array $boeker
     * @throws Exception
     */
    public function setBoeker($boeker) {
        
        // Delete all records
        $prep_stmt = 'DELETE FROM ura_urenboeken WHERE username = ?';
        $stmt = $this->mysqli->prepare($prep_stmt);
        if ($stmt) {
            $stmt->bind_param('s',$boeker['username']);
            
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows >= 0) {
                $user = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
        } else {
            throw new Exception('Database error');
        }
        $stmt->close();
        
        $prep_stmt = 'DELETE FROM ura_certificaat WHERE username = ?';
        $stmt = $this->mysqli->prepare($prep_stmt);
        if ($stmt) {
            $stmt->bind_param('s',$boeker['username']);
            
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows >= 0) {
                $user = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
        } else {
            throw new Exception('Database error');
        }
        $stmt->close();

        if (isset($boeker['rol'])) {
            foreach($boeker['rol'] as $rol) {
                // Insert records
                $prep_stmt = 'INSERT INTO ura_urenboeken (username, rol_id, groep_id) VALUES (?, ?, ?)';
                $stmt = $this->mysqli->prepare($prep_stmt);
                if ($stmt) {
                    $stmt->bind_param('sii',$boeker['username'], $rol['rol_id'], $rol['groep_id']);
                    
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows >= 0) {
                        $user = null;
                    } else {
                        $stmt->close();
                        throw new Exception('No users found');
                    }
                } else {
                    throw new Exception('Database error');
                }
                $stmt->close();
                
                // Insert records
                $prep_stmt = 'INSERT INTO ura_certificaat (username, rol_id, gecertificeerd, verloopt, groep_id) VALUES (?, ?, ?, ?, ?)';
                $stmt = $this->mysqli->prepare($prep_stmt);
                if ($stmt) {
                    $stmt->bind_param('sissi',$boeker['username'], $rol['rol_id'], $rol['gecertificeerd'], $rol['verloopt'], $rol['groep_id']);
                    
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows >= 0) {
                        $user = null;
                    } else {
                        $stmt->close();
                        throw new Exception('No users found');
                    }
                } else {
                    throw new Exception('Database error');
                }
                $stmt->close();
            }
        } else {
	        // Insert records
			$prep_stmt = 'INSERT INTO ura_urenboeken (username) VALUES (?)';
			$stmt = $this->mysqli->prepare($prep_stmt);
			if ($stmt) {
				$stmt->bind_param('s',$boeker['username']);
					
				$stmt->execute();
				$stmt->store_result();
				
				if ($stmt->num_rows >= 0) {
					$user = null;
				} else {
					$stmt->close();
					throw new Exception('No users found');
				}
			} else {
				throw new Exception('Database error');
			}
			$stmt->close();
		}
        
    }

    /**
     * Delete a single boeker
     *
     * @param string $username
     * @throws Exception
     * @return boolean
     */
    public function deleteBoeker($username) {
        
        // Delete all records
        $prep_stmt = 'DELETE FROM ura_urenboeken WHERE username = ?';
        $stmt = $this->mysqli->prepare($prep_stmt);
        if ($stmt) {
            $stmt->bind_param('s',$username);
            
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows >= 0) {
                $user = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
        } else {
            throw new Exception('Database error');
        }
        $stmt->close();
        
        $prep_stmt = 'DELETE FROM ura_certificaat WHERE username = ?';
        $stmt = $this->mysqli->prepare($prep_stmt);
        if ($stmt) {
            $stmt->bind_param('s',$username);
            
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows >= 0) {
                $user = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
        } else {
            throw new Exception('Database error');
        }
        $stmt->close();
        
        return true;
    }
    
    /**
     * Get all goedkeurders
     *
     * @throws Exception
     * @return NULL|unknown[][]
     */
    public function getGoedkeurders() {
        $prep_stmt = "
            SELECT 
                users.username, users.firstName, users.lastName
            FROM
                users
                    LEFT JOIN
                ura_urengoedkeuren ON users.username = ura_urengoedkeuren.username
            WHERE
                ura_urengoedkeuren.groep_id > 0
            GROUP BY users.username";
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows >= 1) {
                $stmt->bind_result($username, $firstname, $lastname);
                
                $rollen = array();
                
                while ($stmt->fetch()) {
                    $rollen[] = ['username'=>$username, 'firstname'=>$firstname, 'lastname'=>$lastname];
                }
            } elseif ($stmt->num_rows == 0) {
                $rollen = null;
            } else {
                $stmt->close();
                throw new Exception('No users found');
            }
            
        } else {
            throw new Exception('Database error');
        }
        
        $stmt->close();
        return $rollen;
    }
	
	/**
     * Controlleer of gebruiker kan goedkeuren
     *
	 * @param  string	 $username	Username
     * @throws Exception  				
     * @return bool       			Resultaat (true is kan goedkeuren)
     */
	public function kanGoedkeuren($username) {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        
        $prep_stmt = "
            SELECT DISTINCT
                username
            FROM
                ura_urengoedkeuren
            WHERE
                username = ?";
                
        $stmt = $this->mysqli->prepare($prep_stmt);
        
        if ($stmt) {
            $stmt->bind_param('s', $username);
            
            $stmt->execute();   
            $stmt->store_result();
            
            $result = ($stmt->affected_rows >= 1);
            $stmt->close();    
        } else {
            throw new Exception('Database error');
        }
		
		return $result;
    }
}
