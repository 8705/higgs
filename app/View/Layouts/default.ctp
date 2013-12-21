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
							<a href="#">ホーム</a>
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
	<div id="main" class="row clearfix">
		<?php echo $this->fetch('content'); ?>
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
<p>ないけどしよか？</p>
<p>この場所わかりづらいわ</p>
<p>さらに編集</p>
<p>もっと編集</p>
<p>もっかい編集</p>
<p>うまくいってる？</p>
<p>ぱいんず会長です。こんばんは</p>
</body>
</html>
