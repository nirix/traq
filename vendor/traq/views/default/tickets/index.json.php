<?php echo to_json([
  'page' => (int) ($pagination->total_pages > 0 ? $pagination->page : 1),
  'total_pages' => (int) $pagination->total_pages,
  'tickets' => $tickets,
]);