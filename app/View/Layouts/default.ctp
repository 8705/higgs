<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>
		<?php echo __('ToDo(B)'); ?>
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
</head>
<body>
<div class="container">
	<?php if($this->params["controller"] != 'users'): ?>
		<div id="header" class="row clearfix">
			<div class="col-md-12 column">
				<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
					<div class="row clearfix">
						<div class="col-md-1 column navbar-header">
					 		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> <a class="navbar-brand" href="#">ToDo(B)</a>
						</div>
						<div id="d-bar" class="col-md-10 column progress progress-striped">
							<?php foreach($bar as $val): ?>
								<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $val; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $val; ?>%">
									<?php echo round($val)."%"; ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="col-md-1 column">
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown">
							 		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class=" glyphicon glyphicon-cog"></i> Setting<strong class="caret"></strong></a>
									<ul class="dropdown-menu">
										<li>
											<a href="#">アカウント設定</a>
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
					</div>
				</nav>
			</div>
		</div>
	<?php endif; ?>
	<div id="noticePanel"></div>
	<div id="main" class="row clearfix">
		<?php if($this->params["controller"] != 'users'): ?>
				<div id="side-menu" class="tasks col-md-4 column">
					<p id="clean-bomb" class="btn btn-danger">Bomb->Notyet ボタン</p>
					<div class="tasks">
						<?php echo $this->Form->create('Task'); ?>
						<fieldset>
						<?php
							echo $this->Form->input('user_id', array('type'=>'hidden', 'default' => $author['id']));
							echo $this->Form->input('body', array('placeholder' => 'Add Project'));
							echo $this->Form->input('start_time', array('type'=>'text', 'class' => 'datepicker','readonly' => 'readonly'));
							//ajax送信用設定
							echo $this->Js->submit('Submit', array(
								'url'		=> '/tasks/add',
								'type'		=> 'json',
								'success'	=> 'addTask(data, textStatus)',
								'error'		=> 'popUpPanel(true, "サーバーエラー")',
								'async'		=> true,
								'class' 	=> 'btn btn-primary',
								'complete'  => '$("input.datepicker").val(getFutureDate(0))'
							));
						?>
						</fieldset>
						<?php echo $this->Form->end(); ?>
					</div>
					<div class="tasks parents">
						<h2>Projects</h2>
						<ul class="list-group" id="task-list-parents">
							<?php if (count($parents)): ?>
								<?php foreach ($parents as $parent): ?>
									<li id="parent_<?php echo h($parent['Task']['id']); ?>" class="notyet list-group-item clearfix" data-task-id="<?php echo h($parent['Task']['id']); ?>">
										<span class="body"><?php echo $this->Html->link(__(h($parent['Task']['body'])), array('controller'=>'tasks', 'action' => 'view', $parent['Task']['id'])); ?></span>
										<span class="complete"><?php echo $parent['Task']['complete'].'%'; ?></span>
										<span class="delete-task"><span class="glyphicon glyphicon-trash"></span><b>削除</b></span>
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
							<li><?php echo $this->Html->link(__('List Bombs'), array('controller'=>'tasks', 'action' => 'bomb')); ?> </li>
							<li><?php echo $this->Html->link(__('カレンダー表示'), array('controller'=>'calendars', 'action' => 'viewcalendar')); ?></li>
						</ul>
					</div>
				</div>
		<?php endif; ?>
		<div id="tasks" class="index col-md-8 column">
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<div id="footer" class="row clearfix">
		<div class="col-md-12 column">
			<p class="text-center">
				&copy;2013 temp-space. All rights Reserved.
			</p>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</div>
<!-- ajax用 -->
<?php echo isset($token)?"<script>var token = '{$token}'</script>":"";?>
<?php echo $this->Html->script('ajax'); ?>
<?php echo $this->Js->writeBuffer(array( 'inline' => 'true')); ?>
<script>
	$(document).ready(function() {
		$('input.datepicker').Zebra_DatePicker({
	        direction : [getFutureDate(0), false],
	        first_day_of_week : 0
	    });
	    $('input.datepicker').val(getFutureDate(0));
	});
</script>
<?php echo $this->Html->script('jquery-ui-1.10.3.custom.min'); ?>
</body>
</html>
