<nav id="admin_nav" class="tabs">
	<ul>
		<li<?php echo iif(active_nav('/admin(?:/projects(.*))?'), ' class="active"')?>><?php echo HTML::link(l('projects'), "/admin"); ?></li>
		<li<?php echo iif(active_nav('/admin/users(.*)'), ' class="active"')?>><?php echo HTML::link(l('users'), "/admin/users"); ?></li>
		<li<?php echo iif(active_nav('/admin/groups(.*)'), ' class="active"')?>><?php echo HTML::link(l('groups'), "/admin/groups"); ?></li>
		<li<?php echo iif(active_nav('/admin/plugins(.*)'), ' class="active"')?>><?php echo HTML::link(l('plugins'), "/admin/plugins"); ?></li>
	</ul>
</nav>