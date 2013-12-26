<?php

App::uses('AppController', 'Controller');

class CalendarsController extends AppController {

	public $uses = array('Task');
	public $helpers = array('Calendar');
	public $components = array(
		'Auth',
		'Security' => array(
			'csrfUseOnce' => false,  //CSRFトークンを使いまわす
			'csrfExpires' => '+1 hour'  //トークンの持続時間を1h延長
		)
	);
	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->deny();
		//全てのアクションでログインユーザー情報を格納する$authorを定義
		$this->set('author', $this->Auth->user());

		//CSRF対策用にSecurityComponentが生成したトークンを取得
		$token = $this->Session->read('_Token.key');
		$this->set('token', $token);

		//SecurityComponentのCSRFチェックを無効
		if ($this->params['action'] == 'status' ||
			$this->params['action'] == 'edit' ||
			$this->params['action'] == 'sort') {
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

	public function viewcalendar() {
		if(isset($this->params['url']['date'])) {
			$val = $this->params['url']['date'];
		} else {
			$val =date("Y-m-d");
		}
		$values = explode("-", $val);
		$keys = array("year", "month", "day");
		$date = array_combine($keys, $values);
		$options = array(
			'conditions' => array(
				'Task.user_id' => $this->Auth->user('id'),
				'Task.status !=' => 'delete',
			)
		);
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

	//task-idからタスクレコードを引っ張って返す
	public function status($id) {
		$this->Task->id = $id;
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Don\'t ajax!'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Non exist $id'));
        }
        $row = $this->Task->find('first',array(
        	'conditions' => array(
        		'Task.id' => $id,
        	),
        	'recursive' => 1,
        ));
        if($row) {
        	$result = $row['Task'];
        	$error = false;
            $res = array("error" => $error,"result" => $result);
            $this->response->type('json');
            echo json_encode($res);
            exit;
        } else  {
        	$error = true;
            $message = 'データ取得失敗';
            $res = $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
	}

	public function edit($status, $id) { //status -- 1:stauts,2:push
		$this->Task->id = $id;
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Don\'t ajax!'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Non exist $id'));
        }
        switch($status) {
        	case 'status':
        		//syori
        		$row = $this->Task->find('first',array(
		        	'conditions' => array(
		        		'Task.id' => $id,
		        	),
		        	'recursive' => 1,
		        ));
		        if($row) {
		        	$result = $row['Task'];
		        	$error = false;
		            $res = array("error" => $error,"result" => $result);
		            $this->response->type('json');
		            echo json_encode($res);
		            exit;
		        } else  {
		        	$error = true;
		            $message = 'データ取得失敗';
		            $res = $res = compact('error', 'message');
		            $this->response->type('json');
		            echo json_encode($res);
		            exit;
		        }
        		break;

        	case 'push':
        		//syori
        		if ($this->Task->save($this->request->data)) {
					$options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
					$result = $this->Task->find('first', $options);
                    $all_d = $this->getuseralld();
					$error = false;
		        	$res = array("error" => $error,"result" => $result["Task"], 'all_d' =>$all_d);
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
        		break;
        	default:
        		throw new NotFoundException(__("Non exist $status"));
        }
	}
	public function sort(){
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Don\'t ajax!'));
        }
        $this->autoRender = false;   // 自動描画をさせない
        $errorArray = array();
        $request = $this->request->data['sequence'];
        $date = $this->request->data['date'];
        //リクエスト値が 'task[]=130&task[]=210&...'という形なので、$task(array)に値が入っている
        parse_str($request);
        foreach ($task as $sequence => $id) {
            $this->Task->create();
            // $this->Task->id = $id;
            $errorArray[] = $this->Task->save(
            	array(
            		'id' 			=> $id,
            		'start_time' 	=> $date,
            		'sequence' 		=> $sequence,
            	),
            	false,
            	array('start_time','sequence')
            );
        }
        //saveOK
        if(!in_array(false, $errorArray)) {
            $error = false;
            $res = array("error" => $error);
            $this->response->type('json');
            echo json_encode($res);
            exit;
        } else {
        	$error = true;
            $message = 'サーバーエラーでタスクの移動に失敗しました';
            $res = $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
	}
}