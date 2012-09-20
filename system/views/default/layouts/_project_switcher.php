<span id="project_switcher_btn">
	<a href="#" class="arrow"><em></em></a>
</span>
<div id="popover" class="project_switcher">
	<ul>
	<?php foreach ($projects as $p) { ?>
		<li><?php echo HTML::link($p->name, $p->href()); ?>
	<?php } ?>
	</ul>
</div>