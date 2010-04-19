
		<div class="clear"></div>
		</div>
		<div id="foot">
			<?php ($hook = FishHook::hook('template_footer')) ? eval($hook) : false; ?>
			<span id="powered_by">
				<?php echo l('poweredby')?>
			</span>
		</div>