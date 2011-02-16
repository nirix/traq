<div id="wrapper">
	<div id="head">
			<span><a href="<?php echo $uri->anchor()?>"><?php echo settings('title')?></a> <?php if(isset($project) && is_project($project['slug'])) { ?>/ <?php echo $project['name']?><?php } ?></span>
			<div id="nav">
				<div id="meta_nav">
					<ul>
					<?php if($user->loggedin) { ?>
						<li class="first<?php echo iif($uri->seg(1) == 'usercp',' active')?>"><a href="<?php echo $uri->anchor('user','usercp')?>"><?php echo l('usercp')?></a></li>
						<li><a href="<?php echo $uri->anchor('user','logout')?>"><?php echo l('logout')?> (<?php echo $user->info['username']?>)</a></li>
						<?php if($user->group['is_admin']) { ?>
						<li><a href="<?php echo baseurl(); ?>admincp"><?php echo l('admincp')?></a></li>
						<?php } ?>
					<?php } else { ?>
						<li class="first<?php echo iif($uri->seg(1) == 'login',' active')?>"><a href="<?php echo $uri->anchor('user','login')?>"><?php echo l('login')?></a></li>
						<?php if(settings('allow_registration')) { ?>
						<li<?php echo iif($uri->seg(1) == 'register',' class="active"')?>><a href="<?php echo $uri->anchor('user','register')?>"><?php echo l('register')?></a></li>
						<?php } ?>
					<?php } ?>
					</ul>
				</div>
				<?php if(isset($project) && is_project($project['slug'])) { ?>
				<ul class="main_nav">
					<li class="first<?php echo iif(!$uri->seg(1),' active')?>"><a href="<?php echo $uri->anchor($project['slug'])?>"><?php echo l('project_info')?></a></li>
					<li<?php echo iif($uri->seg(1) =='roadmap' or preg_match('/milestone-(?P<slug>.*)/',$uri->seg(1)),' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'roadmap')?>"><?php echo l('roadmap')?></a></li>
					<li<?php echo iif($uri->seg(1) =='timeline',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'timeline')?>"><?php echo l('timeline')?></a></li>
					<li<?php echo iif($uri->seg(1) =='tickets' or preg_match('/ticket-(?P<ticket_id>.*)/',$uri->seg(1)),' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>"><?php echo l('tickets')?></a></li>
					<li<?php echo iif($uri->seg(1) =='changelog',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'changelog')?>"><?php echo l('changelog')?></a></li>
					<li<?php echo iif($uri->seg(1) =='wiki',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'wiki')?>"><?php echo l('Wiki')?></a></li>
					<?php if(has_repo()) { ?><li<?php echo iif($uri->seg(1)=='source',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'source')?>"><?php echo l('source')?></a></li><?php } ?>
					<?php ($hook = FishHook::hook('template_header_project_links')) ? eval($hook) : false; ?>
					<?php if($user->group['create_tickets']) { ?>
					<li class="standalone<?php echo iif($uri->seg(1)=='newticket',' active')?>"><a href="<?php echo $uri->anchor($project['slug'],'newticket')?>"><?php echo l('new_ticket')?></a></li>
					<?php } ?>
				</ul>
				<?php } else { ?>
				<ul class="main_nav">
					<li class="first<?php echo iif($uri->seg(0)=='',' active')?>"><a href="<?php echo $uri->anchor()?>"><?php echo l('projects')?></a></li>
				</ul>
				<?php } ?>
			</div>
		</div>
		<div id="page">
