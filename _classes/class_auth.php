<?
/**
* @package CORE
* @version: class_auth.php,v 0.4 2004/08/11 clatko
*/
/**
* The session auth for logged in users...
* @package CORE
* @access public
*/
class class_auth {
/*********************************************************
PROPERTIES
**********************************************************/
	/**
	* I am the core Data Access Object
	* @var object
	* @access private
	*/
	private $dbObj;
	/**
	* Location to redirect to on a login success.
	* @var string
	* @access private
	*/
	private $success;
	/**
	* Location to redirect to on a login failure.
	* @var string
	* @access private
	*/
	private $failure;
	/**
	* Hashkey for the md5 encryption - currently this is always '*******'
	* @var string
	* @access private
	*/
	private $hashkey;
	/**
	* If we should md5 the password in the session scope. Should always be true.
	* @var boolean
	* @access private
	*/
	private $md5;
/*********************************************************
CONSTRUCTOR/DESTRUCTOR
**********************************************************/
	/**
	* class_auth constructor
	* @param object database connector
	* @param string location to redirect on login success.
	* @param string location to redirect on login failure.
	* @param string hashkey for md5 encryption.
	* @param boolean if we should md5 the stored password.
	* @access public
	*/
	function __construct(class_db $dbObj,$success,$failure,$hashkey,$md5) {
		session_name('XXX');
		session_start();
		$this->dbObj = $dbObj;
		$this->success = $success;
		$this->failure = $failure;
		$this->hashkey = $hashkey;
		$this->md5 = $md5;
	}
/*********************************************************
PUBLIC METHODS
**********************************************************/
////////////////////////////// SET/GET METHODS START
	/**
	* Gets specified name
	* @param string name to get
	* @return mixed value of the name
	* @access public
	*/
	public function getValue($name) {
		if(isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return false;
		}
	}

	/**
	* Sets specified name
	* @param string name to set
	* @param string value to set name to
	* @return void
	* @access public
	*/
	public function setValue($name,$value) {
		$_SESSION[$name] = $value;
	}

	/**
	* Deletes session value
	* @param string name of session name to delete
	* @return mixed returns false if delete fails
	* @access public
	*/
	public function delValue($name) {
		if(isset($_SESSION[$name])) {
			unset($_SESSION[$name]);
		} else {
			return false;
		}
	}
////////////////////////////// SET/GET METHODS END


////////////////////////////// CORE METHODS START
	/**
	* Checks email address for duplicates
	* @param string email
	* @return boolean if email already exists
	* @access public
	*/
	public function checkEmail($email) {
		$resultObj = $this->dbObj->checkEmail($email);
		if(mysqli_num_rows($resultObj)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Checks username for duplicates
	* @param string username
	* @return boolean if username already exists
	* @access public
	*/
	public function checkUsername($username) {
		$resultObj = $this->dbObj->checkUsername($username);
		if(mysqli_num_rows($resultObj)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Validates old password when updating your account
	* @param string userID
	* @param string password
	* @return boolean if password checks out
	* @access public
	*/
	public function checkPassword($userID,$password) {
		$resultObj = $this->dbObj->checkPassword($userID,$password);
		if(mysqli_num_rows($resultObj)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Checks if the user has proper credentials
	* @param boolean if the user should be redirected if login fails
	* @return mixed true if user passes, redirects if not
	* @access public
	*/
	public function checkStatus($redirect=true,$location=false) {
		if(!$location) {
			$location = $this->failure;
		}
		if($this->getValue('userID') && $this->getValue('userID') > 0) {
			if ($this->getValue('timestamp') && (date('U') - $this->getValue('timestamp')) > EXPIREINSECONDS) {
				$this->logout();
			} else {
				$this->setValue('timestamp',date('U'));
				return true;
			}
		} else {
			if($redirect) {
				$this->redirect($location,true);
			} else {
				return false;
			}
		}
	}

	/**
	* Checks if the user is in the proper role
	* @param array possible roles
	* @return mixed true if user passes
	* @access public
	*/
	public function checkRole($array) {
		$roles = $this->getValue('role');

		foreach($array as $v) {
			if(ListFind($roles,trim($v))) {
				return true;
			}
		}
		
		return false;
	}

	/**
	* Checks email and password against the DB
	* @param string email
	* @param string password
	* @return mixed redirects user to $this->success or $this->failure
	* @access public
	*/
	public function login($email,$password,$redirect=true,$success='',$failure='') {
		$success = ($success=='') ? $this->success: $success;
		$failure = ($failure=='') ? $this->failure: $failure;
		if($email=='' || $password=='') {
			$this->recordFailure($email,$password);
			if($redirect) { $this->redirect($failure); }
		}

		$password = ($this->md5) ? md5($password) : $password;

		$resultObj = $this->dbObj->login($email,$password,true);
		$data = $resultObj->fetch_array();

		if($data['num_users']!=1) {
			$resultObj = $this->dbObj->login($email,$password,false); // try again with looser restrictions
			$data = $resultObj->fetch_array();
			if($data['num_users']!=1) {
				$this->setValue('error','Email/Password is invalid!');
				$this->recordFailure($email,$password);
				if($redirect) { $this->redirect($failure); }
			} else {
				$this->storeAuth($data['agentID'],'Agent',$data['name'],'',$data['email'],$data['password'],'agent');
				$this->setValue('timestamp',date('U'));
	
				if($redirect) { $this->redirect($success); }
			}
		} else {
			// get account properties
			$resultObj = $this->dbObj->getProps($data['userID']);
			while($prop_row = $resultObj->fetch_assoc()) {
				if(ListFind('first_name,last_name',$prop_row['propkey'])) {
					$$prop_row['propkey'] = trim($prop_row['propvalue']);
				}
			}

			$this->storeAuth($data['userID'],$data['username'],$first_name,$last_name,$email,$password,$data['role']);
			$this->setValue('timestamp',date('U'));

			if($redirect) { $this->redirect($success); }
		}
	}

	/**
	* Logs user out of the system
	* @param boolean (optional) if we should redirect
	* @param string (optional) location of redirect
	* @return void
	* @access public
	*/
	public function logout($redirect=true,$location='failure') {
		$this->delValue('userID');
		$this->delValue('name');
		$this->delValue('email');
		$this->delValue('password');
		$this->delValue('login_hash');
		$this->delValue('timestamp');
		$_SESSION = array();
		session_destroy();
		if($redirect) {
			if($location=='failure') {
				$location = $this->failure;
			}
			$this->redirect($location,true);
		}
	}
////////////////////////////// CORE METHODS END


// STORING CURRENTLY VIEWED PROPERTY IN SESSION
	/**
	* Sets view property in view array in $_SESSION
	* @param string name - property name
	* @param string value - property value
	* @return void
	* @access public
	*/
	public function setPropertyView($name,$value) {
		if(!$view = $this->getValue('view')) {
			$view = array();
		}

		$view[$name] = $value;

		$this->setValue('view',$view);
	}

	/**
	* Confirms user credentials
	* @return void
	* @access private
	* @todo shouldn't this be used instead of checkStatus?
	*/
	public function confirmAuth() {
		$userID = $this->getValue('userID');
		$username = $this->getValue('username');
		$email = $this->getValue('email');
		$password = $this->getValue('password');
		$hashkey = $this->getValue('login_hash');
		if(md5($this->hashkey.$userID.$username.$email.$password)!=$hashkey) {
			$this->logout(true);
		}
	}
/*********************************************************
PRIVATE METHODS
**********************************************************/
	/**
	* Stores authentication credentials
	* @param string userID
	* @param string username
	* @param string first_name
	* @param string last_name
	* @param string email
	* @param string password
	* @return void
	* @access private
	*/
	private function storeAuth($userID,$username,$first_name,$last_name,$email,$password,$role) {
		$this->setValue('userID',$userID);
		$this->setValue('username',$username);
		$this->setValue('name',$first_name.' '.$last_name);
		$this->setValue('email',$email);
		$this->setValue('password',$password);
		$this->setValue('role',$role);
		$hashkey = md5($this->hashkey.$userID.$username.$email.$password);
		$this->setValue('login_hash',$hashkey);
	}

	/**
	* Records login failure
	* @return void
	* @access private
	*/
	private function recordFailure($email,$password) {
		$query = 'INSERT INTO `leech_failure`
			(`email`,`password`,`date`)
			VALUES
			(\''.$this->dbObj->escapeData($email).'\',\''.$this->dbObj->escapeData($password).'\',\''.time().'\')';
		$this->dbObj->query($query);
	}

	/**
	* Redirects the user to success/failure/from pages
	* @return void
	* @access private
	*/
	private function redirect($location) {
		header('Location: '.$location);
		exit();
	}
/*********************************************************
DEBUG
**********************************************************/
	/**
	* Object dumper
	* @return void
	* @access public
	*/
	public function dumpObj() {
		echo '<pre class="debug">';
		print_r($_SESSION);
		echo '</pre>';
	}
}
?>
