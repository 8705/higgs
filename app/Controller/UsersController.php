<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $components = array(
		'Cookie',
		'Security' => array(
			'csrfUseOnce' => false,  //CSRFトークンを使いまわす
			'csrfExpires' => '+1 hour'  //トークンの持続時間を1h延長
		)
	);
	public $name = 'Users';
	public $uses = array("Passport", "User");//使用するモデルを追加
	public $expires = "2 weeks";//パスポートの有効期限

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Security->validatePost = false;
		if($this->params['action'] != 'logout') {
			if($this->Auth->login()) $this->redirect($this->Auth->redirectUrl());
		}

		$this->Auth->autoRedirect = false;
		$this->Auth->allow('index', 'register');
		$this->Cookie->name = 'remember_me';
		$this->Cookie->time = '1 weeks';  // または '1 hour'
		$this->Cookie->path = '/todo/';
		$this->Cookie->domain = 'todo.pyns.jp';
		$this->Cookie->secure = false;  // セキュアな HTTPS で接続している時のみ発行されます
		$this->Cookie->key = 'cw94r8vy34(*(hn93N&Q#3q4P*@^OAIW3myw8P*CP#Rp08ppoYfh!@fpp';
		$this->Cookie->httpOnly = true;
	}

	public function index() {
		$this->layout = 'welcom';
	}

	public function login() {
		$this->layout = 'single';
		if($this->request->is('post')){
			if($this->Auth->login()) {
				$user = $this->Auth->user();
				if (empty($this->data['User']['remember_me'])) {
					$this->__passportDelete($user);
				} else {
					$this->__passportWrite($user);
				}
				unset($this->data['User']['remember_me']);
				$this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Session->setFlash('入力がただしくありません');
			}
		}

		$cookiePassport=$this->Cookie->Read('User');
		if($cookiePassport){
			$deadline 	= date('Y-m-d H:i:s', strtotime("-".$this->expires));
			$options 	= array(
				'conditions' => array(
					'Passport.passport' => $cookiePassport['passport'],
					'Passport.updated >' => " $deadline"
				)
			);
			$passport 	= $this->Passport->find("first",$options);
			if($passport){
				$user['username'] = $passport['User']['username'];
				$user['password'] = $passport['User']['password'];

				if($this->Auth->login($user)){
					$this->__passportWrite($passport);
					$this->flash("自動ログインしました。",$this->Auth->redirect());
				}
			}
		}
		// $this->redirect(array('action'=>'index'));
	}

	public function logout() {
		$user=$this->Auth->user();
		$this->__passportDelete($user);
		$this->Session->setFlash('ログアウトしました');
		$this->redirect($this->Auth->logout());
	}

	public function register() {
		$this->layout = 'single';
		// if($this->Auth->login()) $this->redirect($this->Auth->redirectUrl());
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				$this->Auth->login();
				$this->redirect(array('controller'=>'tasks', 'action'=>'index'));
			} else {
				$this->Session->setFlash('入力がただしくありません');
				// $this->redirect(array('controller'=>'users', 'action'=>'index'));
			}
		}
	}
	/* とりあえず消しとく
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}
	*/

	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

	/*とりあえず消す
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('The user has been deleted.'));
		} else {
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	*/

	public function __passportWrite($user){
		$passport = array();
		$passport['user_id']=$user['id'];
		$passport['passport']=Security::generateAuthKey();
		if(isset($user['Passport']['id']))$passport['id']=$user['Passport']['id'];
		$this->Passport->save($passport);

		$cookie = array('passport'=>$passport['passport']);
		$this->Cookie->write('User', $cookie, true,"+ ".$this->expires);
	}

	public function __passportDelete($user){
		$this->Cookie->delete('User');
		$condition=array('Passport.user_id'=>$user['id']);
		$this->Passport->deleteAll($condition);
	}
}
