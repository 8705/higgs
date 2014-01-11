<h2>ログイン</h2>
<?php echo $this->Form->create('User',
	array('action'=>'login', 'class'=>'form-horizontal', 'role'=>'form')
);?>
	<div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">User Id</label>
		<div class="col-sm-10">
			<?php echo $this->Form->input('username', array('class'=>'form-control')); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword3" class="col-sm-2 control-label">Password</label>
		<div class="col-sm-10">
			<?php echo $this->Form->input('password', array('type'=>'password', 'class'=>'form-control', 'id'=>'inputPassword3')); ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				 <label><input type="checkbox" /> Remember me</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<?php echo $this->Form->button('Log in', array('class'=>'btn btn-primary')); ?>
		</div>
	</div>
</form>
<p class="lead-to-signup"><?php echo $this->Html->link(__('アカウントをお持ちでない方はこちら'), array('controller' => 'users', 'action' => 'register')); ?></p>
