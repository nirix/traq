<div id="wrapper">
	<div id="header">
		<span id="head"><a href="<?=$uri->anchor()?>"><?=$settings->title?></a></span>
		<div id="metanav" class="nav">
			<ul>
<? if($user->loggedin) { ?>
					<li class="first"><a href="<?=$uri->anchor(array('user','settings'))?>"><?=l('usercp')?></a></li>
					<? if($user->group->isadmin) { ?>
					<li><a href="<?=$uri->rootpath()?>admincp"><?=l('admincp')?></a></li>
					<? } ?>
					<li class="last"><a href="<?=$uri->anchor(array('user','logout'))?>"><?=l('logout_x',$user->info->username)?></a></li>
<? } else { ?>
					<li class="first"><a href="<?=$uri->anchor(array('user','login'))?>"><?=l('login')?></a></li>
					<li class="last"><a href="<?=$uri->anchor(array('user','register'))?>"><?=l('register')?></a></li>
<? } ?>
			</ul>
		</div>
	</div>
