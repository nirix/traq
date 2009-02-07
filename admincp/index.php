<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

require("global.php");

if(!$user->group->isadmin) {
	exit;
}

adminheader();
?>
<div id="content">
	<div class="content-group">
			<div class="content-title">Summary</div>
			woot
		</div>
</div>
<?
adminfooter();
?>