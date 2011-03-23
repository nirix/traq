<h2><?php _l('Milestone_x', $milestone['milestone'])?></h2>
<?php View::render('projects/_milestone_progress', array('milestone'=>$milestone))?>
<?php echo formattext($milestone['info'])?>