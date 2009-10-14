<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

/**
 * Authenticate
 * Used to make sure the user is an admin.
 */
function authenticate()
{

}

/**
 * Check Active
 * @param string $page The page filename
 * @param array $query An array of query strings the page can have, blank for any. (Opional)
 */
function activepage($page,$query=NULL)
{
	// check if $query is empty and set it to _SERVER[QUERY_STRING]
	if($query==NULL)
	{
		$query = $_SERVER['QUERY_STRING'];
	}
	// check if $query is an array or not, if not make it an array.
	if(!is_array($query))
	{
		$query = array($query);
	}
	return iif(THISPAGE == $page && in_array($_SERVER['QUERY_STRING'],$query),1,0);
}

/**
 * AdminCP Header
 */
function head($title='')
{
	global $sidebar_links;
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Traq AdminCP</title>
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<div id="head">
				<h1><a href="./">Traq AdminCP</a></h1>
			</div>
			<div class="pagetitle"><?=$title?></div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="page">
				<tr>
					<td id="sidebar" valign="top">
						<ul id="sb">
						<? foreach($sidebar_links as $item) { ?>
							<? if(is_array($item['links'])) { ?>
							<li class="category">
								<div><?=$item['title']?></div>
								<ul class="sblinks">
									<? foreach($item['links'] as $link) { ?>
									<li<?=($link['active'] ? ' class="active"' : '')?>><a href="<?=$link['url']?>"><?=$link['title']?></a></li>
									<? } ?>
								</ul>
							</li>
							<? } else { ?>
							<li class="normal<?=($item['active'] ? ' active' : '')?>">
								<a href="<?=$item['url']?>"><?=$item['title']?></a>
							</li>
							<? } ?>
						<? } ?>
						</ul>
					</td>
					<td id="content" valign="top">
	<?
}

/**
 * AdminCP Footer
 */
function foot()
{
	?>
					</td>
				</tr>
			</table>
			<div id="foot">
				<?=l('poweredby')?>
			</div>
		</div>
	</body>
</html>
	<?
}
?>