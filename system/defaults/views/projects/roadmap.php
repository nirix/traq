<h2><?php _l('Roadmap')?></h2>

<ul class="list milestones_list">
	<?php foreach($milestones as $milestone) { ?>
	<li>
		<h3><?php echo HTML::link(baseurl($traq->project['slug'],'milestones',$milestone['slug']), $milestone['milestone'])?></h3>
		<?php View::render('projects/_milestone_progress', array('milestone'=>$milestone))?>
		<div class="info"><?php echo formattext($milestone['info'])?></div>
	</li>
	<?php } ?>
</ul>