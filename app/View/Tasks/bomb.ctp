<h2>爆発タスク一覧</h2>
<div class="tasks bombs">
    <ul class="list-group" id="task-list-bombs">
        <?php if (count($bombs)): ?>
            <?php foreach ($bombs as $bomb): ?>
                <li id="bomb_<?php echo h($bomb['Task']['id']); ?>" class="<?php echo h($bomb['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($bomb['Task']['id']); ?>">
                    <span class="body">
                        <?php echo __(h($bomb['Task']['body'])); ?>
                    </span>
                    <span class="num_bomb">
                        <?php echo __(h('爆発回数：'.$bomb['Task']['num_bomb'].'回')); ?>
                    </span>
                    <p id="tryagain" class="btn btn-danger">今度こそ！</p>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="empty list-group-item clearfix">タスクがありません</li>
        <?php endif; ?>
   </ul>
</div>