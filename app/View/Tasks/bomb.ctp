<h2>Bombs</h2>
<div class="tasks bombs">
    <ul class="list-group" id="task-list-bombs">
        <?php if (count($bombs)): ?>
            <?php foreach ($bombs as $bomb): ?>
                <li id="bomb_<?php echo h($bomb['Task']['id']); ?>" class="<?php echo h($bomb['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($bomb['Task']['id']); ?>">
                    <span class="body">
                        <?php echo $this->Html->link(__(h($bomb['Task']['body'])), array('action' => 'view', $bomb['Task']['id'])); ?>
                    </span>
                    <span class="num_bomb">
                        <?php echo __(h($bomb['Task']['num_bomb'])); ?>
                    </span>
                    <p id="tryagain" class="btn btn-danger">今度こそ！</p>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="empty list-group-item clearfix">タスクがありません</li>
        <?php endif; ?>
   </ul>
</div>