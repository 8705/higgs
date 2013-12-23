<?php

App::uses('AppController', 'Controller');
App::uses('BombController', 'Controller');

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
            $this->params['action'] == 'check' ||
            $this->params['action'] == 'divide' ||
            $this->params['action'] == 'clean') {
             $this->Security->csrfCheck = false;
             $this->Security->validatePost = false;

			//token確認
			if ( !isset($_SERVER['HTTP_X_CSRF_TOKEN'])
				|| !strtolower($_SERVER['HTTP_X_CSRF_TOKEN']) == $token) { //トークン
				echo 'token error';
                throw new NotFoundException(__('Token error'));
			}
		}
	}

	public function index() {
		$this->Task->recursive = -1;
		$this->set('username', $this->Auth->user('username'));
		$opt_today = array(
			'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'Task.start_time <=' => date('Y-m-d'),
            ),
			'order' => array('Task.d_param'),
		);
        $opt_tomorrow = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'Task.start_time' => date('Y-m-d', strtotime('+1 day')),
            ),
            'order' => array('Task.d_param'),
        );
        $opt_dayaftertomorrow = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'Task.start_time' => date('Y-m-d', strtotime('+2 day')),
            ),
            'order' => array('Task.d_param'),
        );
        $opt_parents = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.parent_id' => null,
                'Task.status' => 'notyet',
            ),
        );
        $opt_bombs = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.status' => 'bomb',
                'Task.bomb' => 1,
            ),
        );
		$this->set('tasks_today', $this->Task->find('all', $opt_today));
        $this->set('tasks_tomorrow', $this->Task->find('all', $opt_tomorrow));
        $this->set('tasks_dayaftertomorrow', $this->Task->find('all', $opt_dayaftertomorrow));
        $this->set('bar', almostzero+array_sum($this->_getdparams()));
        $this->set('parents', $this->Task->find('all', $opt_parents));
        $this->set('bombs', $this->Task->find('all', $opt_bombs));
	}

	public function view($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$parents = $this->Task->getPath($id);
		$allChildren = $this->Task->children($parents[0]['Task']['id'], null, null, $order='lft');
		array_unshift($allChildren, $parents[0]);
        foreach ($allChildren as $key => $val) {
            $allChildren[$key]['Task']['indent'] = count($this->Task->getPath($val['Task']['id']))-1;
        }
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
        //Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Don\'t ajax!'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Non exist $id'));
        }

        $json = json_decode($this->request->data['json'], true);
        // debug($json);
        $errorArray = array();
        $resultArray = array();
        $this->Task->create();

        foreach ($json as $row) {
            $this->Task->create();
            $data = array(
                'parent_id'     => $id,
                'user_id'       => $this->Auth->user('id'),
                'body'          => $row['body'],
                'start_time'    => $row['start_time'],
                'd_param'       => $row['d_param']
            );
            $errorArray[]       = $this->Task->save($data);
            $row                = $this->Task->find('first',array(
                'conditions'    => array('Task.user_id' =>$this->Auth->user('id')),
                'order'         => array('Task.id' => 'desc'),
            ));
            $resultArray[]      = $row['Task'];
        }
        //save OK
        if(!in_array(false,$errorArray)) {
            $error = false;
            $res = array("error" => $error,"result" => $resultArray);
            $this->response->type('json');
            echo json_encode($res);
            exit;
        } else {

        }
    }

	public function delete($id = null) {
        $this->Task->id = $id;
        //Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
		if (!$this->Task->exists()) {
			throw new NotFoundException(__('Invalid task'));
		}

		$this->autoRender = false;
		$this->autoLayout = false;

        //save OK
        $this->Task->id = $id;
        if($this->Task->saveField('status','delete')) {
            $row = $this->Task->find('first',array(
                'conditions'    => array('Task.id' =>$id),
            ));
            $result = $row['Task'];
            $error = false;
            $res = array("error" => $error,"result" => $result);
            $this->response->type('json');
            echo json_encode($res);
        //save NG
        } else {
            $error = true;
            $message = $this->Task->validationErrors;
            $res = $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }

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

    public function clean() {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        $json = json_decode($this->request->data['json'], true);

        $res = $this->Task->updateAll(
            array('Task.bomb' => 0),
            //array('Task.bomb' => 1)
            array('Task.id' => $json)
        );
        //save OK
        if($res) {
            $error = false;
            $result = $json;
            $res = array("error" => $error, "result" => $result);
            $this->response->type('json');
            echo json_encode($res);
            exit;
        //save NG
        } else {
            $error = true;
            $message = $this->Task->validationErrors;
            $res = $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
    }
}
