<div class="users index container">
	<div class="row clearfix">
		<div id="top-img" class="col-md-6 column">
			<div class="carousel slide" id="carousel-707106">
				<ol class="carousel-indicators">
					<li class="active" data-slide-to="0" data-target="#carousel-707106">
					</li>
					<li data-slide-to="1" data-target="#carousel-707106">
					</li>
					<li data-slide-to="2" data-target="#carousel-707106">
					</li>
				</ol>
				<div class="carousel-inner">
					<div class="item active">
						<img alt="" src="http://lorempixel.com/1600/500/sports/1" />
						<div class="carousel-caption">
							<h4>
								First Thumbnail label
							</h4>
							<p>
								Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
							</p>
						</div>
					</div>
					<div class="item">
						<img alt="" src="http://lorempixel.com/1600/500/sports/2" />
						<div class="carousel-caption">
							<h4>
								Second Thumbnail label
							</h4>
							<p>
								Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
							</p>
						</div>
					</div>
					<div class="item">
						<img alt="" src="http://lorempixel.com/1600/500/sports/3" />
						<div class="carousel-caption">
							<h4>
								Third Thumbnail label
							</h4>
							<p>
								Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.
							</p>
						</div>
					</div>
				</div> <a class="left carousel-control" href="#carousel-707106" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#carousel-707106" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
			</div>
		</div>
		<div class="col-md-6 column">
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
							<label><?php echo $this->Form->checkbox('remember_NAKAI!!!!!!!!'); ?>Remember me</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<?php echo $this->Form->button('Sign in', array('class'=>'btn btn-default')); ?>
					</div>
				</div>
			<?php echo $this->Form->end(); ?>
			<?php echo $this->Form->create('User',
				array('action'=>'register', 'class'=>'form-horizontal', 'role'=>'form')
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
						<?php echo $this->Form->button('Sign up', array('class'=>'btn btn-default')); ?>
					</div>
				</div>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
</div>