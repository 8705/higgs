<?php

App::uses('AppController', 'Controller');
App::uses('TasksController', 'Controller');

class BombController extends AppController {

	public $uses = array('Task');

	public function _add() {
		$this->Task->updateAll(
			array('Task.d_param' => 'Task.d_param * 2'),
			array('Task.status =' => 'notyet', 'Task.start_time <=' => date("Y-m-d"))
		);
	}

    public function modifyinfluence($id) {
        $parent = $this->Task->getParentNode($id);
        $children = $this->Task->children($parent['Task']['id']);
        foreach($children as $child) {
            $influence = $this->_influence($child['Task']['id']);
            $this->Task->id = $child['Task']['id'];
            $this->Task->saveField('influence', $influence);

            $parents = $this->Task->getPath($child['Task']['id']);
            $dparam = $parents[0]['Task']['d_param'];
            $this->Task->saveField('d_param', round($dparam*$child['Task']['influence']));
        }
    }

    public function _myinfluence($id) {
        $parent = $this->Task->getParentNode($id);
        if(!$parent) return 1;
        return $this->_returninfluence($parent['Task']['id']);
    }

    public function _influencefromparent($id) {
        return $this->_returninfluence($id);
    }

    public function _returninfluence($id) {
        $count = $this->Task->childCount($id,true);
        $options = array('conditions'=>array('Task.id'=>$parent['Task']['id']),'fields'=>'influence');
        $influence_parent = $this->Task->find('list',$options);
        return $influence_parent[$parent['Task']['id']]/$count;
    }

    public function influenceall() {
        $gods_id = $this->Task->find('list',array('conditions' => array('Task.parent_id' => null),'fields' => 'id'));
        foreach($gods_id as $id) {
            $parents = $this->Task->getPath($id);
            $allChildren = $this->Task->children($parents[0]['Task']['id'], null, null, $order='lft');
            array_unshift($allChildren, $parents[0]);
            foreach($allChildren as $child) { 
                $influence = $this->_myinfluence($child['Task']['id']);
                $this->Task->id = $child['Task']['id'];
                $this->Task->saveField('influence', $influence);
            }
        }
        echo 'Success!';
        exit;
    }

    public function dparamall() {
        $tasks = $this->Task->find('all');
        foreach($tasks as $task) {
            $parents = $this->Task->getPath($task['Task']['id']);
            $dparam = $parents[0]['Task']['d_param'];
            $this->Task->id = $task['Task']['id'];
            $this->Task->saveField('d_param', round($dparam*$task['Task']['influence']));
        }
        echo 'Success!';
        exit;
    }

    public function _bomb() {
        $max = dcapacity;
        $dparams = $this->_getdparams();
        arsort($dparams);
        $diff = array_sum($dparams) - $max;
        foreach ($dparams as $id => $dparam) {
            if ($diff > 0) {
                $this->Task->id = $id;
                $this->Task->saveField('status', 'bomb');
                $diff = $diff - $dparam;
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