<h2><?php _l('projects')?></h2>

<ul class="list projects_list">
	<?php foreach($projects as $project) { ?>
	<li>
		<h3><?php echo HTML::link(baseurl($project['slug']), $project['name'])?></h3>
		<div class="quick_nav"><?php echo HTML::link(baseurl($project['slug'],'roadmap'), l('roadmap'))?> | <?php echo HTML::link(baseurl($project['slug'],'timeline'), l('timeline'))?> | <?php echo HTML::link(baseurl($project['slug'],'tickets'), l('tickets'))?></div>
		<div class="info"><?php echo formattext($project['info'])?></div>
	</li>
	<?php }?>
</ul>