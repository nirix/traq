<title><?php echo implode(' - ', array_reverse($traq->title)); ?></title>
		<meta charset="UTF-8" />
		<?php echo HTML::css_link(Request::base() . 'css.php?css=screen&amp;theme=' . settings('theme')); ?>
		<?php echo HTML::css_link(Request::base() . 'css.php?css=print', 'print'); ?>
		<!--[if lt IE 8]>
		<?php echo HTML::css_link(Request::base() . 'css.php?css=ie'); ?>
		<![endif]-->
		<?php echo HTML::js_inc('//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'); ?>
		<?php echo HTML::js_inc('//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'); ?>
		<?php echo HTML::js_inc(Request::base() . 'js.php?js=all'); ?>
		<?php echo HTML::js_inc(Request::base('_js')); ?>
		<?php FishHook::run('template:layouts/global/head'); ?>
