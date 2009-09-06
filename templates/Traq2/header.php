<div id="head">
			<span><a href="<?=$uri->anchor()?>"><?=settings('title')?></a> <? if(is_project(PROJECT_SLUG)) { ?>/ <?=$project['name']?><? } ?></span>
			<div id="nav">
				<div id="meta_nav">
					<a href="#"><?=l('login')?></a> | <a href="#"><?=l('register')?></a>
				</div>
				<? if(is_project(PROJECT_SLUG)) { ?>
				<ul>
					<li class="first<?=iif(empty($uri->seg[1]),' active')?>"><a href="<?=$uri->anchor($project['slug'])?>"><?=l('project_info')?></a></li>
					<li<?=iif($uri->seg[1]=='roadmap' or preg_match('/milestone-(?<slug>.*)/',$uri->seg[1]),' class="active"')?>><a href="<?=$uri->anchor($project['slug'],'roadmap')?>"><?=l('roadmap')?></a></li>
					<li<?=iif($uri->seg[1]=='timeline',' class="active"')?>><a href="<?=$uri->anchor($project['slug'],'timeline')?>"><?=l('timeline')?></a></li>
					<li<?=iif($uri->seg[1]=='tickets' or preg_match('/ticket-(?<ticket_id>.*)/',$uri->seg[1]),' class="active"')?>><a href="<?=$uri->anchor($project['slug'],'tickets')?>"><?=l('tickets')?></a></li>
					<li<?=iif($uri->seg[1]=='changelog',' class="active"')?>><a href="<?=$uri->anchor($project['slug'],'changelog')?>"><?=l('changelog')?></a></li>
					
					<? if($user->group['create_tickets']) { ?>
					<li class="standalone<?=iif($uri->seg[1]=='newticket',' active')?>"><a href="<?=$uri->anchor($project['slug'],'newticket')?>"><?=l('new_ticket')?></a></li>
					<? } ?>
				</ul>
				<? } else { ?>
				<ul>
					<li class="first<?=iif($uri->seg[0]=='',' active')?>"><a href="#"><?=l('projects')?></a></li>
				</ul>
				<? } ?>
			</div>
		</div>
		<div id="page">
