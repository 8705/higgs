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

    public function _myinfluence($id) {
        $parent = $this->Task->getParentNode($id);
        if(!$parent) return 1;
        $count = $this->Task->childCount($parent['Task']['id'],true);
        $options = array('conditions'=>array('Task.id'=>$parent['Task']['id']),'fields'=>'influence');
        $influence_parent = $this->Task->find('list',$options);
        return $influence_parent[$parent['Task']['id']]/$count;
    }

    public function _modifyinfluence($id) {
        $parents = $this->Task->getPath($id);
        $dparam = $parents[0]['Task']['d_param'];
        $children = $this->Task->children($id, false, null, 'lft');
        foreach($children as $i) {
            $influence = $this->_myinfluence($i['Task']['id']);
            $this->Task->updateAll(
                array(
                    'Task.influence' => $influence,
                    'Task.d_param' => round($dparam*$influence),
                ),
                array('Task.id' => $i['Task']['id'])
            );
        }
    }

    public function influenceall() {
        $gods = $this->Task->find('all',array('conditions' => array('Task.parent_id' => null)));
        foreach($gods as $god) {
            $allChildren = $this->Task->children($god['Task']['id'], null, null, $order='lft');
            array_unshift($allChildren, $god);
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
}