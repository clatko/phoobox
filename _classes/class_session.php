<?
/**
* @package CORE
* @version: class_session.php,v 0.4 2004/08/12 clatko
*/
/**
* All user related tasks are done through this class.
* @package CORE
* @access public
*/
class class_session {
/*********************************************************
PROPERTIES
**********************************************************/
	/**
	* I am the session identifies
	* @var string
	* @access private
	*/
	private $cookieID;
/*********************************************************
CONSTRUCTOR/DESTRUCTOR
**********************************************************/
	/**
	* class_session constructor
	* @access public
	*/
	function __construct() {
		session_name('XXX'); // same as class_auth
		session_start();

		header('Cache-control: private');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');				// Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');	// always modified
		header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP_1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');										// HTTP_1.0

		$this->cookieID = (isset($_COOKIE['XXX'])) ? $_COOKIE['XXX']: '';
	}
/*********************************************************
PUBLIC METHODS
**********************************************************/
	/**
	* Inserts new session value unless it already exists
	* @param string name of session property
	* @param string value of session property
	* @return void
	* @access public
	*/
	public function insertValue($name,$value) {
		$_SESSION[$name]=$value;
	}

	/**
	* Sets session value if it exists
	* @param string name of session property
	* @param string value of session property
	* @return void
	* @access public
	*/
	public function setValue($name,$value) {
		if(isset($_SESSION[$name])) {
			$_SESSION[$name]=$value;
		}
	}

	/**
	* Gets session value
	* @param string name of session property to retrieve
	* @return mixed returns value of session property if it exists
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
	* Deletes session value
	* @param string name of session property to delete
	* @return mixed returns false if delete fails
	* @access public
	*/
	public function delValue($name) {
		unset($_SESSION[$name]);
	}

	/**
	* Drops a bomb on session (when user logs out)
	* @return void
	* @access public
	*/
	public function destroy() {
		$_SESSION = array();
		unset($_SESSION);
		unset($_COOKIE['XXX']);
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
