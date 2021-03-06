<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>
		<?php echo __('Higgs'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
	<meta name="description" content="">
	<meta name="author" content="Shungo Ishino">

	<?php echo $this->Html->css('bootstrap.min'); ?>
	<?php echo $this->Html->css('bootstrap-responsive.min'); ?>
	<?php echo $this->Html->css('zebra-default'); ?>
	<?php echo $this->Html->css('style'); ?>
	<?php echo $this->Html->script('jquery.min'); ?>
	<?php echo $this->Html->script('bootstrap.min'); ?>
	<?php echo $this->Html->script('zebra_datepicker'); ?>
	<?php echo $this->Html->script('script'); ?>
</head>
<body>
<div class="container">
	<?php if($this->params["controller"] == 'tasks' or $this->params["controller"] == 'calendars'): ?>
		<div id="header" class="row clearfix">
			<nav class="col-md-12 column navbar-inverse navbar navbar-default navbar-fixed-top" role="navigation">
				<div class="row clearfix">
					<div class="col-md-2 col-xs-2 col-sm-2 column navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> <a class="navbar-brand" href="/">ホーム</a>
					</div>
					<div id="d-bar" class="col-md-8 col-xs-8 col-sm-8 column progress progress-striped">
						<?php foreach($bar as $id => $val): ?>
							<div class="parent_<?php echo $id; ?> jshover progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $val; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $val; ?>%;<?php if($val == 0)echo 'border-right: none;' ?>">
								<?php echo round($val)."kg"; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="col-md-2 col-xs-2 col-sm-2 column">
						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown">
						 		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class=" glyphicon glyphicon-cog"></i> Setting<strong class="caret"></strong></a>
								<ul class="dropdown-menu">
									<!-- <li>
										<a href="#">アカウント設定</a>
									</li> -->
									<li class="divider">
									</li>
									<li>
										<?php echo $this->Html->link(__('ログアウト'), array('controller' => 'users', 'action' => 'logout')); ?>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	<?php endif; ?>
	<div id="noticePanel"></div>
	<div id="main" class="row clearfix">
		<div id="tasks" class="index col-md-8 col-md-push-4 column">
			<?php echo $this->fetch('content'); ?>
		</div>
		<?php if($this->params["controller"] == 'tasks' or $this->params["controller"] == 'calendars'): ?>
				<div id="side-menu" class="tasks col-md-4 col-md-pull-8 column">
					<p class="username">ようこそ、<?php echo h($author['username']); ?>さん</p>
					<div class="actions">
						<ul>
							<li><?php echo $this->Html->link(__('今日のタスク'), array('controller'=>'tasks', 'action' => 'index')); ?> </li>
							<li><?php echo $this->Html->link(__('爆発タスク一覧'), array('controller'=>'tasks', 'action' => 'bomb')); ?> </li>
							<li><?php echo $this->Html->link(__('Completeタスク一覧'), array('controller'=>'tasks', 'action' => 'viewcomplete')); ?> </li>
							<li><?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'viewcalendar')); ?></li>
						</ul>
					</div>
					<div id="projects">
						<h2><?php echo __('Projects'); ?></h2>
						<ul class="list-group" id="task-list-parents">
							<?php $bomb = 'false'; ?>
							<?php if (count($parents)): ?>
								<?php foreach ($parents as $parent): ?>
									<li class="parent_<?php echo h($parent['Task']['id']); ?> jshover parent_<?php echo $parent['Task']['status'];?> list-group-item clearfix" data-task-id="<?php echo h($parent['Task']['id']); ?>">
										<span class="body"><?php echo $this->Html->link(__(h($parent['Task']['body'])), array('controller'=>'tasks', 'action' => 'view', $parent['Task']['id'])); ?></span>
										<span class="attainment <?php if($parent['Task']['complete'] == 100)echo 'complete btn btn-danger'; ?>"><?php if($parent['Task']['complete'] == 100) {echo 'Complete!!';} else{echo $parent['Task']['complete'].'%';} ?></span>
										<span class="selfbomb"><b>自爆</b></span>
									</li>
									<?php if($parent['Task']['status'] ==='bomb')$bomb='true'; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<li class="empty list-group-item clearfix">タスクがありません</li>
							<?php endif; ?>
							<li>
								<?php echo $this->Form->create('Task'); ?>
								<fieldset>
									<?php
										echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $author['id']));
										echo $this->Form->input('dbar', array('type'=>'hidden', 'default' => array_sum($bar)));
										echo $this->Form->input('body', array('placeholder' => 'Add Project'));
										echo $this->Form->input('start_time', array('type'=>'text', 'class' => 'datepicker','readonly' => 'readonly'));
										echo $this->Js->submit('Submit', array(
											'url'		=> '/tasks/add',
											'type'		=> 'json',
											'success'	=> 'addTask(data, textStatus)',
											'error'		=> 'popUpPanel(true, "サーバーエラー")',
											'async'		=> true,
											'class' 	=> 'btn btn-primary',
											'complete'	=> '$("input.datepicker").val(getFutureDate(0))'
										));
									?>
								</fieldset>
								<?php echo $this->Form->end(); ?>
							</li>
						</ul>
					</div>
					<?php if($bomb == 'true'): ?>
						<p id="clean-bomb" class="btn btn-danger">爆発したタスクがあります。</p>
					<?php endif; ?>
				</div>
		<?php endif; ?>
	</div>
	<div id="footer" class="row clearfix">
		<div class="col-md-12 column">
			<p class="about">
				<a href="/">ホーム</a>
				<span>|</span>
				<a href="/suports/rule">このサイトについて</a>
			</p>
			<p class="text-center">
				&copy;2013-2014 PYNS CREATE. All rights Reserved.
			</p>
		</div>
	</div>
</div>
<!-- ajax用 -->
<?php echo isset($token)?"<script>var token = '{$token}'</script>":"";?>
<?php echo $this->Html->script('ajax'); ?>
<?php echo $this->Js->writeBuffer(array( 'inline' => 'true')); ?>
<?php echo $this->Html->script('jquery-ui-1.10.3.custom.min'); ?>
<?php if($author['username'] == 'egami' || $author['username'] == 'ishino'): ?>
<?php echo $this->Html->script('no-analytics'); ?>
<?php else: ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-19902347-18', '8705.co');
  ga('send', 'pageview');

</script>
<?php endif; ?>
</body>
</html>
