<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Browse Source',$project['name']))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1>Browse Source</h1>
		
		<table class="listing browsesvn_files">
			<thead>
				<tr>
					<th class="file">File</th>
					<th class="size">Size</th>
					<th class="rev">Rev</th>
					<th class="author">Author</th>
				</tr>
			</thead>
			<tbody>
<? foreach($list as $file) {
	if($bgclass == "even") {
		$bgclass = "odd";
	} else {
		$bgclass = "even";
	}
?>
				<tr class="<?=$bgclass?>">
					<td>
						<? if($file['kind'] == 'dir') { ?>
						<a href="<?=$uri->anchor($project['slug'],'source').$file['path']?>"><?=$file['name']?>/</a>
						<? } else { ?>
						<?=$file['name']?>
						<? } ?>
					</td>
					<td><?=$file['size']?> bytes</td>
					<td><?=$file['commit']['rev']?></td>
					<td><?=$file['commit']['author']?></td>
				</tr>
<? } ?>
			</tbody>
		</table>
	</div>
<? include(template('footer')); ?>
</body>
</html>