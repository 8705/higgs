<h2>Completeタスク一覧</h2>
<div class="tasks complete">
    <ul class="list-group" id="task-list-complete">
        <?php if (count($complete)): ?>
            <?php foreach ($complete as $complete): ?>
                <li id="complete_<?php echo h($complete['Task']['id']); ?>" class="list-group-item clearfix" data-task-id="<?php echo h($complete['Task']['id']); ?>">
                    <span class="body">
                        <?php echo $this->Html->link(__(h($complete['Task']['body'])), array('action' => 'view', $complete['Task']['id'])); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="empty list-group-item clearfix">タスクがありません</li>
        <?php endif; ?>
   </ul>
</div>