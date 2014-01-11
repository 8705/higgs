<h2>新規登録</h2>
<?php echo $this->Form->create('User',
	array('action'=>'register', 'class'=>'form-horizontal', 'role'=>'form','autocomplete'=>'off')
);?>
	<div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">User Id</label>
		<div class="col-sm-10">
			<?php echo $this->Form->input('username', array('class'=>'form-control')); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-10">
			<?php echo $this->Form->input('email', array('type'=>'email', 'class'=>'form-control', 'id'=>'inputEmail3')); ?>
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
			 <?php echo $this->Form->button('Sign up', array('class'=>'btn btn-primary')); ?>
		</div>
	</div>
</form>