<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(l('milestone_x',$milestone['milestone']),$project['name']))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=l('x_milestone_x',$project['name'],$milestone['milestone'])?></h1>
		<div class="milestone">
			<div class="info">
				<p class="date">
					<? if($milestone['due'] == 0 && $milestone['completed'] == 0) { ?>
					<?=l('no_due_date_set')?>
					<? } elseif($milestone['completed'] > 0) { ?>
					<?=l('completed_on_x',date("d/m/Y"))?>
					<? } elseif($milestone['due'] <= time()) { ?>
					Due <?=timesince($milestone['due'])?> ago
					<? } elseif($milestone['due'] > time()) { ?>
					Due <?=timefrom($milestone['due'])?> from now
					<? } ?>
				</p>
				<table class="progress">
					<tr>
						<td class="closed" style="width: <?=$milestone['tickets']['percent']['closed']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'closed')?>" title="<?=$milestone['tickets']['closed']?> of <?=$milestone['tickets']['total']?> tickets closed"></a></td>
						<td class="open" style="width: <?=$milestone['tickets']['percent']['open']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'open')?>" title="<?=$milestone['tickets']['open']?> of <?=$milestone['tickets']['total']?> tickets active"></a></td>
					</tr>
				</table>
				<p class="percent"><?=$milestone['tickets']['percent']['closed']?>%</p>
				<dl>
					<dt><?=l('closed_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'closed')?>"><?=$milestone['tickets']['closed']?></a></dd>
					<dt><?=l('active_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'open')?>"><?=$milestone['tickets']['open']?></a></dd>
					<dt><?=l('total_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'])?>"><?=$milestone['tickets']['total']?></a></dd>
				</dl>
				<div class="description">
					<?=$milestone['desc']?> 
				</div>
			</div>
		</div>
	</div>
<? include(template('footer')); ?>
</body>
</html>