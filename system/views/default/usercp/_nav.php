<nav id="usercp_nav" class="tabs">
	<ul>
		<li<?php echo iif(active_nav('/usercp'), ' class="active"')?>><?php echo HTML::link(l('information'), "/usercp"); ?></li>
		<li<?php echo iif(active_nav('/usercp/password'), ' class="active"')?>><?php echo HTML::link(l('password'), "/usercp/password"); ?></li>
	</ul>
</nav>