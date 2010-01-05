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
	global $user;
	
	if(!$user->group['is_admin'])
		header("Location: login.php");
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
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body id="project_info">
		<div id="head">
			<span><a href="./">Traq AdminCP</a></span>
			<div id="nav">
				<div id="meta_nav">
					<ul>
						<li class="first"><a href="/traq/user/settings">View Site</a></li>
					</ul>
				</div>
				<ul class="main_nav">
					<? foreach($sidebar_links as $link) { ?>
					<li class="<?=$link['class'].iif($link['active'],' active')?>"><a href="<?=$link['url']?>"><?=$link['title']?></a></li>
					<? } ?>
				</ul>
			</div>
		</div>
		<div id="page">
	<?
}

/**
 * AdminCP Footer
 */
function foot()
{
	?>
</div>
		<div id="foot">
			<span id="powered_by">
				<?=l('poweredby')?>
			</span>
		</div>
	</body>
</html>
	<?
}
?>