<?php

App::uses('AppController', 'Controller');

class BombController extends AppController {

	public $uses = array('Task');

	public function add() {
		$this->Task->updateAll(
			array('Task.d_param' => 'Task.d_param + 1'),
			array('Task.status =' => 'notyet', 'Task.start_time <=' => date("Y-m-d"))
		);
	}
}