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
 *
 */
function getprojects()
{
	global $db;
	
	$projects = array();
	$fetch = $db->query("SELECT id,name FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetch))
	{
		$projects[] = $info;
	}
	
	return $projects;
}

/**
 * Check Active
 * @param string $page The page filename
 * @param array $query An array of query strings the page can have. (Opional)
 */
function activepage($pages,$query=NULL)
{
	// check if $pages is an array or not.
	if(!is_array($pages)) $pages = array($pages);
	
	// check if $query is an array or not, if not make it an array.
	if(!is_array($query)) $query = array_slice(func_get_args(),1);

	// check if $query is empty and set it to _SERVER[QUERY_STRING]
	if(!count($query)) $query = array($_SERVER['QUERY_STRING']);
	
	return iif(in_array(THISPAGE,$pages) && in_array($_SERVER['QUERY_STRING'],$query),1,0);
}

/**
 * AdminCP Header
 */
function head($title='',$sidebar=false,$links=array())
{
	global $sidebar_links;
	if(!is_array($links)) $links = $sidebar_links[$links]['links'];
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=l('traq_admincp')?><?=iif($title != '',' / '.$title)?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body id="project_info">
		<div id="head">
			<span><a href="./"><?=l('traq_admincp')?></a></span>
			<div id="nav">
				<div id="meta_nav">
					<ul>
						<li class="first"><a href="../"><?=l('view_site')?></a></li>
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
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<? if($sidebar) { ?>
					<td valign="top" id="sidebar">
						<ul>
						<? foreach($links as $link) { ?>
							<? if($link['divider']) { ?>
							<li class="divider"></li>
							<? } else { ?>
							<li class="<?=$link['class'].iif($link['active'],' active')?>"><a href="<?=$link['url']?>"><?=$link['title']?></a></li>
							<? } ?>
						<? } ?>
						</ul>
					</td>
					<? } ?>
					<td valign="top">
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