<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>
		<?php echo __('ToDo(B)'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Shungo Ishino">

	<?php echo $this->Html->css('bootstrap.min'); ?>
	<?php echo $this->Html->css('bootstrap-responsive.min'); ?>
	<?php echo $this->Html->css('zebra-default'); ?>
	<?php echo $this->Html->css('style'); ?>
	<?php echo $this->Html->script('jquery.min'); ?>
	<?php echo $this->Html->script('bootstrap.min'); ?>
	<?php echo $this->Html->script('zebra_datepicker'); ?>
</head>
<body>
<div class="container">
	<div id="header" class="row clearfix">
		<div class="col-md-12 column">
			<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
				<div class="navbar-header">
					 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> <a class="navbar-brand" href="#">ToDo(B)</a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active">
							<a href="/">ホーム</a>
						</li>
						<li>
							<!-- <a href="#">My アカウント</a> -->
							<a href="#"><?php if(isset($author['username']))echo h($author['username']); ?></a>
						</li>
						<li class="dropdown">
							 <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown<strong class="caret"></strong></a>
							<ul class="dropdown-menu">
								<li>
									<a href="#">Action</a>
								</li>
								<li>
									<a href="#">Another action</a>
								</li>
								<li>
									<a href="#">Something else here</a>
								</li>
								<li class="divider">
								</li>
								<li>
									<a href="#">Separated link</a>
								</li>
								<li class="divider">
								</li>
								<li>
									<a href="#">One more separated link</a>
								</li>
							</ul>
						</li>
					</ul>
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
							<input type="text" class="form-control">
						</div> <button type="submit" class="btn btn-default">検索</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="#">Link</a>
						</li>
						<li class="dropdown">
							 <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class=" glyphicon glyphicon-cog"></i> Setting<strong class="caret"></strong></a>
							<ul class="dropdown-menu">
								<li>
									<a href="#">アカウント設定</a>
								</li>
								<li>
									<a href="#">あんなことや</a>
								</li>
								<li>
									<a href="#">こんなことを</a>
								</li>
								<li>
									<a href="#">やろうとしている</a>
								</li>
								<li class="divider">
								</li>
								<li>
									<?php echo $this->Html->link(__('ログアウト'), array('controller' => 'users', 'action' => 'logout')); ?>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</div>
	<div id="noticePanel"></div>	
	<div id="main" class="row clearfix">
		<div class="tasks side col-md-4 column">
			<h2><?php echo __(h($username)); ?></h2>
			<div class="d-bar progress progress-striped">
		<div id="d-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo 100*$bar/dcapacity; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo 100*$bar/dcapacity; ?>%">
			<?php echo 100*$bar/dcapacity."%"; ?>
		</div>
	</div>
			<p id="clean-bomb" class="btn btn-danger">Bomb一括削除</p>
			<div class="tasks">
				<?php echo $this->Form->create('Task'); ?>
			<fieldset>
				<?php
					echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $author['id']));//$user_idから$authorに変更
					echo $this->Form->input('body', array('placeholder' => 'Add Project'));
					echo $this->Form->input('start_time', array('type'=>'text', 'class' => 'datepicker','readonly' => 'readonly'));
				?>
				<?php
					//ajax送信用設定
					echo $this->Js->submit('Submit', array(
						'url'		=> '/tasks/add',
						'type'		=> 'json',
						'success'	=> 'addTask(data, textStatus)',
						'error'		=> 'popUpPanel(true, "サーバーエラー")',
						'async'		=> true,
						'class' 	=> 'btn btn-primary'
						)
					);
				?>
			</fieldset>
				<?php echo $this->Form->end(); ?>
			</div>
			<div class="tasks parents">
				<h2>Parents</h2>
				<ul class="list-group" id="task-list-parents">
					<?php if (count($parents)): ?>
						<?php foreach ($parents as $parent): ?>
							<li id="parent_<?php echo h($parent['Task']['id']); ?>" class="<?php echo h($parent['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($parent['Task']['id']); ?>">
							<span class="body"><?php echo $this->Html->link(__(h($parent['Task']['body'])), array('controller'=>'tasks', 'action' => 'view', $parent['Task']['id'])); ?></span>
							<span><?php echo $parent['Task']['complete'].'%'; ?></span>
							</li>
						<?php endforeach; ?>
					<?php else: ?>
						<li class="empty list-group-item clearfix">タスクがありません</li>
					<?php endif; ?>
				</ul>
			</div>
			<div class="tasks bombs">
				<h2>Bombs</h2>
				<ul class="list-group" id="task-list-bombs">
				<?php if (count($bombs)): ?>
				<?php foreach ($bombs as $bomb): ?>
					<li id="bomb_<?php echo h($bomb['Task']['id']); ?>" class="<?php echo h($bomb['Task']['status']);?> list-group-item clearfix" data-task-id="<?php echo h($bomb['Task']['id']); ?>">
						<span class="body"><?php echo $this->Html->link(__(h($bomb['Task']['body'])), array('action' => 'view', $bomb['Task']['id'])); ?></span>
					</li>
				<?php endforeach; ?>
				<?php else: ?>
					<li class="empty list-group-item clearfix">タスクがありません</li>
				<?php endif; ?>
				</ul>
			</div>
			<div class="actions">
				<h3><?php echo __('Actions'); ?></h3>
				<ul>
					<li><?php echo $this->Html->link(__('List Tasks'), array('controller'=>'tasks', 'action' => 'index')); ?> </li>
					<li><?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'viewcalendar')); ?></li>
				</ul>
			</div>
		</div>
		<div id="tasks" class="index col-md-8 column">
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
	<div id="footer" class="row clearfix">
		<div class="col-md-12 column">
			<p class="text-center">
				&copy;2013 temp-space. All rights Reserved.
			</p>
		</div>
	</div>
</div>
<!-- ajax用 -->
<?php echo isset($token)?"<script>var token = '{$token}'</script>":"";?>
<?php echo $this->Html->script('ajax'); ?>
<?php echo $this->Js->writeBuffer(array( 'inline' => 'true')); ?>
<script>
	$(document).ready(function() {
		$('input.datepicker').Zebra_DatePicker({
	        direction : [getFutureDate(0), false]
	    });
	    $('input.datepicker').val(getFutureDate(0));
	});
</script>
<?php echo $this->Html->script('jquery-ui-1.10.3.custom.min'); ?>
</body>
</html>
