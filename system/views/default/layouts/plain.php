<?php
if ($content_type = mime_type_for(Router::$extension))
{
	header("Content-Type: {$content_type}");
}
echo $output;
?>