<h2>Completeタスク一覧</h2>
<div id="complete">
    <ul class="list-group" id="task-list-complete">
        <?php if (count($complete)): ?>
            <?php foreach ($complete as $comp): ?>
                <li id="complete_<?php echo h($comp['Task']['id']); ?>" class="list-group-item clearfix" data-task-id="<?php echo h($comp['Task']['id']); ?>">
                    <span class="body">
                        <?php echo __(h($comp['Task']['body'])); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="empty list-group-item clearfix">タスクがありません</li>
        <?php endif; ?>
   </ul>
</div>