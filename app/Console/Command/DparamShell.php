<?php

App::uses('BombController', 'Controller');
 
class DparamShell extends AppShell {
 
	public function startup() {
		parent::startup();
		$this->BombController = new BombController();
	}

	public function bomb() {
		$this->BombController->_add();
        $this->BombController->_bomb();
	}
}