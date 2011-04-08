<h2><?php _l('roadmap')?></h2>

<ul class="list milestones_list">
	<?php foreach($milestones as $milestone) { ?>
	<li>
		<h3><?php echo HTML::link(baseurl($traq->project['slug'],'milestones',$milestone['slug']), $milestone['milestone'])?></h3>
		<?php View::render('projects/_milestone_progress', array('milestone'=>$milestone))?>
		<div class="info"><?php echo formattext($milestone['info'])?></div>
		
		<?php if(count($milestone['tickets'])) { ?>
		<div class="related-tickets">
			<h4><a href="<?php echo baseurl($traq->project['slug'],'tickets')?>?milestone=<?php echo $milestone['slug']?>" onclick="$('#related_tickets_<?php echo $milestone['id']?>').slideToggle('slow'); return false;"><?php _l('related_tickets')?></a></h4>
			<div id="related_tickets_<?php echo $milestone['id']?>" style="display: none;" class="list">
				<?php foreach($milestone['tickets'] as $ticket) { ?>
				<div>
					<a href="<?php echo baseurl($traq->project['slug'],'tickets',$ticket['ticket_id'])?>"<?php echo ($ticket['closed'] ? ' class="closed"' :'') ?>><?php echo ticket_type($ticket['type'])?> #<?php echo $ticket['ticket_id']?></a>:
					<?php echo $ticket['summary']?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</li>
	<?php } ?>
</ul>