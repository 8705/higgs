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

    private function isAuthorized($post) {
        if ($this->Task->isOwnedBy($post, $this->Auth->user('id'))) {
            return true;
        }

        return false;
    }

	public function index() {
		$this->Task->recursive = -1;
		$opt_today = array(
			'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'OR' => array(
                    array(
                        'Task.status ' => 'notyet',
                        'Task.start_time <=' => date('Y-m-d'),
                        '(Task.rght - Task.lft)' => 1
                    ),
                    array(
                        'Task.status' => 'done',
                        'Task.start_time' => date('Y-m-d'),
                        '(Task.rght - Task.lft)' => 1
                    )
                )
            ),
			'order' => array('Task.sequence'=>'asc'),
		);
        $opt_tomorrow = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'Task.status !=' => 'delete',
                'Task.start_time' => date('Y-m-d', strtotime('+1 day')),
                '(Task.rght - Task.lft)' => 1
            ),
            'order' => array('Task.sequence'=>'asc'),
        );
        $opt_dayaftertomorrow = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.bomb' => 0,
                'Task.status !=' => 'delete',
                'Task.start_time' => date('Y-m-d', strtotime('+2 day')),
                '(Task.rght - Task.lft)' => 1
            ),
            'order' => array('Task.sequence'=>'asc'),
        );
        $tasks_today = $this->Task->find('all', $opt_today);
        foreach($tasks_today as $key => $row) {
            $tasks_today[$key]['Task']['breadcrumb'] = $this->makepankuzu($row['Task']['id']);
        }
        // $this->set('tasks_today', $this->Task->find('all', $opt_today));
		$this->set('tasks_today', $tasks_today);
        $this->set('tasks_tomorrow', $this->Task->find('all', $opt_tomorrow));
        $this->set('tasks_dayaftertomorrow', $this->Task->find('all', $opt_dayaftertomorrow));
	}

	public function view($id = null) {
        if(!$this->isAuthorized($id)) {
            throw new BadRequestException('他人のタスクを見ようなんざ、まさに「ゲスの極み」！！');
        }
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		$parents = $this->Task->getPath($id);
		$allChildren = $this->Task->children($parents[0]['Task']['id'], null, null, $order='lft');
		array_unshift($allChildren, $parents[0]);
        foreach ($allChildren as $key => $val) {
            $allChildren[$key]['Task']['indent']    = count($this->Task->getPath($val['Task']['id']))-1;
            $allChildren[$key]['Task']['childCount']  = $this->Task->childCount($val['Task']['id']);
        }
        $this->set('tasks', $allChildren);
	}

    public function bomb() {
        $this->Task->recursive = -1;
        $opt_bombs = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.status' => 'bomb',
                'Task.parent_id' => null,
                'Task.bomb' => 1,
            ),
        );
        $this->set('bombs', $this->Task->find('all', $opt_bombs));
    }

	public function add() {
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない
        $data = $this->request->data;
        if($data['Task']['dbar'] > 90) {
            $error = true;
            $message['body'][] = 'タスクが多すぎます。';
            $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
        // save OK
        if ($this->Task->save($this->request->data)) {
            $result = $this->Task->find('first', array(
                'conditions' => array(
                    'Task.user_id' => $this->Auth->user('id')
                ),
                'order' => array('Task.id' => 'desc'),
                'recursive' => -1
            ));

            $error = false;
            $res = array("error" => $error,"result" => $result);
            $this->response->type('json');
            echo json_encode($res);
            exit;
        // save NG
        } else {
            $error = true;
            $message = $this->Task->validationErrors;
            $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
	}

	public function edit($status, $id = null) {
		//Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
        switch($status) {
            case 'status':
                //syori
                $result = $this->Task->find('first',array(
                    'conditions' => array(
                        'Task.id' => $id,
                    ),
                    'recursive' => -1,
                ));
                if($result) {
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
            case 'cancel':
                //syori
                $result = $this->Task->find('first',array(
                    'conditions' => array(
                        'Task.id' => $id,
                    ),
                    'recursive' => 1,
                ));
                if($result) {
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
                $this->Task->id = $id;
                if ($this->Task->save($this->request->data)) {
                    $options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
                    $result = $this->Task->find('first', $options);
                    $all_d = $this->getdbar($id);
                    $error = false;
                    $res = array("error" => $error,"result" => $result, 'all_d' =>$all_d);
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

        //過去
		$this->Task->id = $id;
		//save OK
		if ($this->Task->save($this->request->data)) {
			$options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
			$result = $this->Task->find('first', $options);

            $all_d = $this->getdbar();
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
	}

	public function divide($id = null) {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Don\'t ajax!'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Non exist $id'));
        }

        $json = json_decode($this->request->data['json'], true);

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
            );
            $errorArray[]       = $this->Task->save($data);
        }

        $bomb = new BombController;
        $bomb->_modifyinfluence($id);

        $resultArray = $this->Task->find('all',array(
            'conditions' => array('Task.user_id' =>$this->Auth->user('id')),
            'limit' => count($json),
            'order' => array('Task.id' => 'desc'),
            'recursive' => -1,
        ));
        $resultArray = array_reverse($resultArray);
        //save OK
        if(!in_array(false, $errorArray)) {
            $all_d = $this->getdbar($id);
            $error = false;
            $res = array("error" => $error,"result" => $resultArray, 'all_d' => $all_d);
            $this->response->type('json');
            echo json_encode($res);
            exit;
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

            $result = $this->Task->find('first',array(
                'conditions'    => array('Task.id' =>$id),
                'recursive' => -1
            ));
            $parent = $this ->Task->getParentNode($id);
            $children = $this->Task->children($id);
            $child_id = array();
            foreach($children as $child) {
                $child_id[] = $child['Task']['id'];
                $this->Task->removeFromTree($child['Task']['id']);
            }

            $this->Task->updateAll(
                array('Task.status' => "'delete'"),
                array('Task.id' => $child_id)
            );
            $this->adjustStatus($id);
            $this->Task->removeFromTree($id);

            if($parent) {
                $bomb = new BombController;
                $bomb->_modifyinfluence($parent['Task']['id']);
            }
            $all_d = $this->getdbar($parent['Task']['id']);
            $attainment = $this->getattainment($parent['Task']['id']);
            $error = false;
            $res = array(
                "error" => $error,
                "result" => $result,
                "all_d" => $all_d,
                "attainment"=>$attainment
            );
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
        $this->Task->id = $id;
        //Ajax or not
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        if (!$this->Task->exists($id)) {
            throw new NotFoundException(__('Invalid task'));
        }

        $res = $this->Task->find('first',array(
            'conditions' => array(
                'id' => $id,
            ),
            'recursive' => -1,
        ));
        $status = $res['Task']['status'];
        if($status == 'done'){
            $status = 'notyet';
        } else {
            $status = 'done';
        }
        $this->Task->set('status', $status);
        //save OK
        if ($this->Task->save($this->request->data)) {

            $this->adjustStatus($id);

            // $options = array('conditions' => array('Task.' . $this->Task->primaryKey => $id));
            $result = $this->Task->find('first',array(
                'conditions' => array(
                    'id' => $id,
                ),
                'recursive' => -1,
            ));
            $all_d = $this->getdbar($id);
            $attainment = $this->getattainment($id);
            $error = false;
            $res = array(
                "error" => $error,
                "result" => $result,
                "all_d" => $all_d,
                "attainment" => $attainment
            );
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

    public function clean() {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        $json = json_decode($this->request->data['json'], true);
        $res = $this->Task->updateAll(
            array('Task.bomb' => 1, 'Task.num_bomb' => 'Task.num_bomb + 1'),
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

    public function selfbomb($id) {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない
        $res = $this->Task->updateAll(
            array('Task.status' => "'bomb'",'Task.bomb' => 1, 'Task.num_bomb' => 'Task.num_bomb + 1'),
            array('Task.id' => $id)
        );
        //save OK
        if($res) {
            $error = false;
            $result = $id;
            $all_d = $this->getdbar($id);
            $res = array("error" => $error, "result" => $result, "all_d" => $all_d);
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

    //Sortable
    public function sort($order, $day = null) {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;   // 自動描画をさせない

        $resultArray = array();  //クライアントへ返す配列
        $errorArray = array();

        switch($day) {
            case 'today':
                $cond = 'Task.start_time <=';
                $day = date('Y-m-d');
                break;
            case 'tomorrow':
                $cond = 'Task.start_time';
                $day = date('Y-m-d', strtotime('+1 day'));
                break;
            case 'dayaftertomorrow':
                $cond = 'Task.start_time';
                $day = date('Y-m-d', strtotime('+2 day'));
                break;
        }

        switch($order) {
            //d値並べ替えの場合
            case 'd':
                $result = $this->Task->find('all', array(
                    'conditions' => array(
                        'Task.user_id' => $this->Auth->user('id'),
                        'Task.status !=' => 'delete',
                        'Task.bomb' => 0,
                        $cond => $day,
                    ),
                    'order' => array('Task.d_param' => 'desc'),
                    'recursive' => -1,
                ));
                // var_dump($result);exit;
                foreach ($result as $sequence => $row) {
                    $this->Task->create();
                    $this->Task->id = $row['Task']['id'];
                    $errorArray[] = $this->Task->saveField('sequence', $sequence);
                    $result = $this->Task->find('first',array(
                        'conditions' =>array(
                            'Task.id' => $row['Task']['id'],
                        ),
                        'recursive' => -1,
                    ));
                    $resultArray[] = $result['Task'];
                }
                if(!in_array(false, $errorArray)){
                    $all_d = $this->getdbar($id);
                    $error = false;
                    $res = array("error" => $error, "result" => $resultArray, 'all_d' => $all_d);
                    $this->response->type('json');
                    echo json_encode($res);
                    exit;
                }

                break;

            //手動並び替えの場合
            case 'manually':
                $request = $this->request->data['sequence'];
                //リクエスト値が 'task[]=130&task[]=210&...'という形なので、$task(array)に値が入っている
                parse_str($request);
                foreach ($task as $sequence => $id) {
                    $this->Task->create();
                    $this->Task->id = $id;
                    $errorArray[] = $this->Task->saveField('sequence', $sequence);
                }
                //saveOK
                if(!in_array(false, $errorArray)) {
                    $error = false;
                    $res = array("error" => $error);
                    $this->response->type('json');
                    echo json_encode($res);
                    exit;
                }
                break;
        }
        if(in_array(false, $errorArray)) {
            $error = true;
            $message = '並べ替えがうまく行きませんでした';
            $res = $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }
    }

    public function tryagain($id = null) {
        if (!$this->request->is('ajax')) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->autoRender = false;
        $all_d = $this->getalldbar($this->Auth->user('id'));
        if(array_sum($all_d) > 90) {
            $error = true;
            $message['body'][] = 'タスクが多すぎます。';
            $res = compact('error', 'message');
            $this->response->type('json');
            echo json_encode($res);
            exit;
        }  // 自動描画をさせない

        $res = $this->Task->updateAll(
            array('Task.bomb' => 0, 'Task.status' => "'notyet'", 'Task.d_param' => 100),
            array('Task.id' => $id)
        );

        if($res) {
            $error = false;
            $res = array("error" => $error, "all_d" => $all_d);
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

    private function adjustStatus($id) {
        $parentArr = array_reverse($this->Task->getPath($id));
        // var_dump($parentArr);
        foreach($parentArr as $row) {

            if($this->Task->childCount($row['Task']['id']) == 0) {
                continue;
            }
            // echo 'id:'.$row['Task']['id'];
            $childArr = $this->Task->children($row['Task']['id'],true,null,'lft',null,1,-1);
            // var_dump($childArr);
            $statusArr = array();
            foreach ($childArr as $subrow) {
                if($subrow['Task']['status'] == 'delete' ) {
                    $statusArr[] = 'delete';
                } else if($subrow['Task']['status'] == 'notyet'){
                    $statusArr[] = 'notyet';
                } else if($subrow['Task']['status'] == 'done') {
                    $statusArr[] = 'done';
                }

            }
            // var_dump($statusArr);exit;
            if(count($statusArr) == 1 && $statusArr[0] == 'delete') {
                continue;
            }
            // var_dump($statusArr);exit;
            if (!in_array('notyet', $statusArr)) {

                $this->Task->id = $row['Task']['id'];
                $this->Task->save(array('status' => 'done'));
            } else {
                $this->Task->id = $row['Task']['id'];
                $this->Task->save(array('status' => 'notyet'));
            }
        }
    }
}
