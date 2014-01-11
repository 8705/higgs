<?php

App::uses('AppController', 'Controller');
App::uses('TasksController', 'Controller');

class SuportsController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('rule');
    }

    public function rule(){
        $this->layout = 'single';
    }
}