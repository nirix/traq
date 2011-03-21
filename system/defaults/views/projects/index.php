<h2><?php _l('Projects')?></h2>

<ul>
	<?php foreach($projects as $project) { ?>
	<li>
		<h3><?php echo $project['name']?></h3>
		
		<?php echo $project['info']?>
	</li>
	<?php }?>
</ul>