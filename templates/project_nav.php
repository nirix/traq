	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'])?>">Project Info</a></li>
			<li<?=($uri->seg[1] == "roadmap" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'roadmap')?>">Roadmap</a></li>
			<li class="last<?=($uri->seg[1] == "tickets" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'tickets')?>">Tickets</a></li>
		</ul>
	</div>