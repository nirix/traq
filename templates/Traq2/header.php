<div id="head">
			<span><a href="<?php echo $uri->anchor()?>"><?php echo settings('title')?></a> <?php if(is_project($project['slug'])) { ?>/ <?php echo $project['name']?><?php } ?></span>
			<div id="nav">
				<div id="meta_nav">
					<ul>
					<?php if($user->loggedin) { ?>
						<li class="first<?php echo iif($uri->seg[1] == 'usercp',' active')?>"><a href="<?php echo $uri->anchor('user','usercp')?>"><?php echo l('usercp')?></a></li>
						<li><a href="<?php echo $uri->anchor('user','logout')?>"><?php echo l('logout')?></a></li>
						<?php if($user->group['is_admin']) { ?>
						<li><a href="<?php echo str_replace('index.php/','',$uri->anchor('admincp'))?>"><?php echo l('admincp')?></a></li>
						<?php } ?>
					<?php } else { ?>
						<li class="first<?php echo iif($uri->seg[1] == 'login',' active')?>"><a href="<?php echo $uri->anchor('user','login')?>"><?php echo l('login')?></a></li>
						<?php if(settings('allow_registration')) { ?>
						<li<?php echo iif($uri->seg[1] == 'register',' class="active"')?>><a href="<?php echo $uri->anchor('user','register')?>"><?php echo l('register')?></a></li>
						<?php } ?>
					<?php } ?>
					</ul>
				</div>
				<?php if(is_project($uri->seg[0])) { ?>
				<ul class="main_nav">
					<li class="first<?php echo iif(empty($uri->seg[1]),' active')?>"><a href="<?php echo $uri->anchor($project['slug'])?>"><?php echo l('project_info')?></a></li>
					<li<?php echo iif($uri->seg[1]=='roadmap' or preg_match('/milestone-(?<slug>.*)/',$uri->seg[1]),' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'roadmap')?>"><?php echo l('roadmap')?></a></li>
					<li<?php echo iif($uri->seg[1]=='timeline',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'timeline')?>"><?php echo l('timeline')?></a></li>
					<li<?php echo iif($uri->seg[1]=='tickets' or preg_match('/ticket-(?<ticket_id>.*)/',$uri->seg[1]),' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>"><?php echo l('tickets')?></a></li>
					<li<?php echo iif($uri->seg[1]=='changelog',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'changelog')?>"><?php echo l('changelog')?></a></li>
					<?php if(has_repo()) { ?><li<?php echo iif($uri->seg[1]=='source',' class="active"')?>><a href="<?php echo $uri->anchor($project['slug'],'source')?>"><?php echo l('source')?></a></li><?php } ?>
					
					<?php if($user->group['create_tickets']) { ?>
					<li class="standalone<?php echo iif($uri->seg[1]=='newticket',' active')?>"><a href="<?php echo $uri->anchor($project['slug'],'newticket')?>"><?php echo l('new_ticket')?></a></li>
					<?php } ?>
				</ul>
				<?php } else { ?>
				<ul class="main_nav">
					<li class="first<?php echo iif($uri->seg[0]=='',' active')?>"><a href="<?php echo $uri->anchor()?>"><?php echo l('projects')?></a></li>
				</ul>
				<?php } ?>
			</div>
		</div>
		<div id="page">
