<h2><?php _l('Roadmap')?></h2>

<ul class="list milestones_list">
	<?php foreach($milestones as $milestone) { ?>
	<li>
		<h3><?php echo HTML::link(baseurl($projectinfo['slug'],'milestones',$milestone['slug']), $milestone['milestone'])?></h3>
		<div class="info"><?php echo formattext($milestone['info'])?></div>
	</li>
	<?php } ?>
</ul>