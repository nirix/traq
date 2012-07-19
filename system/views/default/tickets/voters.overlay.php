<h3><?php echo l('people_who_have_voted_on_this_ticket', count($voters)); ?></h3>
<ol id="voters_list" class="clearfix">
<?php foreach ($voters as $key => $voter) { ?>
	<li><?php echo HTML::link($voter->username, $voter->href()) . iif($key != count($voters)-1, ', '); ?></li>
<?php } ?>
</ol>