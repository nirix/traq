<?php
if (!function_exists('_mklocale')) {
	function _mklocale($change, $locale_property = 'x')
	{
		if (!empty($change['to']) and !empty($change['from'])) {
			$string = "{$locale_property}_from_x_to_x";
		} elseif (!empty($change['to']) and empty($change['from'])) {
			$string = "{$locale_property}_from_null_to_x";
		} elseif (empty($change['to']) and !empty($change['from'])) {
			$string = "{$locale_property}_from_x_to_null";
		}
		return l(
			"ticket_history.{$string}",
			'<span class="ticket_history_property">' . l($change['property']) . '</span>',
			'<span class="ticket_history_from">' . $change['from'] . '</span>',
			'<span class="ticket_history_to">' . $change['to'] . '</span>'
		);
	}
}

if (isset($change['action'])) {
	echo l("ticket_history.{$change['action']}", $change['from'], $change['to']);
} elseif ($change['property'] == 'assigned_to') {
	echo _mklocale($change, 'assignee');
} else {
	echo _mklocale($change);
}