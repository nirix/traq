<section id="login_form" class="tabular content">
	<div class="push-6 span-11">
		<h2 id="page_title"><?php echo l('login'); ?></h2>
		<?php if (isset($error) and $error) { ?>
		<div class="error">
			<?php echo l('errors.invalid_username_or_password'); ?>
		</div>
		<?php } ?>
		<form action="<?php echo Request::base('login'); ?>" method="post" class="box">
			<?php if (!empty($_SERVER['HTTP_REFERER']) or isset(Request::$post['redirect'])) { ?>
			<input type="hidden" name="redirect" value="<?php echo (isset(Request::$post['redirect'])) ? Request::$post['redirect'] : $_SERVER['HTTP_REFERER']; ?>" />
			<?php } ?>
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