		<table class="listing files">
			<thead>
				<tr>
					<th><?php echo l('name')?></th>
					<th><?php echo l('revision')?></th>
					<th><?php echo l('author')?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($files as $file) { ?>
				<tr class="<?php echo altbg()?>">
					<td><?php if($file['kind'] == 'dir') { ?><a href="<?php echo $uri->geturi()?>/<?php echo $file['name']?>"><?php echo $file['name']?></a><?php } else { ?><?php echo $file['name']?><?php } ?></td>
					<td><?php echo $file['commit']['rev']?></td>
					<td><?php echo $file['commit']['author']?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>