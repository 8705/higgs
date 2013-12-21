<div class="tasks view">
<h2><?php echo __('Task'); ?></h2>
	<table class='table'>
	<tr>
			<th><?php echo __('Task'); ?></th>
			<th><?php echo __('Dead Line'); ?></th>
			<th><?php echo __('Status'); ?></th>
			<th><?php echo __('D値'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($tasks_today as $task): ?>
	<tr>
		<td><?php echo h($task['Task']['body']); ?>&nbsp;</td>
		<td><?php echo h($task['Task']['start_time']); ?>&nbsp;</td>
		<td><?php echo h($task['Task']['status']); ?>&nbsp;</td>
		<td><?php echo h($task['Task']['d_param']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $task['Task']['id'])); ?>
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $task['Task']['id'])); ?>
			<?php echo $this->Html->link(__('分割'), array('action' => 'edit', $task['Task']['id'])); ?>
			<?php echo $this->Form->postLink(__('削除'), array('action' => 'delete', $task['Task']['id']), null, __('Are you sure you want to delete # %s?', $task['Task']['id'])); ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Tasks'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'selectcalendar', $task['Task']['id'])); ?></li>
	</ul>
</div>
