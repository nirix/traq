<?php
if (!function_exists('_mklocale')) {
	function _mklocale($change, $locale_property = 'x')
	{
		if (!empty($change['to']) and !empty($change['from'])) {
			return l("ticket_history.{$locale_property}_from_x_to_x", l($change['property']), $change['from'], $change['to']);
		} elseif (!empty($change['to']) and empty($change['from'])) {
			return l("ticket_history.{$locale_property}_from_null_to_x", l($change['property']), $change['from'], $change['to']);
		} elseif (empty($change['to']) and !empty($change['from'])) {
			return l("ticket_history.{$locale_property}_from_x_to_null", l($change['property']), $change['from'], $change['to']);
		}
	}
}

if (isset($change['action'])) {
	echo l("ticket_history.{$change['action']}", $change['from'], $change['to']);
} elseif ($change['property'] == 'assigned_to') {
	echo _mklocale($change, 'assignee');
} else {
	echo _mklocale($change);
}