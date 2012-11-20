<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
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

	if(!$user->group['is_admin']) {
		header("Location: login.php");
		exit;
	}
}

/**
 * Get Projects
 * Used to get an array of the projects.
 * @return array
 */
function getprojects()
{
	global $db;

	$projects = array();
	$fetch = $db->query("SELECT id,name FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetch))
		$projects[] = $info;

	return $projects;
}

/**
 * Get User Groups
 * Returns an array of the user groups.
 * @return array
 */
function getgroups()
{
	global $db;

	$groups = array();
	$fetch = $db->query("SELECT id,name FROM ".DBPF."usergroups ORDER BY name ASC");
	while($info = $db->fetcharray($fetch))
		$groups[] = $info;

	return $groups;
}

function get_locales()
{
	$locales = array();
	foreach(scandir(TRAQPATH.'system/locale') as $file)
	{
		if(!in_array($file,array('.','..','.svn')))
		{
			$data = file_get_contents(TRAQPATH.'system/locale/'.$file);
			preg_match('| Name: (.*)$|mi',$data,$name); // Language name
			preg_match('| Author:(.*)$|mi',$data, $author); // Language author
			$locales[] = array(
				'name' => trim($name[1]),
				'author' => $author[1],
				'file' => $file
			);
		}
	}
	return $locales;
}

/**
 * Check Active
 * @param string $page The page filename
 * @param array $query An array of query strings the page can have. (Opional)
 *
 * This may be changed someday to just check a complete page and query string in one,
 * for example: activepage('page.php?this=that') instead of activepage('page.php','this=that')
 */
function activepage($pages,$query=NULL)
{
	// check if $pages is an array or not.
	if(!is_array($pages)) $pages = array($pages);

	// check if $query is an array or not, if not make it an array.
	if(!is_array($query)) $query = array_slice(func_get_args(),1);

	// check if $query is empty and set it to _SERVER[QUERY_STRING]
	if(!count($query)) $query = array($_SERVER['QUERY_STRING']);

	return (in_array(THISPAGE,$pages) && in_array($_SERVER['QUERY_STRING'],$query) ? true : false);
}

/**
 * Check for update
 */
function check4update()
{
	global $traq_version_code;
	libxml_use_internal_errors(true);
	if($contents = @file_get_contents("http://traq.io/version_check.php?version=".urlencode(TRAQVER)."&versioncode=".$traq_version_code)
	and $xml = simplexml_load_string($contents))
	{
		if($xml->version and $xml->version['code'] > $traq_version_code)
		{
			return $xml;
		}
		return false;
	}
	return false;
}

/**
 * AdminCP Header
 * Used to print the AdminCP header.
 * @param string $title Page title.
 * @param bool $sidebar Show the sidebar or not.
 * @param mixed $links The links for the sidebar.
 */
function head($title='',$sidebar=false,$links=array())
{
	global $sidebar_links;
	if(!is_array($links)) $links = $sidebar_links[$links]['links'];
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo l('traq_admincp')?><?php echo iif($title != '',' / '.$title)?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<link rel="stylesheet" href="../js/likeaboss/css/editor.css" type="text/css" />
		<script src="../js/jquery.min.js" type="text/javascript"></script>
		<script src="../js/likeaboss/likeaboss.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				// Add the editor interface to all text areas, like a boss.
				$('textarea').likeaboss();
			});
		</script>
		<?php ($hook = FishHook::hook('admin_headerinc')) ? eval($hook) : false; ?>
	</head>
	<body>
		<div id="head">
			<span><a href="./"><?php echo l('traq_admincp')?></a></span>
			<div id="nav<?php echo (defined("HIDENAV") ? '_small' : '')?>">
			<?php if(!defined("HIDENAV")) { ?>
				<div id="meta_nav">
					<ul>
						<li class="first"><a href="../"><?php echo l('view_site')?></a></li>
					</ul>
				</div>
				<ul class="main_nav">
					<?php foreach($sidebar_links as $link) { ?>
					<li class="<?php echo isset($link['class']) ? $link['class'] : '' . iif($link['active'],' active')?>"><a href="<?php echo $link['url']?>"><?php echo $link['title']?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</div>
		</div>
		<div id="page">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<?php if($sidebar) { ?>
					<td valign="top" id="sidebar">
						<ul>
						<?php foreach($links as $link) { ?>
							<?php if(isset($link['divider']) && $link['divider']) { ?>
							<li class="divider"></li>
							<?php } else { ?>
							<li class="<?php echo isset($link['class']) ? $link['class'] : '' . iif($link['active'],' active')?>"><a href="<?php echo $link['url']?>"><?php echo $link['title']?></a></li>
							<?php } ?>
						<?php } ?>
						</ul>
					</td>
					<?php } ?>
					<td valign="top">
	<?php
}

/**
 * AdminCP Footer
 * Used to print the AdminCP footer.
 */
function foot()
{
	?>
					</td>
				</tr>
			</table>
		</div>
		<div id="foot">
			<?php ($hook = FishHook::hook('admin_foot')) ? eval($hook) : false; ?>
			<span id="powered_by">
				<?php echo l('poweredby')?>
			</span>
		</div>
	</body>
</html><?php
}
?>