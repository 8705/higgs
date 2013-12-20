<div class="tasks form">
<?php echo $this->Form->create('Task'); ?>
	<fieldset>
		<legend><?php echo __('Add Task'); ?></legend>
	<?php
		echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $user_id));
		echo $this->Form->input('parent_id', array('type'=>'hidden', 'default' => $task_id));
		echo $this->Form->input('body');
		echo $this->Form->input('start_time');
		echo $this->Form->input('status', array('type'=>'hidden', 'default' => 'notyet'));
		echo $this->Form->input('d_param', array('type'=>'hidden', 'default' => 1));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Tasks'), array('action' => 'index')); ?></li>
	</ul>
</div>
