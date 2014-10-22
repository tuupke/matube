<?php

class pageBuilder {

	private $pageId;
	
	public function __construct($id){
		$this->pageId = $id;
	}

	public function __toString(){
		return $this->pageId;
	}

}