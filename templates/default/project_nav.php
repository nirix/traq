	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'])?>">Project Info</a></li><?
			?><li<?=($uri->seg[1] == "roadmap" || $uri->seg[1] == "milestone" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'roadmap')?>">Roadmap</a></li><?
			?><li class="<?=($uri->seg[1] == "tickets" || $uri->seg[1] == "ticket" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'tickets')?>">Tickets</a></li><?
			?><li<?=($uri->seg[1] == "timeline" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'timeline')?>">Timeline</a></li><?
			?><li<?=($uri->seg[1] == "changelog" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'changelog')?>">Change Log</a></li><?
			?><? if($project['sourcelocation'] != '') {
			?><li class="<?=($uri->seg[1] == "source" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'source')?>">Browse Source</a></li><? } ?><? if($user->group->createtickets) {
			?><li class="<?=($uri->seg[1] == "newticket" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'newticket')?>">New Ticket</a></li>
			<? } ?>
		</ul>
	</div>