<div id="noticePanel"></div>
<div class="tasks index">
	<h2><?php echo __('User Name: '.h($username)); ?></h2>
	<div class="tasks form">
	<?php echo $this->Form->create('Task'); ?>
		<fieldset>
			<legend><?php echo __('Add Task'); ?></legend>
		<?php
			echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $author['id']));//$user_idから$authorに変更
			echo $this->Form->input('body');
			echo $this->Form->input('start_time', array('type'=>'text', 'class' => 'datepicker'));
		?>
		<script>
			$(document).ready(function() {
				$('input.datepicker').Zebra_DatePicker({offset:[-225,1000]});
			});
		</script>
		<?php
			

			//ajax送信用設定
			echo $this->Js->submit('Submit', array(
				'url' 		=> 'add',
				'type' 		=> 'json',
				'success' 	=> 'addTask(data, textStatus)',
				'error' 	=> 'popUpPanel(true, "サーバーエラー")',
				'async' 	=> true
				)
			)
		?>
		</fieldset>
	<?php echo $this->Form->end(); ?>
	</div>
	<div class="d-bar progress progress-striped">
		<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $bar; ?>%">
		</div>
	</div>
	<div class="data-label">
		<span class="head-body"><?php echo __('Task'); ?></span>
		<span class="head-deadLin"><?php echo __('Dead Line'); ?></span>
		<span class="head-status"><?php echo __('Status'); ?></span>
		<span class="head-d"><?php echo __('D値'); ?></span>
		<span class="head-actions"><?php echo __('Actions'); ?></span>
	</div>
	<h3>今日</h3>
	<ul class="list-group" id="task-list-today">
	<?php if (count($tasks_today)): ?>
	<?php foreach ($tasks_today as $task): ?>
		<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
			<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
			<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
			<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
			<span class="status"><?php echo h($task['Task']['status']); ?></span>
			<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span>
			<span class="delete-task btn btn-default">削除</span>
		</li>
	<?php endforeach; ?>
	<?php else: ?>
		<li class="list-group-item clearfix">タスクがありません</li>
	<?php endif; ?>
	</ul>
	<h3>明日</h3>
	<ul class="list-group" id="task-list-tomorow">
	<?php if (count($tasks_tomorrow)): ?>
	<?php foreach ($tasks_tomorrow as $task): ?>
		<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
			<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
			<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
			<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
			<span class="status"><?php echo h($task['Task']['status']); ?></span>
			<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span>
			<span class="delete-task btn btn-default">削除</span>
		</li>
	<?php endforeach; ?>
	<?php else: ?>
		<li class="list-group-item clearfix">タスクがありません</li>
	<?php endif; ?>
	</ul>
	<h3>明後日</h3>
	<?php if (count($tasks_dayaftertomorrow)): ?>
	<ul class="list-group" id="task-list-dayaftertomorrow">
	<?php foreach ($tasks_dayaftertomorrow as $task): ?>
		<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
			<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
			<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
			<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
			<span class="status"><?php echo h($task['Task']['status']); ?></span>
			<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
			<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span>
			<span class="delete-task btn btn-default">削除</span>
		</li>
	<?php endforeach; ?>
	<?php else: ?>
		<li class="list-group-item clearfix">タスクがありません</li>
	<?php endif; ?>
	</ul>
	<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
		<?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'viewcalendar')); ?>
	</div>
</div>

