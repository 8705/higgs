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
            $this->params['action'] == 'check' ||
            $this->params['action'] == 'divide') {
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
                'Task.start_time <=' => date('Y-m-d'),
            ),
			'order' => array('Task.d_param'),
		);
        $opt_tomorrow = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.start_time' => date('Y-m-d', strtotime('+1 day')),
            ),
            'order' => array('Task.d_param'),
        );
        $opt_someday = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.start_time >' => date('Y-m-d', strtotime('+1 day')),
            ),
            'order' => array('Task.d_param'),
        );
		$this->set('tasks_today', $this->Task->find('all', $opt_today));
        $this->set('tasks_tomorrow', $this->Task->find('all', $opt_tomorrow));
        $this->set('tasks_someday', $this->Task->find('all', $opt_someday));
        $this->set('bar', $this->_bar());
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

        //save OK
        //create()は一体何をやっているのか？あとで調べる。今は無理。酔ってるから2013/12/19深夜
        $this->Task->create();
        if ($this->Task->save(array_merge($this->request->data, array('user_id'=>$this->Auth->user('id'))))) {
            //最後の更新のidを取得 !ただし、他人のタスク更新と区別するためAuthユーザーの条件を付け足す必要あり!
            $saved_id = $this->Task->getLastInsertID();
            $options = array('conditions' => array('Task.' . $this->Task->primaryKey => $saved_id));
            $result = $this->Task->find('first', $options);
            $error = false;
            $res = array("error" => $error,"result" => $result["Task"]);
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

    public function _bar() {
        $max = 1000;
        $options = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.status' => 'notyet',
                'Task.start_time <=' => date('Y-m-d')
            ),
            'fields' => array('Task.d_param')
        );
        $d_param = $this->Task->find('list', $options);
        return $bar = array_sum($d_param)/$max*100;
    }
}
