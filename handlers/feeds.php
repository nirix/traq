<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Breadcrumbs
$breadcrumbs[$uri->anchor($project['slug'],'feeds')] = "Feeds";

if(!isset($uri->seg[2])) {
	// Feed list...
	include(template('feedlist'));
} elseif($uri->seg[2] == "timeline") {
	$updated = 0;
	// Fetch the timeline rows...
	$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."' ORDER BY timestamp DESC"); // Fetch timeline rows
	while($rowinfo = $db->fetcharray($fetchrows)) {
		$parts = explode(':',$rowinfo['data']); // Explode the timeline data field
		$rowinfo['type'] = $parts[0]; // Set the row type
		$rowinfo['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$rowinfo['userid']."' LIMIT 1")); // Get the user info
		// Check the type, and set the info for that specified type
		if($parts[0] == "TICKETCREATE" or $parts[0] == "TICKETCLOSE" or $parts[0] == "TICKETREOPEN") {
			// Ticket Open, Close and Reopen
			$rowinfo['ticket'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$parts[1]."' LIMIT 1"));
			$rowinfo['ticket']['summary'] = stripslashes($rowinfo['ticket']['summary']);
		}
		if($updated < $rowinfo['timestamp']) {
			$updated = $rowinfo['timestamp'];
		}
		FishHook::hook('projecthandler_timeline_fetchrows_feed');
		$rows[] = $rowinfo;
	}	
	
	header('Content-type: application/rss+xml');
	echo '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
?><rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	>

	<channel>
		<title><?=$project['name']?> Timeline RSS Feed</title>
		<atom:link href="http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'feeds','timeline','rss2')?>" rel="self" type="application/rss+xml" />
		<link>http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'])?></link>
		<pubDate><?=date("D, d M Y H:i:s O",$updated)?></pubDate>
		<generator>http://traqproject.org</generator>
		<language>en</language>
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>1</sy:updateFrequency>
		<? foreach($rows as $row) { ?> 
		<item>
			<? if($row['type'] == "TICKETCREATE") { ?>
			<title>Ticket #<?=$row['ticket']['tid']?> (<?=$row['ticket']['summary']?>) (<?=tickettype($row['ticket']['type'])?>) created</title>
			<? } elseif($row['type'] == "TICKETCLOSE") { ?>
			<title>Ticket #<?=$row['ticket']['tid']?> (<?=$row['ticket']['summary']?>) (<?=tickettype($row['ticket']['type'])?>) closed</title>
			<? } elseif($row['type'] == "TICKETREOPEN") { ?>
			<title>Ticket #<?=$row['ticket']['tid']?> (<?=$row['ticket']['summary']?>) (<?=tickettype($row['ticket']['type'])?>) reopened</title>
			<? } ?>
			<link>http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?></link>
			<comments>http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?>#history</comments>
			<pubDate><?=date("D, d M Y H:i:s O",$row['timestamp'])?></pubDate>
			<dc:creator><?=$row['username']?></dc:creator>
			
			<guid isPermaLink="true">http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?></guid>
			
			<description><![CDATA[<?=substr(stripslashes($row['ticket']['body']),0,255)?><?=(strlen(stripslashes($row['ticket']['body']))>255 ? '...' : '')?>]]></description>
			<content:encoded><![CDATA[<?=stripslashes($row['ticket']['body'])?>]]></content:encoded>
		</item>
		<? } ?> 
	</channel>
</rss>
<?
}
?>