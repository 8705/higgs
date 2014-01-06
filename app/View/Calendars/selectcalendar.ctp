<div class="calendar-view">
    <div class="title clearfix">
        <a href="/tasks/view/<?php echo h($id); ?>"><span class="link btn btn-info"><span class="glyphicon glyphicon-flag"></span>プロジェクトツリーで見る</span></a>
    </div>
    <div id="calendarPanel"></div>
	<?php $this->Calendar->make($id, $selectday, $body); ?>
</div>