<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('source')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<?php if(count(project_repos()) > 1) { ?>
		<div id="repository_select">
			<?php echo l('select_repository')?> <select>
			<?php foreach(project_repos() as $rep) { ?>
				<option onclick="javascript:window.location='<?php echo $uri->anchor($project['slug'],'source',$rep['slug'])?>';"<?php echo iif($rep['slug'] == $uri->seg[2],' selected="selected"')?>><?php echo $rep['name']?></option>
			<?php } ?>
			</select>
		</div>
		<?php } ?>
		
		<?php require(template('source/'.$repo['info']['template'].'_'.$info['kind'])); ?>
		
		<?php require(template('footer')); ?>
	</body>
</html>