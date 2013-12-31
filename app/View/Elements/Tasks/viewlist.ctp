<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php if($task['Task']['parent_id'] == null){echo 'origin';} ?> <?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task['Task']['id']); ?>">
     <?php if($task['Task']['childCount'] != 0 && $task['Task']['parent_id'] == null): ?>
     <span class="origin glyphicon glyphicon-flag"></span>
     <?php elseif($task['Task']['childCount'] != 0): ?>
     <span class="accordion spread glyphicon glyphicon-expand"></span>
     <?php else: ?>
     <span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
     <?php endif; ?>
     <span class="body <?php if($task['Task']['status'] == 'notyet'){echo 'edit-task';} ?>"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id']));?></span>
     <span class="status"><?php echo h($task['Task']['status']); ?></span>
     <span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
     <span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>
     <span class="start_time"><?php echo h(str_replace('-','/',substr($task['Task']['start_time'],5))); ?></span>
</li>