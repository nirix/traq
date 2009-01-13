	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'])?>">Project Info</a></li><?
			?><li<?=($uri->seg[1] == "roadmap" ? ' class="active"' : '')?>><a href="<?=$uri->anchor($project['slug'],'roadmap')?>">Roadmap</a></li><?
			?><li class="<?=($uri->seg[1] == "tickets" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'tickets')?>">Tickets</a></li><?
			?><? if($user->loggedin) { ?>
			<li class="<?=($uri->seg[1] == "newticket" ? ' active' : '')?>"><a href="<?=$uri->anchor($project['slug'],'newticket')?>">New Ticket</a></li>
			<? } ?>
		</ul>
	</div>