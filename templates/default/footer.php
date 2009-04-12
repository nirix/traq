	<br />
	<div id="footer">
		<?=l('copyright')?>
	</div>
</div>
<? ($hook = FishHook::hook('template_footer')) ? eval($hook) : false; ?>