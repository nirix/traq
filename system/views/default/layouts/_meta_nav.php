<div id="meta_nav">
	<ul id="user_nav">
	<?php if (LOGGEDIN) { ?>
		<li<?php echo iif(active_nav('/usercp'), ' class="active"')?>><?php echo HTML::link(l('usercp'), '/usercp'); ?></li>
		<li><?php echo HTML::link(l('logout'), '/logout'); ?></li>
		<?php if ($current_user->group->is_admin) { ?>
		<li<?php echo iif(active_nav('/admin(.*)'), ' class="active"')?>><?php echo HTML::link(l('admincp'), '/admin'); ?></li>
		<?php } ?>
	<?php } else { ?>
		<li<?php echo iif(active_nav('/login'), ' class="active"')?>><?php echo HTML::link(l('login'), '/login'); ?></li>
		<li<?php echo iif(active_nav('/register'), ' class="active"')?>><?php echo HTML::link(l('register'), '/register'); ?></li>
	<?php } ?>
	</ul>
</div>