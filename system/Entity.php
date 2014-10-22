<?php

/* TODOS

Check very carefully if the permuted function works.
More comment work

*/
class Entity {

	/** Middle name of this entity */
	private $name = "";

	/** The rights of this entity */
	private $rights;

	/** Permuted rights list, which says which id's which rights with values apply to */
	private $permuted;

	/** This array contains the originating entity id for the specific rights */
	private $originating;

	/** The parents of this entity */
	private $parents;

	/** The children of this entity */
	private $children;

	/** The unique identifier of this entity */
	private $id;

	const NOPERMISSION = -1;

	// ------------------------ Construction ------------------------
	/**
	 * Construct an entity for a given identifier
	 *
	 * @param integer $id The identifier for the requested entity
	 */
	public function __construct($id = -1){
		$this->id = $id;

		if($id > -1){
			$this->loadValues();			
		}
	}

	/**
	 * Load the values from the database for this entity
	 *
	 * @pre $this->id > -1
	 * @post The values are loaded from the database
	 */
	private function loadValues(){
		global $db;
		$val = array($this->id);

		// Fetch the name
		$res = $db->query("select * from entity where entityId=?", $val);
		if(count($res) != 1){
			return;
		}

		$this->name = $res[0][1];

		// Fetch the children
		$res = $db->query("select child from `group` where parent=?", $val);
		$this->children = count($res) ? call_user_func_array('array_merge', $res) : array();

		// Fetch the parents
		$res = $db->query("select parent from `group` where child=?", $val);
		$this->parents = count($res) ? call_user_func_array('array_merge', $res) : array();

		// Fetch the rights for the entity
		$this->calculateRights();
	}


	/**
	 * Calculates the rights for this entity, does BFS over the graph (stored in sql db)
	 * Determines the permissions, originating entity and the set of identifiers for the
	 * (object, right, value) tuple
	 *
	 * @pre loadValues has been called, but strictely speeking we only need a valid level
	 * @post All values having to do with permissions are loaded
	 */
	private function calculateRights(){
		global $db;

		// The parents set where we will iterate over, we start with the entityId itself, since you want the a
		$parents = array($this->id);
		$localPerm = array(array(array()));
		$localMuta = array(array(array(array())));
		$originating = array(array(array(array())));

		// BFS over the tree, store all parents of a level in temporary array and continue untill no parents are left.
		while(count($parents)) {

			// Temporary permissions for the checks per level
			$tempPerm = array();

			$placeholders = rtrim(str_repeat('?, ', count($parents)), ', ');

			$result = $db->query("select * from permission where entityId in ($placeholders)", $parents);

			foreach($result as $r){
				$parent = $r[0];
				$obj = $r[1];
				$right = $r[2];
				$objId = $r[3];
				$value = $r[4];

				/*
				 * Small experiment to determine if we could use a simplified formula for the calculation of the rights assignment
				 *
				 * a  = isset($localPerm[$obj][$right][$objId])
				 * b  = isset($tempPerm[$obj][$right][$objId])
				 * c  = $tempPerm[$obj][$right][$objId]
				 * r0 = (!a && !b) || (b && c)
				 * r1 = !a || (b && c)
				 * 
				 * a !a b !b c r0 r1
				 * 0  1 0  1 0  1  1
				 * 0  1 0  1 1  1  1
				 * 0  1 1  0 0  0  1
				 * 0  1 1  0 1  1  1
				 * 1  0 0  1 0  0  0
				 * 1  0 0  1 1  0  0
				 * 1  0 1  0 0  0  0
				 * 1  0 1  0 1  1  1
				 * Only different value means:
				 * not a member of containing array, but set in working array and not neccisarily true
				 * We do have that r0 |= r1 but that does not help.
				 *
				 * It is a strict requirement that if the value in the working array is overridden it has to NOT be false.
				 * So r1 cannot be implemented
				 */
				// Check if tempPerm has to be (over)written
				if((!isset($localPerm[$obj][$right][$objId]) && !isset($tempPerm[$obj][$right][$objId])) ||
					(isset($tempPerm[$obj][$right][$objId]) && !$tempPerm[$obj][$right][$objId])){

					$tempPerm[$obj][$right][$objId] = $value;

					// Determine whether there is a reference to a denial right for this specific object id and right
					$mutaIndex = isset($localMuta[$obj][$right][!$value])?array_search($objId, $localMuta[$obj][$right][!$value]):false;

					// If the value we will set is false 
					if(!$value && $mutaIndex !== false){
						unset($localMuta[$obj][$right][!$value][$mutaIndex]);
						$localMuta[$obj][$right][$value][] = $objId;
					} else if(!$value || $mutaIndex === false){
						$localMuta[$obj][$right][$value][] = $objId;
					}

					$originating[$obj][$right][$objId][$value] = $parent;
				}
			}

			// Get the parents from the next level
			$result = $db->query("select parent from `group` where child in ($placeholders)", $parents);
			$parents = count($result)?call_user_func_array('array_merge', $result):array();
			$localPerm = array_replace_recursive($localPerm, $tempPerm);
		}

		// Store the local values in the global class variables
		$this->rights = $localPerm;
		$this->permuted = $localMuta;
		$this->originating = $originating;
	}

	// --------------------------- Getters --------------------------

	/**
	 * Returns the name of the entity
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Get the parents set (id) of this entity
	 */
	public function getParents(){
		return $this->parents;
	}

	/**
	 * Get the children set (id) of this entity
	 */
	public function getChildren(){
		return $this->children;
	}

	/**
	 * Get the id of this entity
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Returns the entire rights array
	 *
	 * @pre $this->isLoaded() == true
	 * @return The permission array
	 */
	public function getPermissions(){
		return $this->rights;
	}

	/**
	 *
	 * 
	 * @pre 
	 * @param 
	 * @param 
	 * @param 
	 * @param 
	 * @return 
	 */
	public function checkPermuted($object, $right, $value, $toCheck){
		if(isset($this->rights['*']['*']['*']) || isset($this->rights[$object]['*']['*']) ||isset($this->rights[$object][$right]['*'])){
			return $toCheck;
		} else {
			return array_intersect($this->permuted[$object][$right][$value], $toCheck);
		}
	}

	// --------------------------- Setters --------------------------

	// ----------------------- Can / Has / Is -----------------------

	/**
	 * Checks whether the entity has a specific right, more specific is better
	 * If the right is not specified in any matter, it is assumed the user does NOT
	 * have the specified right
	 *
	 * @param String $object The object on which the right is defined
	 * @param String $right The actual right
	 * @param int $id The identifier of an $object instance on which the right applies
	 */
	public function hasRight($object, $right, $id){
		if(isset($this->rights['*']['*']['*'])){
			return $this->rights['*']['*']['*'];
		} else if(isset($this->rights[$object]['*']['*'])){
			return $this->rights[$object]['*']['*'];
		} else if(isset($this->rights[$object][$right]['*'])){
			return $this->rights[$object][$right]['*'];
		} else if(isset($this->rights[$object][$right][$id])){
			return $this->rights[$object][$right][$id];
		}
		return false;
	}

	/**
	 * Checks whether a graph for a given childrenset will contain a cycle or not
	 *
	 * @param int $parent The entity id of the parent which will be checked for cycles
	 * @param Array(int) $overChildren The array of children entity ids which will be seen as 
	 */
	private static function hasCycle($parent, $overChildren){
		global $db;
		while(count($overChildren)) {
			// Check if parent is in the childrenset
			if(in_array($parent, $overChildren)){
				return true;
			}
			$placeHolders = rtrim(str_repeat('?, ', count($overChildren)), ', ');
			$res = $db->query("select unique child from group where parent in ($placeHolders)", $overChildren);
			$overChildren = call_user_func_array('array_merge', $res);
		}
		return false;
	}

	// --------------------------- Internals --------------------------
	private function save(){

	}	

	// --------------------------- Statics --------------------------

	/**
	 * Set a permission for a given entity id. The entity in question needs to relog
	 *
	 * @param int $id The entityId for whom the permission is intended
	 * @param String $object The object for which the permission will hold
	 * @param String $right The actual right for the entity
	 * @param int $identifier The identifier for $object, for which the right will apply
	 * @param bool $value The value of the entity, give/forbid $right of $identifier on $object
	 */ 
	public static function setPermission($id, $object, $right, $identifier, $value){
		global $db;
		$vals = array($id, $object, $right, $identifier, $value);
		$db->nquery("replace into permission values (?, ?, ?, ?, ?)", $vals);
	}

	/**
	 * Override the name for an entity
	 *
	 * @param int $id The entity id
	 * @param String $name The new name of the entity
	 */
	public static function setName($id, $name){
		global $db, $entity;
		$db->nquery("update entity set name=? where id=?", array($name, $id));
	}

	/**
	 * Add a child to an entity
	 *
	 * @param int $entityId
	 * @param int $childId
	 */
	public static function addChild($entityId, $childId){
		global $db, $entity;

		if($entity->hasRight('entity','edit',$this->id)){
			$db->nquery("replace into group values (?, ?)", array($entityId, $childId));
		} else {
			return self::NOPERMISSION;
		}
	}

	/**
	 * Remove a child from an entity
	 *
	 * @param int $entityId The entity id needs to get a child
	 * @param int $childId the entity id of the entity to be added as child to $parentId
	 */
	public static function removeChild($entityId, $childId){
		global $db, $entity;

		if($entity->hasRight('entity', 'edit', $this->id)){
			$db->nquery("delete from group where parent=? and child=?", array($parentId, $childId));
		} else {
			return self::NOPERMISSION;
		}
	}

	/**
	 * Set the children for a given entity id
	 *
	 * @param int $entityId The entity id needs to assigned the $children set
	 * @param Array(int) $children The array of entity ids which need to be assigned as children to $entityId
	 */
	public static function setChildren($entityId, $children){
		global $db, $entity;
		if(!$entity->hasRight('entity','edit', $this->id)){
			return self::NOPERMISSION;
		}
		$db->nquery("delete from group where parentId=?", array($entityId));
		$fff = array_fill(0, count($children), $entityId);
		$vals = array(":eId" => $entityId);
		$placeholders = "";
		for($i = 0; $i < count($children); $i++){
			$vals[":n$i"] = $children[$i];
			$placeholders .= "(:eId, :n$i),";
		}
		$values = rtrim($placeholders, ', ');
		$db->nquery("insert into group values $values", $vals);
	}

	/**
	 * Create and return a new entity
	 *
	 * @param String $name The new name of the entity
	 * @param Array(int) $children The array of children for this new 
	 * @param bool $visible Whether the entity is visible
	 */
	public static function createEntity($name, $children, $visible = true){
		global $db;
		$db->nquery("insert into entity (name, visible) values (?, ?)", array($name, $visible?1:0));
		$id = $db->getDBN()->lastInsertId();
		Entity::setChildren($id, $children);
		return new Entity($id);
	}

	/**
	 * Returns the graph as adjacency matrix
	 */
	public static function getGraph(){
		global $db;

		$return = array();

		// Start with the orphans, or roots of the graph
		$res = $db->query("select id from entity where id not in (select distinct(child) from `group`)", array(), PDO::FETCH_NUM);
		$calcSet = count($res)?call_user_func_array("array_merge", $res):array();

		// While current "generation" (level) is not empty, i.e. we are NOT done
		while(count($calcSet)) {
			// Instantiate the container for the new generation
			$nextGen = array();

			// Iterate over all members of current level and add their children
			foreach($calcSet as $u){

				// Fetch all children (as direct array) and set them accordingly
				$pSet = $db->query("select child from `group` where parent=?", array($u), PDO::FETCH_NUM);

				if(count($pSet)){
					$pSet = call_user_func_array('array_merge', $pSet);
					$return[$u] = $pSet;

					// They are ofcourse part of the generation which needs to be checked next
					$nextGen = array_merge($nextGen, $pSet);
				}
			}
			$calcSet = $nextGen;
		}
		return $return;
	}
}

?>