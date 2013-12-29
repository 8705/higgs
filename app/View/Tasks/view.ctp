<div class="tasks view">

	<h3>一族</h3>
	<ul class="list-group task-list children-ul" id="task-list">
		<?php $prev = 0; ?>
		<?php foreach ($tasks as $task): ?>
			<?php $indent = $task['Task']['indent']-$prev ?>
			<?php if($indent == 0): ?>
				<?php echo $this->element('/Tasks/viewlist',array('task' =>$task,'bar'=>$bar)); ?>
			<?php elseif($indent == 1): ?>
				<ul class="children-ul" data-children-ul-id="<?php echo h($task['Task']['parent_id']); ?>">
				<?php echo $this->element('/Tasks/viewlist',array('task' =>$task,'bar'=>$bar)); ?>
			<?php elseif($indent < 0): ?>
				<?php echo str_repeat('</ul>', -$indent) ?>
				<?php echo $this->element('/Tasks/viewlist',array('task' =>$task,'bar'=>$bar)); ?>
			<?php endif; ?>
			<?php $prev = $task['Task']['indent']; ?>
		<?php endforeach; ?>
		<?php echo str_repeat('</ul>', $prev); ?>
	</ul>
</div>
