<div class="usercp content">
	<h2 id="page_title"><?php echo l('usercp'); ?></h2>

	<?php show_errors($user->errors); ?>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<fieldset id="info" class="box">
			<legend><?php echo l('information'); ?></legend>

			<div class="tabular">
				<div class="group">
					<?php echo Form::label(l('password'), 'password'); ?>
					<?php echo Form::password('password'); ?>
				</div>
				<div class="group">
					<?php echo Form::label(l('name'), 'name'); ?>
					<?php echo Form::text('name', array('value' => $user->name)); ?>
				</div>
				<div class="group">
					<?php echo Form::label(l('email'), 'email'); ?>
					<?php echo Form::text('email', array('value' => $user->email)); ?>
				</div>
				<div class="group">
					<?php echo Form::label(l('new_password'), 'new_password'); ?>
					<?php echo Form::password('new_password'); ?>
				</div>
				<?php FishHook::run('template:users/usercp/info'); ?>
			</div>
		</fieldset>

		<fieldset id="options" class="box">
			<legend><?php echo l('options'); ?> [NO YET IMPLEMENTED]</legend>
			<div class="tabular">
				<div class="group">
					<?php echo Form::label(l('watch_my_new_tickets'), 'watch_my_new_tickets'); ?>
					<?php echo Form::checkbox('watch_my_new_tickets', 1); ?>
				</div>
			</div>
		</fieldset>

		<?php FishHook::run('template:users/usercp'); ?>

		<div class="clearfix"></div>
		<div class="actions">
			<?php echo Form::submit(l('save')); ?>
		</div>
	</form>
</div>