<h2><?php _l('Roadmap')?></h2>

<ul class="list milestones_list">
	<?php foreach($milestones as $milestone) { ?>
	<li>
		<h3><?php echo HTML::link(baseurl($traq->project['slug'],'milestones',$milestone['slug']), $milestone['milestone'])?></h3>
		<?php View::render('projects/_milestone_progress', array('milestone'=>$milestone))?>
		<div class="info"><?php echo formattext($milestone['info'])?></div>
		
		<?php if(count($milestone['tickets'])) { ?>
		<div class="related-tickets">
			<h4><a href="#" onclick="$('#related_tickets_<?php $milestone['id']?>').slideToggle('slow'); return false;"><?php _l('Related Tickets')?></a></h4>
			<div id="related_tickets_<?php $milestone['id']?>" style="display: none;" class="list">
				<?php foreach($milestone['tickets'] as $ticket) { ?>
				<div>
					<?php _l('Ticket: #x', $ticket['ticket_id'])?>:
					<?php echo HTML::link(baseurl($traq->project['slug'],'tickets',$ticket['ticket_id']), $ticket['summary'])?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</li>
	<?php } ?>
</ul>