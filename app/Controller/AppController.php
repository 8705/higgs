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
                'Task.bomb' => 0,
                'Task.status !=' => 'delete',
            ),
        );
        $parents = $this->Task->find('all', $opt_parents);
        $bar = array();
        foreach($parents as $key=>$parent) {
            $children = $this->Task->children($parent['Task']['id']);
            array_unshift($children, $parent);
            $sum_dparam = 0;
            foreach($children as $child) {
                if(
                    $child['Task']['status'] == 'notyet' and
                    $child['Task']['rght'] - $child['Task']['lft'] == 1
                ) {
                    $sum_dparam += $child['Task']['influence'];
                }
            }
            $parents[$key]['Task']['complete'] = round(100*(1-$sum_dparam));
            $bar[$parent['Task']['id']] = 100*$parent['Task']['d_param']*$sum_dparam/dcapacity;
        }
        $this->set('bar', $bar);
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
            $this->params['action'] == 'selfbomb' ||
            $this->params['action'] == 'sort' ||
            $this->params['action'] == 'tryagain'
        ) {
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

    public function getalldbar($userid) {
        $options = array(
            'conditions' => array(
                'Task.user_id' => $userid,
                'Task.status' => 'notyet',
                'Task.parent_id' => null
            )
        );
        $gods = $this->Task->find('all', $options);
        $alldbar = array();
        foreach($gods as $key=>$god) {
            $children = $this->Task->children($god['Task']['id']);
            array_unshift($children, $god);
            $sum_influence = 0;
            foreach($children as $child) {
                if(
                    $child['Task']['status'] == 'notyet' and
                    $child['Task']['rght'] - $child['Task']['lft'] == 1
                ) {
                    $sum_influence += $child['Task']['influence'];
                }
            }
            $alldbar[$god['Task']['id']] = 100*$god['Task']['d_param']*$sum_influence/dcapacity;
        }

        return $alldbar;
    }

    public function getdbar($id) {
        $parents = $this->Task->getPath($id);
        $children = $this->Task->children($parents[0]['Task']['id']);
        array_unshift($children, $parents[0]);
        $sum_dparam = 0;
        foreach($children as $child) {
            if(
                $child['Task']['status'] == 'notyet' and
                $child['Task']['rght'] - $child['Task']['lft'] == 1
            ) {
                $sum_dparam += $child['Task']['influence'];
            }
        }
        $dbar[$parents[0]['Task']['id']] = 100*$parents[0]['Task']['d_param']*$sum_dparam/dcapacity;
        return $dbar;
    }

    public function getattainment($id) {
        $god = $this->Task->getPath($id);
        $children = $this->Task->children($god[0]['Task']['id']);
        array_unshift($children, $god[0]);
        $sum_dparam = 0;
        foreach($children as $child) {
            if(
                $child['Task']['status'] == 'done' and
                $child['Task']['rght'] - $child['Task']['lft'] == 1
            ) {
                $sum_dparam += $child['Task']['influence'];
            }
        }
        $attainment[$god[0]['Task']['id']] = round(100*$sum_dparam);
        return $attainment;
    }

    public function makepankuzu($id) {
        // $parents = $this->Task->getPath($id,array('Task.body'));
        $parents = $this->Task->getPath($id);
        $count = 0;
        $elm = '<a href="/tasks/view/'.$parents[0]['Task']['id'].'">';
        $elm .= '<span class="origin glyphicon glyphicon-flag"></span> ';
        foreach ($parents as $id => $row) {
            if($count != 0) $elm .= ' &gt; ';
            $elm .= $row['Task']['body'];
            $count++;
        }
        $elm .= '</a>';
        // echo $elm;
        return $elm;
    }
}
