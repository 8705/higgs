<?php

App::uses('BombController', 'Controller');
 
class DparamShell extends AppShell {
 
	public function startup() {
		parent::startup();
		$this->BombController = new BombController();
	}

	public function add() {
		$this->BombController->add();
	}
}