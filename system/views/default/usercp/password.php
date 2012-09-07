<div class="usercp password">
	<h2 id="page_title"><?php echo l('usercp'); ?></h2>
</div>
<?php View::render('usercp/_nav'); ?>
<div class="usercp content">
	<?php show_errors($user->errors); ?>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<fieldset id="info" class="box">
			<legend><?php echo l('password'); ?></legend>

			<div class="tabular">
				<div class="group">
					<?php echo Form::label(l('old_password'), 'old_password'); ?>
					<?php echo Form::password('password'); ?>
				</div>
				<div class="group">
					<?php echo Form::label(l('new_password'), 'new_password'); ?>
					<?php echo Form::password('new_password'); ?>
				</div>
				<div class="group">
					<?php echo Form::label(l('confirm_password'), 'confirm_password'); ?>
					<?php echo Form::password('confirm_password'); ?>
				</div>
				<?php FishHook::run('template:users/usercp/password'); ?>
			</div>
		</fieldset>

		<?php FishHook::run('template:users/usercp'); ?>

		<div class="clearfix"></div>
		<div class="actions">
			<?php echo Form::submit(l('save')); ?>
		</div>
	</form>
</div>