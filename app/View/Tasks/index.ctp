<div class="actions">
<h3><?php echo __('Actions'); ?></h3>
	<?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'viewcalendar')); ?>
</div>
<p id="clean-bomb" class="btn btn-danger">Bomb一括削除</p>
<h2><?php echo __('User Name: '.h($username)); ?></h2>
<div class="d-bar progress progress-striped">
	<div id="d-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo 100*$bar/dcapacity; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo 100*$bar/dcapacity; ?>%">
	</div>
	<?php echo 100*$bar/dcapacity."%"; ?>
</div>
<div class="row">
	<div class="tasks form col-md-12">
		<?php echo $this->Form->create('Task'); ?>
			<fieldset>
				<legend><?php echo __('Add Task'); ?></legend>
				<?php
					echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $author['id']));//$user_idから$authorに変更
					echo $this->Form->input('body', array('placeholder'=>'タスクを入力して下さい'));
					echo $this->Form->input('start_time', array('type'=>'text', 'class' => 'datepicker','readonly' => 'readonly'));
					echo $this->Form->input('sequence',array('type' => 'hidden', 'class' => 'sequence', 'value' =>'0'));
				?>
				<?php
					//ajax送信用設定
					echo $this->Js->submit('Submit', array(
						'url'		=> 'add',
						'type'		=> 'json',
						'success'	=> 'addTask(data, textStatus)',
						'error'		=> 'popUpPanel(true, "サーバーエラー")',
						'async'		=> true,
						'class' 	=> 'btn btn-primary'
						)
					);
				?>
			</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<div class="row clearfix">
	<div id="tasks" class="index col-md-8 column">
		<h2>今日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/today">D値並べ替え</a>
		<ul class="list-group task-list sort-list" id="task-list-today">
		<?php if (count($tasks_today)): ?>
		<?php foreach ($tasks_today as $task): ?>
			<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task	['Task']['id']); ?>" style="background-color: hsl(<?php echo 0; ?>,100%,<?php echo 100-70*$task['Task']['d_param']/$bar; ?>%);">
				<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
				<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
				<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
				<span class="status"><?php echo h($task['Task']['status']); ?></span>
				<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
				<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
				<!-- <span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span> -->
				<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>
				<!-- <span class="delete-task btn btn-default">削除</span> -->
				<span class="sequence"><?php echo h($task['Task']['sequence']); ?></span>
			</li>
		<?php endforeach; ?>
		<?php else: ?>
			<li class="empty list-group-item clearfix">タスクがありません</li>
		<?php endif; ?>
		</ul>
		<h2>明日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/tomorrow">D値並べ替え</a>
		<ul class="list-group task-list sort-list" id="task-list-tomorrow">
		<?php if (count($tasks_tomorrow)): ?>
		<?php foreach ($tasks_tomorrow as $task): ?>
			<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task	['Task']['id']); ?>">
				<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
				<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
				<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
				<span class="status"><?php echo h($task['Task']['status']); ?></span>
				<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
				<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
				<!-- <span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span> -->
				<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>
				<span class="sequence"><?php echo h($task['Task']['sequence']); ?></span>
			</li>
		<?php endforeach; ?>
		<?php else: ?>
			<li class="empty list-group-item clearfix">タスクがありません</li>
		<?php endif; ?>
		</ul>
		<h2>明後日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/dayaftertomorrow">D値並べ替え</a>
		<ul class="list-group task-list sort-list" id="task-list-dayaftertomorrow">
		<?php if (count($tasks_dayaftertomorrow)): ?>
		<?php foreach ($tasks_dayaftertomorrow as $task): ?>
			<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task	['Task']['id']); ?>">
				<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
				<span class="body"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>
				<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
				<span class="status"><?php echo h($task['Task']['status']); ?></span>
				<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
				<span class="<?php echo h($task['Task']['status']=='notyet'?'edit-task':'disable-edit btn-disabled');?> btn btn-default">編集</span>
				<!-- <span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?> btn btn-default">分割</span> -->
				<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>
				<span class="sequence"><?php echo h($task['Task']['sequence']); ?></span>
			</li>
		<?php endforeach; ?>
		<?php else: ?>
			<li class="empty list-group-item clearfix">タスクがありません</li>
		<?php endif; ?>
		</ul>

	</div>
	<div class="tasks side col-md-4 column">
		<br>
		<div class="tasks parents">
			<h2>Parents</h2>
			<ul class="list-group" id="task-list-parents">
				<?php if (count($parents)): ?>
				<?php foreach ($parents as $parent): ?>
					<li id="parent_<?php echo h($parent['Task']['id']); ?>" class="<?php echo h($parent['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($parent['Task']['id']); ?>">
						<span class="body"><?php echo $this->Html->link(__(h($parent['Task']['body'])), array('action' => 'view', $parent['Task']['id'])); ?></span>
						<span><?php echo $parent['Task']['complete'].'%'; ?></span>
					</li>
				<?php endforeach; ?>
				<?php else: ?>
					<li class="empty list-group-item clearfix">タスクがありません</li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="tasks bombs">
			<h2>Bombs</h2>
			<ul class="list-group" id="task-list-bombs">
				<?php if (count($bombs)): ?>
				<?php foreach ($bombs as $bomb): ?>
					<li id="bomb_<?php echo h($bomb['Task']['id']); ?>" class="<?php echo h($bomb['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($bomb['Task']['id']); ?>">
						<span class="body"><?php echo $this->Html->link(__(h($bomb['Task']['body'])), array('action' => 'view', $bomb['Task']['id'])); ?></span>
					</li>
				<?php endforeach; ?>
				<?php else: ?>
					<li class="empty list-group-item clearfix">タスクがありません</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
