<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(l('roadmap'),$project['name']))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=l('x_roadmap',$project['name'])?></h1>
		<ul class="milestones">
<? foreach($milestones as $milestone) { ?>
			<li class="milestone">
				<div class="info">
					<h2><a href="<?=$uri->anchor($project['slug'],'milestone',$milestone['milestone'])?>"><?=l('milestone')?>: <em><?=$milestone['milestone']?><?=(!empty($milestone['codename']) ? ' <small>"'.$milestone['codename'].'"</small>' : '')?></em></a></h2>
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
							<td class="closed" style="width: <?=$milestone['tickets']['percent']['closed']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'closed')?>" title="<?=l('x_of_x_tickets_closed',$milestone['tickets']['closed'],$milestone['tickets']['total'])?>"></a></td>
							<td class="open" style="width: <?=$milestone['tickets']['percent']['open']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'open')?>" title="<?=l('x_of_x_tickets_active',$milestone['tickets']['open'],$milestone['tickets']['total'])?>"></a></td>
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
			</li>
<? } ?> 
		</ul>
	</div>
<? include(template('footer')); ?>
</body>
</html>