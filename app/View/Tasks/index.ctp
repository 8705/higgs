<h2>今日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/today">D値並べ替え</a>
<ul class="list-group task-list sort-list" id="task-list-today">
	<?php echo count($tasks_today); ?>
<?php if (count($tasks_today)): ?>
	<?php foreach ($tasks_today as $task): ?>
		<?php if($task['Task']['status'] == 'bomb'): ?>
			<li class="<?php echo h($task['Task']['status']);?> list-group-item clearfix">メリークリスマス！このタスクは爆発させたよ！ハハハ！サンタから爆弾のプレゼントだよ♫</li>
		<?php else: ?>
			<?php echo $this->element('/Tasks/tasklist',array('task' =>$task,'bar'=>$bar)); ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="empty list-group-item clearfix">タスクがありません</li>
<?php endif; ?>
</ul>
<h2>明日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/tomorrow">D値並べ替え</a>
<ul class="list-group task-list sort-list" id="task-list-tomorrow">
<?php if (count($tasks_tomorrow)): ?>
	<?php foreach ($tasks_tomorrow as $task): ?>
		<?php if($task['Task']['status'] == 'bomb'): ?>
			<li class="<?php echo h($task['Task']['status']);?> list-group-item clearfix">メリークリスマス！このタスクは爆発させたよ！ハハハ！サンタから爆弾のプレゼントだよ♫</li>
		<?php else: ?>
			<?php echo $this->element('/Tasks/tasklist',array('task' =>$task,'bar'=>$bar)); ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="empty list-group-item clearfix">タスクがありません</li>
<?php endif; ?>
</ul>
<h2>明後日</h2><a class="btn btn-success sort-link" href="/tasks/sort/d/dayaftertomorrow">D値並べ替え</a>
<ul class="list-group task-list sort-list" id="task-list-dayaftertomorrow">
<?php if (count($tasks_dayaftertomorrow)): ?>
	<?php foreach ($tasks_dayaftertomorrow as $task): ?>
		<?php if($task['Task']['status'] == 'bomb'): ?>
			<li class="<?php echo h($task['Task']['status']);?> list-group-item clearfix">メリークリスマス！このタスクは爆発させたよ！ハハハ！サンタから爆弾のプレゼントだよ♫</li>
		<?php else: ?>
			<?php echo $this->element('/Tasks/tasklist',array('task' =>$task,'bar'=>$bar)); ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="empty list-group-item clearfix">タスクがありません</li>
<?php endif; ?>
</ul>