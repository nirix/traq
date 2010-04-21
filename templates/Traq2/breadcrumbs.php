<div class="breadcrumbs">
	<small><?php if(is_project($uri->seg[0])) { ?><a href="<?php echo $uri->anchor($project['slug'])?>"><?php echo $project['name']?></a><?php } ?><?php foreach($breadcrumbs as $crumb) { ?> > <a href="<?php echo $crumb['url']?>"><?php echo $crumb['label']?></a><?php } ?></small>
</div>
