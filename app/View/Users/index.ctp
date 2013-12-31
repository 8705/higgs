<dvi class="row">
	<h1>PYNS TODO(仮)(β)</h1>
</dvi>
<div class="main-view col-md-8 col-xs-12 column">
	<h2>ツリー型タスク管理ツール</h2>
	<h3><span class="glyphicon glyphicon-check"></span>ツリー表示と今日やる事表示を切り替えれます</h3>
	<p class="view-swich"><img src="/img/view_swich.png" alt=""></p>
	<h3><span class="glyphicon glyphicon-check"></span>タスクには体重があります</h3>
	<div class="clearfix">
		<p class="hakari"><img src="/img/hakari.png" alt=""></p>
		<ul class="bread">
			<li><span class="glyphicon glyphicon-star-empty"></span>新しいプロジェクトを追加すると、その分体重も増えます</li>
			<li><span class="glyphicon glyphicon-star-empty"></span>抱えることの出来る重さは100kgまでです</li>
			<li><span class="glyphicon glyphicon-star-empty"></span>期限の切れたタスクの体重は毎日増えます</li>
			<li><p><span class="glyphicon glyphicon-star-empty"></span>
				重量オーバーになるとタスクが爆発します（よかったですね、これでもうこのタスクをやらなくて済みますよ）</p>
				<p class="kieru"><img src="/img/kieru.png" alt=""></p>
			</li>
			<li><span class="glyphicon glyphicon-star-empty"></span>現在の体重は常に画面上部で確認できます</li>
		</ul>
	</div>
</div>
<div class="form col-md-4 col-xs-12 column">
	<div class="row">
		<div class="loginform col-md-12 col-xs-6">
			<h3>ログイン</h3>
			<?php echo $this->Form->create('User',
				array('action'=>'login', 'class'=>'form-horizontal', 'role'=>'form')
			);?>
				<div class="form-group">
					<!--<label for="inputEmail3" class="col-sm-2 control-label">User Id</label>-->
					<div class="col-md-10">
						<?php echo $this->Form->input('username', array('class'=>'form-control', 'placeholder' => 'User ID')); ?>
					</div>
				</div>
				<div class="form-group">
					<!-- <label for="inputPassword3" class="col-sm-2 control-label">Password</label> -->
					<div class="col-md-10">
						<?php echo $this->Form->input('password', array('type'=>'password', 'class'=>'form-control', 'id'=>'inputPassword3', 'placeholder' => 'Password')); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10">
						<div class="checkbox">
							<label><?php echo $this->Form->checkbox('remember_me'); ?>Remember me</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10">
						<?php echo $this->Form->button('Sign in', array('class'=>'btn btn-default')); ?>
					</div>
				</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div class="signinform col-md-12 col-xs-6">
			<h3>新規登録登録</h3>
			<?php echo $this->Form->create('User',
				array('action'=>'register', 'class'=>'form-horizontal', 'role'=>'form')
			);?>
				<div class="form-group">
					<!--<label for="inputEmail3" class="col-sm-2 control-label">User Id</label>-->
					<div class="col-md-10">
						<?php echo $this->Form->input('username', array('class'=>'form-control', 'placeholder' => 'User ID')); ?>
					</div>
				</div>
				<div class="form-group">
					<!--<label for="inputEmail3" class="col-sm-2 control-label">Email</label>-->
					<div class="col-md-10">
						<?php echo $this->Form->input('email', array('type'=>'email', 'class'=>'form-control', 'id'=>'inputEmail3', 'placeholder' => 'E-mail')); ?>
					</div>
				</div>
				<div class="form-group">
					<!--<label for="inputPassword3" class="col-sm-2 control-label">Password</label>-->
					<div class="col-md-10">
						<?php echo $this->Form->input('password', array('type'=>'password', 'class'=>'form-control', 'id'=>'inputPassword3', 'placeholder' => 'Password')); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10">
						<?php echo $this->Form->button('Sign up', array('class'=>'btn btn-default')); ?>
					</div>
				</div>
			<?php echo $this->Form->end(); ?></div>
		</div>
</div>

