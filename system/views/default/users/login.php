<section id="login_form" class="tabular content">
	<div class="push-7 span-8">
		<h2 id="page_title">Login</h2>
		<?php if (true) { ?>
		<div class="error">
			<?php echo l('error:invalid_username_or_password'); ?>
		</div>
		<?php } ?>
		<form action="<?php echo Request::base('login'); ?>" method="post" class="box">
			<div class="group">
				<label><?php echo l('username'); ?></label>
				<?php echo Form::text('username'); ?>
			</div>
			<div class="group">
				<label><?php echo l('password'); ?></label>
				<?php echo Form::password('password'); ?>
			</div>
			<div class="group">
				<input type="submit" value="<?php echo l('login'); ?>" />
			</div>
		</form>
	</div>
	<div class="clearfix"></div>
</section>