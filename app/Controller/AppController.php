<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

	public $components = array(
		'Paginator',
		'Session',
		'Auth'=> array(
			'loginRedirect' => array('controller' => 'tasks', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'users', 'action' => 'index'),
		),
	);

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
