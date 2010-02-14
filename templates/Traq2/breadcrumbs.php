<div class="breadcrumbs">
	<small><? if(is_project($uri->seg[0])) { ?><a href="<?=$uri->anchor($project['slug'])?>"><?=$project['name']?></a><? } ?><? foreach($breadcrumbs as $crumb) { ?> > <a href="<?=$crumb['url']?>"><?=$crumb['label']?></a><? } ?></small>
</div><br>
