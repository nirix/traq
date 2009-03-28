<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(l('tickets'),$project['name']))?></title>
<? include(template('headerinc')); ?>
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content" class="query">
		<? include(template("breadcrumbs")); ?>
		<h1><?=l('tickets')?></h1>
		<form method="post" action="<?=$uri->geturi()?>">
			<input type="hidden" name="sort" value="<?=$sort?>" />
			<input type="hidden" name="order" value="<?=$order?>" />
			<fieldset id="filters">
				<legend><?=l('filters')?></legend>
				<table>
					<? foreach($filters as $filter) { ?>
					<? if($filter['type'] == 'milestone') { ?>
					<tbody>
						<tr class="milestone">
							<th scope="row"><label><?=l('milestone')?></label></th>
							<td class="mode">
								<select name="filters[milestone][mode]">
								<option value=""<?=($filter['mode'] == '' ? ' selected="selected"' : '')?>><?=l('is')?></option>
								<option value="!"<?=($filter['mode'] == '!' ? ' selected="selected"' : '')?>><?=l('is_not')?></option>
								</select>
							</td>
							<td class="filter">
								<select name="filters[milestone][value]">
									<option></option>
									<? foreach(projectmilestones($project['id'],true) as $milestone) { ?>
									<option value="<?=$milestone['milestone']?>"<?=($filter['value'] == $milestone['milestone'] ? ' selected="selected"' : '')?>><?=$milestone['milestone']?></option>
									<? } ?>
								</select>
							</td>
							<td class="actions"><input type="submit" name="rm_filter_milestone" value="-" /></td>
						</tr>
					</tbody>
					<? } elseif($filter['type'] == 'status') { ?>
					<tbody>
						<tr class="status">
							<th scope="row"><label><?=l('status')?></label></th>
							<td class="filter" colspan="2">
								<? foreach(getstatustypes() as $type) { ?>
								<label><input type="checkbox" name="filters[status][values][<?=$type['id']?>]" value="<?=$type['id']?>"<?=((in_array($type['id'],$filter['values'])  or ($type['id'] <= 0 and $filter['value'] == 'closed') or ($type['id'] >= 1 and $filter['value'] == 'open')) ? ' checked="checked"' : '')?> /> <?=$type['name']?></label>
								<? } ?>
							</td>
							<td class="actions"><input type="submit" name="rm_filter_status" value="-" /></td>
						</tr>
					</tbody>
					<? } ?>
					<? } ?>
					<tbody>
						<tr class="actions">
							<td colspan="2"><input type="submit" value="<?=l('update')?>" /></td>
							<td class="actions" colspan="2" style="text-align: right">
								<label for="add_filter"><?=l('add_filter')?></label> 
								<select name="add_filter" id="add_filter">
									<option></option>
									<!--<option value="component"><?=l('component')?></option>
									<option value="description"><?=l('description')?></option>-->
									<option value="milestone"><?=l('milestone')?></option>
									<!--<option value="owner"><?=l('owner')?></option>
									<option value="priority"><?=l('priority')?></option>
									<option value="reporter"><?=l('reporter')?></option>
									<option value="severity"><?=l('severity')?></option>-->
									<option value="status"><?=l('status')?></option>
									<!--<option value="summary"><?=l('summary')?></option>
									<option value="type"><?=l('type')?></option>
									<option value="version"><?=l('version')?></option>-->
								</select>
								<input type="submit" name="add" value="+" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
		<table class="listing tickets">
			<thead>
				<tr>
					<th class="id"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=tid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('ticket')?></a></th>
					<th class="summary"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=summary&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('summary')?></a></th>
					<th class="status"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=status&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('status')?></a></th>
					<th class="owner"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=ownername&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('owner')?></a></th>
					<th class="type"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=type&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('type')?></a></th>
					<th class="priority"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=priority&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('priority')?></a></th>
					<th class="component"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=componentid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('component')?></a></th>
					<? if(!$uri->seg[2]) { ?>
					<th class="milestone"><a href="?<?=($filterstring != '' ? $filterstring.'&' : '')?>sort=milestoneid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>"><?=l('milestone')?></a></th>
					<? } ?>
				</tr>
			</thead>
			<tbody>
<? foreach($tickets as $ticket) {
	if($bgclass == "even") {
		$bgclass = "odd";
	} else {
		$bgclass = "even";
	}
?>
				<tr class="<?=$bgclass?> priority<?=$ticket['priority']?>">
					<td class="id"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>"><?=$ticket['tid']?></a></td>
					<td class="summary"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>"><?=$ticket['summary']?></a></td>
					<td class="status"><?=ticketstatus($ticket['status'])?></td>
					<td class="owner"><?=$ticket['ownername']?></td>
					<td class="type"><?=tickettype($ticket['type'])?></td>
					<td class="priority"><?=ticketpriority($ticket['priority'])?></td>
					<td class="component"><?=$ticket['component']['name']?></td>
					<? if(!$uri->seg[2]) { ?>
					<td class="milestone"><a href="<?=$uri->anchor($project['slug'],'milestone',$ticket['milestone']['milestone'])?>"><?=$ticket['milestone']['milestone']?></a></td>
					<? } ?>
				</tr>
<? } ?>
			</tbody>
		</table>
	</div>
<? include(template('footer')); ?>
</body>
</html>