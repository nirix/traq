<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('roadmap')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo l('x_roadmap',$project['name'])?></h1>
		
		<ul class="milestones">
		<?php foreach($milestones as $milestone) { ?>
			<li class="milestone">
				<div class="info">
					<h2><a href="<?php echo $uri->anchor($project['slug'],'milestone-'.$milestone['slug'])?>"><?php echo l('milestone')?>: <em><?php echo $milestone['milestone']?><?php echo (!empty($milestone['codename']) ? ' <small>"'.$milestone['codename'].'"</small>' : '')?></em></a></h2>
					<p class="date">
						<?php if($milestone['due'] == 0 && $milestone['completed'] == 0) { ?>
						
						<?php } elseif($milestone['completed'] > 0) { ?>
						<?php echo l('completed_on_x',date("d/m/Y",$project['completed']))?>
						<?php } elseif($milestone['due'] <= time()) { ?>
						<?php echo l('due_x_ago',timesince($milestone['due'],true))?>
						<?php } elseif($milestone['due'] > time()) { ?>
						<?php echo l('due_x_from_now',timefrom($milestone['due'],true))?>
						<?php } ?>
					</p>
					<table>
						<tr>
							<td>
								<table class="progress" cellspacing="0">
									<tr>
										<?php if($milestone['tickets']['percent']['closed'] != 0) { ?><td class="closed" width="<?php echo $milestone['tickets']['percent']['closed']?>%"><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>?milestone=<?php echo $milestone['milestone']?>&status=closed"></a></td><?php } ?>
										<?php if($milestone['tickets']['percent']['open'] != 0) { ?><td class="open" width="<?php echo $milestone['tickets']['percent']['open']?>%"><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>?milestone=<?php echo $milestone['milestone']?>&status=open"></a></td><?php } ?>
									</tr>
								</table>
							</td>
							<td class="percent"><?php echo $milestone['tickets']['percent']['closed']?>%</td>
						</tr>
					</table>
					<dl>
						<dt><?php echo l('closed_tickets')?>:</dt>
						<dd><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>?milestone=<?php echo $milestone['milestone']?>&status=closed"><?php echo $milestone['tickets']['closed']?></a></dd>
						<dt><?php echo l('active_tickets')?>:</dt>
						<dd><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>?milestone=<?php echo $milestone['milestone']?>&status=open"><?php echo $milestone['tickets']['open']?></a></dd>
						<dt><?php echo l('total_tickets')?>:</dt>
						<dd><a href="<?php echo $uri->anchor($project['slug'],'tickets')?>?milestone=<?php echo $milestone['milestone']?>"><?php echo $milestone['tickets']['total']?></a></dd>
					</dl>
					<?php echo formattext($milestone['info'])?>
				</div>
			</li>
		<?php } ?>
		</ul>
		
		<?php require(template('footer')); ?>
	</body>
</html>