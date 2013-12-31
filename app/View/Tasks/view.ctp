<div class="tasks view">

	<div class="title clearfix">
		<h3>プロジェクト</h3>
		<a href="/calendars/selectcalendar/<?php echo h($id); ?>"><span class="link btn btn-info"><span class="glyphicon glyphicon-calendar"></span>カレンダーで見る</span></a>
	</div>
	<div id="viewWrapper">
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
</div>
