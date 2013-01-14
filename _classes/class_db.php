<?
/**
* @package CORE
* @version: class_db.php,v 0.4 2004/08/12 clatko
*/
/**
* I am the main Data Access Object
* All DB access is done through the class.
* @package CORE
* @access public
*/
class class_db {
/*********************************************************
PROPERTIES
**********************************************************/
	/**
	* I am the host location of the MySQL DB (I come from sitewide cfg)
	* @var string
	* @access private
	*/
	private $host;
	/**
	* I am the MySQL DB used for the site (I come from sitewide cfg)
	* @var string
	* @access private
	*/
	private $db;
	/**
	* I am the user that connects to the MySQL DB (I come from sitewide cfg)
	* @var string
	* @access private
	*/
	private $user;
	/**
	* I am the password for that MySQL user (I come from sitewide cfg)
	* @var string
	* @access private
	*/
	private $pass;
	/**
	* I am the connection resource
	* @var object
	* @access private
	*/
	private $mysqliObj;
/*********************************************************
CONSTRUCTOR/DESTRUCTOR
**********************************************************/
	/**
	* class_data constructor
	* @access public
	*/
	function __construct($host='',$db='',$user='',$pass='') {
		if($host=='') { $host = DB_HOST; }
		if($db=='') { $db = DB_DB; }
		if($user=='') { $user = DB_USER; }
		if($pass=='') { $pass = DB_PASSWORD; }
		
		$this->host=$host;
		$this->db=$db;
		$this->user=$user;
		$this->pass=$pass;
		
		$this->dbConnect();
	}

	/**
	* class_data shutdown
	* @access public
	*/
	function __destruct() {
		$this->mysqliObj->close();
	}
/*********************************************************
PUBLIC
**********************************************************/
	/**
	* Queries the MySQL database with the $query param
	* @param string query to use against DB
	* @param boolean if the query should only return affected row count
	* @return mixed result resource or int of affected rows
	* @access public
	*/
	public function query($query,$affected=false){
		$this->mysqliObj->query('SET CHARACTER SET \'utf8\'');
		$this->mysqliObj->query('SET collation_connection = \'utf8_general_ci\'');
		if(!$resultObj = $this->mysqliObj->query($query)) {
			$error = 'Query Error: '.$this->mysqliObj->error."\n".'SQL: '.$query;
			echo '<strong style="color:#900;">We&rsquo;re sorry, an SQL error has occurred. An admin has been notified.</strong>';
			echo $error.'<br/>';
//			$this->sendError($error);
			die();
		}
		if($affected) {
			if(is_object($resultObj)) {
				return $resultObj->affected_rows;
			} else {
				return $resultObj;
			}
		} else {
			return $resultObj;
		}
	}

	/**
	* Returns array
	* @param string query to use against DB
	* @return array
	* @access public
	*/
	public function fetchArray($query) {
		if(!$resultObj = $this->mysqliObj->query($query)) {
			$error = 'Query Error: '.$this->mysqliObj->error."\n".'SQL: '.$query;
			echo '<strong style="color:#900;">We&rsquo;re sorry, an SQL error has occurred. An admin has been notified.</strong>';
			$this->sendError($error);
			die();
		}

		if(mysqli_num_rows($resultObj)) {
			return $data = $resultObj->fetch_assoc();
		} else {
			return false;
		}
	}
	
	/**
	* Returns single cell
	* @param string query to use against DB
	* @return string
	* @access public
	*/
	public function fetchQuery($query) {
		if(!$resultObj = $this->mysqliObj->query($query)) {
			$error = 'Query Error: '.$this->mysqliObj->error."\n".'SQL: '.$query;
			echo '<strong style="color:#900;">We&rsquo;re sorry, an SQL error has occurred. An admin has been notified.</strong>';
			$this->sendError($error);
			die();
		}

		if(mysqli_num_rows($resultObj)) {
			$data = $resultObj->fetch_array();
			return $data[0];
		} else {
			return false;
		}
	}
	

	/**
	* Generates a comma separated list of specified field of resultset
	* @param resource resultset resource
	* @param string field to generate list from
	* @return string list from resultset
	* @access public
	*/
	public function getList($resultObj,$field) {
		if(mysqli_num_rows($resultObj)) {
			$resultObj->data_seek(0);
			$returnList='';
			while($data = $resultObj->fetch_assoc()) {
				$returnList=ListAppend($returnList,$data[$field]);
			}
			return $returnList;
		} else {
			return '';
		}
	}

	/**
	* Returns ID of last insert
	* @return int ID
	* @access public
	*/
	public function returnID() {
		return $this->mysqliObj->insert_id;
	}

	/**
	* Escapes data before being entered into DB
	* @param string string to escape
	* @return string escaped string
	* @access public
	*/
	public function escapeData($string) {
		if(get_magic_quotes_gpc()) {
			return $string;
		} else { // need to fix
			return $this->mysqliObj->real_escape_string($string);
		}
	}

/*********************************************************
PRIVATE
**********************************************************/
	/**
	* Establishes the DB connection and stores the resource in this->mysqliObj
	* @return void
	* @access private
	*/
	private function dbConnect() {
		$this->mysqliObj = new mysqli($this->host,$this->user,$this->pass,$this->db);
		
		if(mysqli_connect_errno()) {
			$error = 'Connection failed: '.mysqli_connect_error();
			echo '<strong style="color:#900;">We&rsquo;re sorry, an SQL error has occurred. An admin has been notified.</strong>';
			$this->sendError($error);
			die();
		} else {
			$this->query("SET CHARACTER SET 'utf8';");
			$this->query("SET collation_connection = 'utf8_general_ci';");
		}
	}

	/**
	* Sends SQL errors to site admin
	* @param string error
	* @return void
	* @access private
	*/
	private function sendError($error) {
		if(SITE_DEBUGMODE) {
			echo '<strong style="color:#900;">'.$error.'</strong>';
			return;
		}
		
		$from_name = EMAIL_ADMINNAME;
		$from_email = EMAIL_ADMINEMAIL;
		$to_email = EMAIL_ADMINRECEIVER;
		$subject = '[SQL Error]:  ('.date('c',time()).')';

		// content
		$content = $error;
		$content .= '
POST:
'.implode("\n",$_POST).'

GET:
'.implode("\n",$_GET).'

COOKIE:
'.implode("\n",$_COOKIE).'

SERVER:
'.$_SERVER.'

SESSION:
'.$_SESSION;

		// prep headers
		$generic_header='From: "'.$from_name.'" <'.$from_email.'>'."\n";
		$generic_header.='Reply-To: "'.$from_name.'" <'.$from_email.'>'."\n";
		$generic_header.='Return-path: "'.$from_name.'" <'.$from_email.'>'."\n";
		$header = 'Content-type: text/plain; charset=iso-8859-1'."\n".$generic_header;
		
		mail($to_email,$subject,trim($content),$header,'-f '.$from_email);
	}

}
?>
