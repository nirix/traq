<h2><?php _l('Milestone x', $milestone['milestone'])?></h2>
<?php View::render('projects/_milestone_progress', array('milestone'=>$milestone))?>
<div class="project-info">
	<?php echo formattext($milestone['info'])?>
</div>