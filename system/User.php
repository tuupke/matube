<?php

class User {

	/** The userId of this user */
	private $userId;

	/** The entityId linked to this user */
	private $entityId;

	/** The entity for this user */
	private $entity;

	/** The username */
	private $username;

	/** The firstname of the user */
	private $firstName;

	/** The middlename of the user */
	private $middleName;

	/** The lastname of the user */
	private $lastName;

	/** The email of the user */
	private $mail;

	/** Is the user loaded */
	private $loaded = false;

	/** The values which should be stored in session */
	private $values = array('username', 'firstName', 'middleName', 'lastName', 'entity', 'mail');

	/** Value which shows whether a user is logged in */
	public $isLoggedIn;

	/**
	 * Constructor for a User
	 * 
	 * @param int $id The identifier for this user
	 * @post All values are loaded
	 */
	public function __construct($id = -1){
		$this->userId = $id;
		if($id >= 0){
			$this->isLoggedIn = $this->loaded = $this->load();
		} else if(session_start()){
			if($this->sessionLoaded()){
				$this->loaded = $this->sessionLoad();

			}
		}
	}

	/**
	 * Destructor for this entity, saves the required information
	 */
	public function __destruct() {
		if(!$this->loaded){
			return;
		}
		$_SESSION['username'] = $this->username;
		$_SESSION['firstName'] = $this->firstName;
		$_SESSION['middleName'] = $this->middleName;
		$_SESSION['lastName'] = $this->lastName;
		$_SESSION['entity'] = $this->entity;
		$_SESSION['mail'] = $this->mail;
		session_encode();
	}

	/**
	 * Forgets the session data and therefor logs the user out. Furthermore all data is destroyed.
	 * 
	 * @pre The object is loaded
	 * @post All stored information is removed
	 */
	public function logOut(){
		if($this->loaded){
			session_destroy();
			$this->loaded = false;
			foreach($this->values as $k){
				$this->$k = null;
			}
			$this->userId = -1;
		}
	}

	// --------------------------- Getters --------------------------

	/**
	 * Get the entity information for this user
	 *
	 * @return Entity The entity information for the current user
	 */
	public function getEntity(){
		return $this->entity;
	}

	/**
	 * Gets the username of this user
	 *
	 * @return String The human readable username
	 */
	public function getUsername(){
		return $this->username;
	}

	/**
	 * Get the name of this user
	 *
	 * @return String Returns the name of the user in human readable format
	 */
	public function getName(){
		$m = empty($this->middleName)?"":$this->middleName." ";
		return $this->firstName." ".$m.$this->lastName;
	}

	/**
	 * Get the email adress of this user
	 * 
	 * @return String Returns the email of the
	 */
	public function getMail(){
		return $this->mail;
	}

	// ----------------------- Can / Has / Is -----------------------
	public function isLoggedIn(){
		return $this->loaded;
	}


	// --------------------------- Setters --------------------------

	/**
	 * Sets the password of the current user, requires the old password first
	 *
	 * @param String $oldPassword the old password of the user
	 * @param String $password the new password of the user
	 * @return boolean Whether the password was changed in the database
	 */
	public function setPassword($oldPassword, $password){
		global $db, $entity;
		if(!$entity->hasRight('User','edit', $this->userId)){
			return false;
		}
		$res = $db->query("select * from user where id=?",array($this->userId));
		if(count($res)!=1){
			return false;
		}
		$res = $res[0];

		$hash = $res[5];
		if(!password_verify($oldPassword, $hash)){
			return false;
		}
		$hash = password_hash($password, PASSWORD_BCRYPT);

		return $db->nquery("update user set hash=? where id=?", array($hash, $this->userId));
	}

	/**
	 * Updates the user information to values provided in info and store these.
	 * 
	 * @param Array(*) $info The values of the information to set
	 * @return 
	 */
	public function setUserInformation($info){
		global $db, $entity;

		$keys = array_keys($info);
		foreach($keys as $k){
			if(!property_exists($this, $k)){
				return false;
			}
		}
		foreach($info as $k => $f){
			$this->$k = $f;
		}
		return $db->nquery("replace into user (id, firstName, middleName, lastName) values (?, ?, ?, ?)", 
			array($this->userId, $this->firstName, $this->middleName, $this->lastName));
	}

	// --------------------------- Statics --------------------------

	/**
	 * Checks the credentials provided and returns a valid entity if succesfull
	 * 
	 * @param String $username The username of the user to be checked
	 * @param String $password The password of the user to be checked
	 * @return An Entity representing the user when the credentials are correct
	 */ 
	public static function checkCredentials($username, $password){
		global $db;
		$res = $db->query("select * from user where username=?", array($username));
		if(count($res) == 1){
			// Check password
			$r = $res[0];

    		$hash = $r[5];
    		if(!password_verify($password, $hash)){
    			return null;
    		}
			return new User($r[0]);
		}
		return null;
	}

	/**
	 * 
	 * 
	 * 
	 */
	public function createUser($userInfo){
		global $db;
		$return = array("success"=>false);
		$r = $db->query("select * from User where username=?",array($userInfo['username']));
		if(count($r)==0){ // Success
			$doIt = true;
			// Check parameters
			if(strlen($userInfo['username'])>56){
				$return['username'] = "Username too long.";
				$doIt = false;
			}

			if(strlen($userInfo['firstName'])>56){
				$doIt = false;
				$return['firstName'] = "First name too long.";
			}

			if(strlen($userInfo['middleName'])>56){
				$doIt = false;
				$return['middleName'] = "Middle name too long.";
			}

			if(strlen($userInfo['lastName'])>56){
				$doIt = false;
				$return['lastName'] = "Last name is too long.";
			}

			if(strlen($userInfo['email'])>128){
				$doIt = false;
				$return['email'] = "Email too long.";
			}

			if(!filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)){
				$doIt = false;
				$return['email'] = "Email is not of correct format";
			}

			if($userInfo['password']!=$userInfo['confirm']){
				$doIt = false;
				$return['confirm'] = "Passwords do not match";
			}

			$hash = password_hash($userInfo['password'], PASSWORD_BCRYPT);

			if($doIt){
				$fullName = $userInfo['firstName'].(!empty($userInfo['middleName'])?" ".$userInfo['middleName']:"")." ".$userInfo['lastName'];

				$db->nquery("insert into entity (name,visible) values (?,?)",array($fullName, 1));
				$entityId = $db->getDBN()->lastInsertId();

				$db->nquery("insert into user (firstName, middleName,lastName,username,hash,mail,entityId) values (?,?,?,?,?,?,?)",
					array($userInfo['firstName'], $userInfo['middleName'], $userInfo['lastName'], $userInfo['username'], $hash, $userInfo['email'],$entityId));


				$return['success']=true;
			}
		} 
		return $return;
	}

	// --------------------------- Internals --------------------------

	/**
	 * Loads the user from the database and instantiates all variables to their respective values
	 *
	 * @pre true
	 * @post The userinformation is fetched from the database and stored in session data and class variables
	 */
	private function load(){
		global $db;

		$res = $db->query("select * from user where id=?", array($this->userId));
		if(count($res)!=1){
			return false;
		}
		$res = $res[0];

		$this->username = $res[4];
		$this->firstName = $res[1];
		$this->middleName = $res[2];
		$this->lastName = $res[3];
		$this->mail = $res[6];
		$this->entityId = $res[7];

		$this->entity = new Entity($this->entityId);

		return true;
	}

	/**
	 * Loads the stored information from session data
	 * 
	 * @pre true
	 * @return true
	 */
	private function sessionLoad(){
		$this->username = $_SESSION['username'];
		$this->firstName = $_SESSION['firstName'];
		$this->middleName = $_SESSION['middleName'];
		$this->lastName = $_SESSION['lastName'];
		$this->entity = $_SESSION['entity'];
		$this->userId = $this->entity->getId();
		$this->mail = $_SESSION['mail'];
		return true;
	}

	/**
	 * Check whether or not the values should be loaded from session data, checks stored values against values in $this->values
	 *
	 * @pre $this->loaded == true
	 * @return Returns whether the session should be loaded, checks the values stored in $_SESSION
	 */
	private function sessionLoaded(){
		$a = array_values($this->values);
		$b = array_keys($_SESSION);
		return count(array_diff($a + $b, array_intersect($a, $b))) == 0;
	}
}

?>