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
}
