<?php

class Menu {

	private $menuId;

	private $loaded = false;

	private $name;

	private $menuItems;

	public function __construct($id = -1, $load = true){
		$this->menuId = $id;

		if($load && $id > -1){
			$this->loaded = $this->loadMenu();
		}
	}	

	public function loadMenu(){
		global $db, $user;

		$arrId = array($this->menuId);
		$res = $db->query("select * from menu where id=?", $arrId);
		if(!count($res)) {
			return false;
		}
		$res = $res[0];

		$this->name = $res[1];

		$this->menuItems = $db->query("select * from menuItems where menuId=?", $arrId);

		return count($this->menuItems) != 0;
	}


	public function __toString(){
		return (($this->loaded || $this->loadMenu()) && count($this->menuItems))?$this->build():"";
	}

	private function build(){

		$menuIts = "";
		foreach($this->menuItems as $item){
			$menuIts .= "<li class='system-menu-li'><a class='system-munu-a' href='$item[4]/$item[5]/'>$item[3]</a></li>";
		}

		return "
<div id='Menu_$this->menuId' class='system-menu'>
	<div class='system-menu-name'>$this->name</div>
	<div>
		<ul class='system-menu-ul'>
		$menuIts
		</ul>
	</div>
</div>";

	}

	public function install(){
		global $db;

		$db->query("");
	}
}

?>