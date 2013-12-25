
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
