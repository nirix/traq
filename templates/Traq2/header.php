<div id="head">
			<span><a href="<?=$uri->anchor()?>"><?=settings('title')?></a> <? if(is_project(PROJECT_SLUG)) { ?>/ <?=$project['name']?><? } ?></span>
			<div id="nav">
				<div id="meta_nav">
					<ul>
					<? if($user->loggedin) { ?>
						<li class="first<?=iif($uri->seg[1] == 'settings',' active')?>"><a href="<?=$uri->anchor('user','settings')?>"><?=l('settings')?></a></li>
						<li><a href="<?=$uri->anchor('user','logout')?>"><?=l('logout')?></a></li>
						<? if($user->group['is_admin']) { ?>
						<li><a href="<?=str_replace('index.php/','',$uri->anchor('admincp'))?>"><?=l('admincp')?></a></li>
						<? } ?>
					<? } else { ?>
						<li class="first<?=iif($uri->seg[1] == 'login',' active')?>"><a href="<?=$uri->anchor('user','login')?>"><?=l('login')?></a></li>
						<li<?=iif($uri->seg[1] == 'register',' class="active"')?>><a href="<?=$uri->anchor('user','register')?>"><?=l('register')?></a></li>
					<? } ?>
					</ul>
				</div>
				<? if(is_project(PROJECT_SLUG)) { ?>
				<ul class="main_nav">
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
				<ul class="main_nav">
					<li class="first<?=iif($uri->seg[0]=='',' active')?>"><a href="<?=$uri->anchor()?>"><?=l('projects')?></a></li>
				</ul>
				<? } ?>
			</div>
		</div>
		<div id="page">
