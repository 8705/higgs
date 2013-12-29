<li id="task_<?php echo h($task['Task']['id']); ?>" class="<?php echo h($task['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($task  ['Task']['id']); ?>">
    <!--style="background-color: hsl(<?php //echo 0; ?>,100%,<?php //echo 100-70*$task['Task']['d_param']/$bar; ?>%) -->
    <span class="check-task"><input type="checkbox" <?php if($task['Task']['status'] == 'done'){echo h('checked');} ?>></span>
    <span class="body edit-task"><?php echo $this->Html->link(__(h($task['Task']['body'])), array('action' => 'view', $task['Task']['id'])); ?></span>

    <span class="status"><?php echo h($task['Task']['status']); ?></span>
    <span class="d_param"><?php echo h($task['Task']['d_param']); ?></span>
    <span class="delete-task"><span class="glyphicon glyphicon-trash"></span>削除</span>
    <span class="start_time"><?php echo h(str_replace('-','/',substr($task['Task']['start_time'],5))); ?></span>
    <span class="sequence" style="display:none;"><?php echo h($task['Task']['sequence']); ?></span>
</li>