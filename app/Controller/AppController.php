<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

    public $uses = array('User', 'Task');
	public $components = array(
		'Paginator',
		'Session',
		'Auth'=> array(
			'loginRedirect' => array('controller' => 'tasks', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'users', 'action' => 'index'),
		),
        'Security' => array(
            'csrfUseOnce' => false,  //CSRFトークンを使いまわす
            'csrfExpires' => '+1 hour'  //トークンの持続時間を1h延長
        ),
	);

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->deny();
        //全てのアクションでログインユーザー情報を格納する$authorを定義
        $this->set('author', $this->Auth->user());
        $this->set('username', $this->Auth->user('username'));
        $opt_parents = array(
            'conditions' => array(
                'Task.user_id' => $this->Auth->user('id'),
                'Task.parent_id' => null,
                'Task.status' => 'notyet',
            ),
        );
        $parents = $this->Task->find('all', $opt_parents);
        foreach($parents as $key=>$parent) {
            $children = $this->Task->children($parent['Task']['id']);
            $sum_dparam = 0;
            foreach($children as $child) {
                if($child['Task']['status'] == 'done' and $child['Task']['rght']-$child['Task']['lft'] == 1) {
                    $sum_dparam += $child['Task']['d_param'];
                }
            }
            $parents[$key]['Task']['complete'] = 100*$sum_dparam/$parent['Task']['d_param'];
        }
        $this->set('bar', almostzero+array_sum($this->_getdparams()));
        $this->set('parents', $parents);
        //CSRF対策用にSecurityComponentが生成したトークンを取得
        $token = $this->Session->read('_Token.key');
        $this->set('token', $token);

        //SecurityComponentのCSRFチェックを無効
        if ($this->params['action'] == 'delete' ||
            $this->params['action'] == 'edit' ||
            $this->params['action'] == 'check' ||
            $this->params['action'] == 'divide' ||
            $this->params['action'] == 'clean' ||
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
