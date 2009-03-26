	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'])?>"><?=l('project_info')?></a></li><?
			?><li<?=($uri->seg[1] == "roadmap" || $uri->seg[1] == "milestone" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'roadmap')?>"><?=l('roadmap')?></a></li><?
			?><li class="<?=($uri->seg[1] == "tickets" || $uri->seg[1] == "ticket" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'tickets')?>"><?=l('tickets')?></a></li><?
			?><li<?=($uri->seg[1] == "timeline" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'timeline')?>"><?=l('timeline')?></a></li><?
			?><li<?=($uri->seg[1] == "changelog" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'changelog')?>"><?=l('change_log')?></a></li><?
			?><? if($project['sourcelocation'] != '') {
			?><li class="<?=($uri->seg[1] == "source" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'source')?>"><?=l('browse_source')?></a></li><? } ?><? if($user->group->createtickets) {
			?><li class="<?=($uri->seg[1] == "newticket" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'newticket')?>"><?=l('new_ticket')?></a></li>
			<? } ?>
		</ul>
	</div>