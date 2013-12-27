<div class="tasks view">

	<h3>一族</h3>
	<ul class="list-group task-list children-ul" id="task-list">
		<?php $prev = 0; ?>
		<?php foreach ($tasks as $task): ?>
			<?php $indent = $task['Task']['indent']-$prev ?>
			<?php if($indent == 0): ?>
				<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php if($task['Task']['parent_id'] == null){echo 'origin';} ?> <?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
					<?php if($task['Task']['parent_id'] == null && $task['Task']['childCount'] != 0): ?>
					<span class="origin">神</span>
					<?php elseif($task['Task']['childCount'] != 0): ?>
					<span class="accordion opne glyphicon glyphicon-expand"></span>
					<?php else: ?>
					<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
					<?php endif; ?>
					<span class="body edit-task"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id']));?></span>
					<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
					<span class="status"><?php echo h($task['Task']['status']); ?></span>
					<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
					<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?>"><span class="glyphicon glyphicon-plus-sign"></span>分割</span>
					<span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>
				</li>
			<?php elseif($indent == 1): ?>
				<ul class="children-ul" data-children-ul-id="<?php echo h($task['Task']['parent_id']); ?>">
				<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php if($task['Task']['parent_id'] == null){echo 'origin';} ?> <?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
					<?php if($task['Task']['parent_id'] == null): ?>
					<span class="origin">神</span>
					<?php elseif($task['Task']['childCount'] != 0): ?>
					<span class="accordion opne glyphicon glyphicon-expand"></span>
					<?php else: ?>
					<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
					<?php endif; ?>
					<span class="body edit-task"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id']));?></span>
					<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
					<span class="status"><?php echo h($task['Task']['status']); ?></span>
					<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
					<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?>"><span class="glyphicon glyphicon-plus-sign"></span>分割</span>
					<span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>
				</li>
			<?php elseif($indent < 0): ?>
				<?php echo str_repeat('</ul>', -$indent) ?>
				<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php if($task['Task']['parent_id'] == null){echo 'origin';} ?> <?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
					<?php if($task['Task']['parent_id'] == null): ?>
					<span class="origin">神</span>
					<?php elseif($task['Task']['childCount'] != 0): ?>
					<span class="accordion opne glyphicon glyphicon-expand"></span>
					<?php else: ?>
					<span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
					<?php endif; ?>
					<span class="body edit-task"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id']));?></span>
					<span class="start_time"><?php echo h($task['Task']['start_time']); ?></span>
					<span class="status"><?php echo h($task['Task']['status']); ?></span>
					<span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
					<span class="<?php echo h($task['Task']['status']=='notyet'?'divide-task':'disable-divide btn-disabled');?>"><span class="glyphicon glyphicon-plus-sign"></span>分割</span>
					<span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>
				</li>
			<?php endif; ?>
			<?php $prev = $task['Task']['indent']; ?>
		<?php endforeach; ?>
		<?php echo str_repeat('</ul>', $prev); ?>
	</ul>
</div>
