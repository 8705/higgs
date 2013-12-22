<?php

App::uses('AppController', 'Controller');
App::uses('TasksController', 'Controller');

class BombController extends AppController {

	public $uses = array('Task');

	public function _add() {
		$this->Task->updateAll(
			array('Task.d_param' => 'Task.d_param * 2'),
			array('Task.status =' => 'notyet', 'Task.start_time <=' => date("Y-m-d"))
		);
	}

    public function _bomb() {
        $max = dcapacity;
        $dparams = $this->_getdparams();
        arsort($dparams);
        $diff = array_sum($dparams) - $max;
        foreach ($dparams as $id => $dparam) {
            if ($diff > 0) {
                $this->Task->id = $id;
                $this->Task->saveField('status', 'bomb');
                $diff = $diff - $dparam;
            }
        }
    }

    public function _getdparams() {
        $options = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.status' => 'notyet',
                'Task.start_time <=' => date('Y-m-d')
            ),
            'fields' => array('Task.d_param')
        );
        return $this->Task->find('list', $options);
    }
}