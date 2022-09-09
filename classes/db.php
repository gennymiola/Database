<?php
class db {
//proprietà protected delle classi che non sono accessibili dall'esterno, a cui posso accedere tramite i metodi della classe
    protected $connection;
	protected $query;
    protected $show_errors = TRUE;
    protected $query_closed = TRUE;
    //accessibile anche dall'esterno con public
	public $query_count = 0;
// inizializza dei valori di default
	public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8') {
		// connessione al db
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($this->connection->connect_error) {
			$this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
		}
		$this->connection->set_charset($charset);
	}
//funzione che esegue la query
    public function query($query) {
    	//se c'è già una query aperta prima la chiude
        if (!$this->query_closed) {
            $this->query->close();
        }
        //prepara la query all'esecuzione (controllo + assegnamento)
		if ($this->query = $this->connection->prepare($query)) {
			//controlla il numero dia rgomenti della funzione
            if (func_num_args() > 1) {
            	//PRENDE GLI ARGOMENTI
                $x = func_get_args();
                $args = array_slice($x, 1);
				$types = '';
				//li mette in un array
                $args_ref = array();
                foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);
							$args_ref[] = &$a;
						}
					} else {
	                	$types .= $this->_gettype($args[$k]);
	                    $args_ref[] = &$arg;
					}
                }
                //bind param della query
				array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }
            //esegue la query
            $this->query->execute();
           	if ($this->query->errno) {
				$this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);
           	}
            $this->query_closed = FALSE;
			$this->query_count++;
        } else {
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }
		return $this;
    }

//funzione che salva i record della query in un array di array associativi
	public function fetchAll($callback = null) {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            //associazione chiave valore
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            //richiama se stesso x ciclare i risultati della query
            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break') break;
            } else {
            	//ritorna array associativo
                $result[] = $r;
            }
        }
        //chiude la query
        $this->query->close();
        $this->query_closed = TRUE;
		return $result;
	}
//funzione che salva il record di una query in un array e crea arreay associativo
	public function fetchArray() {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
		while ($this->query->fetch()) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}
        $this->query->close();
        $this->query_closed = TRUE;
		return $result;
	}
//chiude la connessione
	public function close() {
		return $this->connection->close();
	}
//conta le righe
    public function numRows() {
		$this->query->store_result();
		return $this->query->num_rows;
	}
//ritorna quante righe sono state modificate dopo un update
	public function affectedRows() {
		return $this->query->affected_rows;
	}
// nella query con isert ritorna l'id incrementale
    public function lastInsertID() {
    	return $this->connection->insert_id;
    }
//mostra gli errori
    public function error($error) {
        if ($this->show_errors) {
            exit($error);
        }
    }
	public function escape($string) {
		//primo parametro è la connessione al database di cui fa l'escape, secono parametro è la stringa di cui fa l'escape a cui si aggiunge anche l'htmlspecialchars
        return mysqli_real_escape_string($this->connection, htmlspecialchars($string));
    }
	private function _gettype($var) {
	    if (is_string($var)) return 's';
	    if (is_float($var)) return 'd';
	    if (is_int($var)) return 'i';
	    return 'b';
	}

}
?>