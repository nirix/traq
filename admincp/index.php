<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Set the full path to the Traq folder
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

include("global.php");

authenticate();

head('Summary');
?>
<div class="tborder">
	<div class="thead">Statistics</div>
	<table width="100%" cellspacing="0">
		<tr class="<?=altbg()?>">
			<td width="150">Projects</td>
			<td></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150">Tickets</td>
			<td></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150">Users</td>
			<td></td>
		</tr>
	</table>
</div>
<?
foot();
?>