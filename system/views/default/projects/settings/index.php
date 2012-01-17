<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<form action="" method="post" class="tabular box">
		<div class="group">
			<label><?php echo l('name'); ?></label>
			<?php echo Form::text('name', array('value' => $project->name)); ?>
		</div>
		<div class="group">
			<label><?php echo l('slug'); ?></label>
			<?php echo Form::text('slug', array('value' => $project->slug)); ?> <abbr title="<?php echo l('help:slug'); ?>">?</abbr>
		</div>
		<div class="group">
			<label><?php echo l('Description'); ?></label>
			<?php echo Form::textarea('info', array('value' => $project->info)); ?>
		</div>
		<div class="group">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>