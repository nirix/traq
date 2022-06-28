<?php echo to_json([
  'page' => $pagination->page,
  'total_pages' => $pagination->total_pages,
  'tickets' => $tickets,
]); ?>