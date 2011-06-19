<div id="page-content">
	<div class="content">
		<h2 id="page-title">Projects</h2>
		<ul>
	<?php foreach($projects as $project) { ?>
	<li>
		<h3><a href="<?php echo Request::base($project['slug'])?>"><?php echo $project['name']; ?></a></h3>
	</li>
	<?php } ?>
</ul>
	</div>
</div>