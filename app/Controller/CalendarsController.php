<?php

App::uses('AppController', 'Controller');

class CalendarsController extends AppController {

	public $uses = array('Task');
	public $helpers = array('Calendar');
	public $components = array('Auth');

	public function viewcalendar() {
		if(isset($this->params['url']['date'])) {
			$val = $this->params['url']['date'];
		} else {
			$val =date("Y-m-d");
		}
		$values = explode("-", $val);
		$keys = array("year", "month", "day");
		$date = array_combine($keys, $values);
		$options = array('conditions' => array('Task.user_id' => $this->Auth->user('id')));
		$tasks = $this->Task->find('all', $options);
		foreach($tasks as $task) {
			$start = explode("-", $task['Task']['start_time']);
			$body[(int)$start[0]][(int)$start[1]][(int)$start[2]][] = $task['Task'];
		}
		$this->set('body', $body);
		$this->set('viewday', $date);
	}

	public function selectcalendar($id, $date=null) {
		if(isset($this->params['url']['date'])) {
			$val = $this->params['url']['date'];
			$id  = $this->params['url']['task_id'];
		} else {
			$val = $this->Task->find('first',
				array('conditions' => array('Task.id'=>$id),'fields' => array('Task.start_time')));
			$val = $val['Task']['start_time'];
		}
		$values = explode("-", $val);
		$keys = array("year", "month", "day");
		$date = array_combine($keys, $values);

		$parents = $this->Task->getPath($id);
		$tasks = $this->Task->children($parents[0]['Task']['id'], null, null, $order='lft');
		array_unshift($tasks, $parents[0]);
		foreach($tasks as $task) {
			$start = explode("-", $task['Task']['start_time']);
			$body[(int)$start[0]][(int)$start[1]][(int)$start[2]][] = $task['Task'];
		}
		$this->set('id', $id);
		$this->set('body', $body);
		$this->set('selectday', $date);
	}

}