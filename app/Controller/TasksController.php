<?php

App::uses('AppController', 'Controller');

class TasksController extends AppController {

	public $components = array(
		'Paginator',
		'RequestHandler',
		'Security' => array(
			'csrfUseOnce' => false,  //CSRFトークンを使いまわす
			'csrfExpires' => '+1 hour'  //トークンの持続時間を1h延長
		)
	);

	//Jsヘルパー追加
	public $helper = array('Js');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny();
		//全てのアクションでログインユーザー情報を格納する$authorを定義
		$this->set('author', $this->Auth->user());

		//CSRF対策用にSecurityComponentが生成したトークンを取得
		$token = $this->Session->read('_Token.key');
		$this->set('token', $token);

		//SecurityComponentのCSRFチェックを無効
		if ($this->params['action'] == 'delete' ||
			$this->params['action'] == 'edit' ||
			$this->params['action'] == 'check') {
			$this->Security->csrfCheck = false;
			$this->Security->validatePost = false;

			//token確認
			if ( !isset($_SERVER['HTTP_X_CSRF_TOKEN'])
				|| !strtolower($_SERVER['HTTP_X_CSRF_TOKEN']) == $token) { //トークン
				echo 'token error';
				throw new NotFoundException(__('Invalid post'));
			}
		}
	}

	public function index() {
		$this->Task->recursive = -1;
		$this->set('username', $this->Auth->user('username'));
		$options = array(
			'conditions' => array('Task.user_id' => $this->Auth->user('id')),
			'order' => array('Task.start_time'),
		);
		$this->set('tasks', $this->Task->find('all', $options));
	}

	public function view($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$parents = $this->Task->getPath($id);
		$allChildren = $this->Task->children($parents[0]['Task']['id'], null, null, $order='lft');
		array_unshift($allChildren, $parents[0]);
		$this->set('tasks', $allChildren);
	}

	public function add() {

		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        // save OK
        if ($this->Task->save($this->request->data)) {

        	//レンダリングのためにtaskIdを取得する
        	$saved_id = $this->Task->getLastInsertID();

        	/*
        		Userテーブルのレコードも取得してしまっている
        		ユーザー情報を返すのは良くない！
        	*/
        	$result = $this->Task->find('first', array(
		        'conditions' => array('Task.id' => $saved_id)
		    ));

        	$error = false;
        	$res = array("error" => $error,"result" => $result["Task"]);
        	// $res = array_merge('error'=>$error, $result['Task']);
        	// debug($res);exit;
        	$this->response->type('json');
        	echo json_encode($res);
        	exit;

        // save NG
        } else {
        	$error = true;
        	$message = $this->Task->validationErrors;
        	$res = $res = compact('error', 'message');
        	$this->response->type('json');
        	echo json_encode($res);
        	exit;
        }
	}

	public function edit($id = null) {
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$this->Task->id = $id;
		//save OK
		if ($this->Task->save($this->request->data)) {
			$options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
			$result = $this->Task->find('first', $options);
			$error = false;
        	$res = array("error" => $error,"result" => $result["Task"]);
        	$this->response->type('json');
        	echo json_encode($res);
        	exit;
		//save NG
		}else {
			$error = true;
        	$message = $this->Task->validationErrors;
        	$res = $res = compact('error', 'message');
        	$this->response->type('json');
        	echo json_encode($res);
			exit;
		}
	}

	public function divide($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		if ($this->request->is('post')) {
			$this->Task->create();
			if ($this->Task->save($this->request->data)) {
				$this->Session->setFlash(__('The task has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.'));
			}
		}
		$this->set('user_id', $this->Auth->user('id'));
		$this->set('task_id', $id);
	}

	public function delete($id = null) {
		$this->Task->id = $id;
		if (!$this->Task->exists()) {
			throw new NotFoundException(__('Invalid task'));
		}

		$this->autoRender = false;
		$this->autoLayout = false;
		$this->request->onlyAllow('post', 'delete');
		if ($this->Task->delete()) {
			$this->Session->setFlash(__('The task has been deleted.'));
		} else {
			$this->Session->setFlash(__('The task could not be deleted. Please, try again.'));
		}
		//return $this->redirect(array('action' => 'index'));
	}

	public function check($id = null) {
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$this->Task->id = $id;
	}
}
