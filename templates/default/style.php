<link rel="stylesheet" href="<?=$uri->anchor('templates',$settings->theme)?>style.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?=$project['name']?> Timeline RSS Feed" href="http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'feeds','timeline','rss2')?>" />
