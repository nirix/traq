<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('New Ticket',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<h1><?=$project['name']?>: New Ticket</h1>
		<form id="newticket" method="post" action="/newticket">
			<input type="hidden" name="action" value="newticket" />
			<fieldset id="summary">
				<legend>Summary</legend>
				<input type="text" name="subject" id="subject" size="80" value="" />
			</fieldset>
			<fieldset id="description">
				<legend>Description</legend>
				<textarea name="body" id="body" rows="10" cols="80"></textarea>
			</fieldset>
			<fieldset id="properties">
				<legend>Properties</legend>
				<table>
					<tr>
						<th class="col1">Type</th>
						<td class="col2">
							<select name="type" id="type">
								<option value="1">Defect</option>
								<option value="2">Enhancement</option>
								<option value="3">Feature Request</option>
								<option value="4">Task</option>
							</select>
						</td>
						<th class="col2">Assign to</th>
						<td>
							<select name="assignto" id="assignto">
								<option value="1">Jack</option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1">Priority</th>
						<td>
							<select name="priority" id="priority">
								<option value="1">Highest</option>
								<option value="2">High</option>
								<option value="3" selected="selected">Normal</option>
								<option value="4">Low</option>
								<option value="5">Lowest</option>
							</select>
						</td>
						<th class="col2">Severity</th>
						<td>
							<select name="severity" id="severity">
								<option value="1">Blocker</option>
								<option value="2">Critical</option>
								<option value="3">Major</option>
								<option value="4" selected="selected">Normal</option>
								<option value="5">Minor</option>
								<option value="6">Trivial</option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1">Milestone</th>
						<td>
							<select name="milestone" id="milestone">
								<? foreach(projectmilestones($project['id']) as $milestone) { ?>
								<option value="<?=$milestone['id']?>"><?=$milestone['milestone']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2">Version</th>
						<td>
							<select name="version" id="version">
								<option selected="selected" value=""> </option>
								<? foreach(projectversions($project['id']) as $version) { ?>
								<option value="<?=$version['id']?>"><?=$version['version']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1">Component</th>
						<td>
							<select name="component" id="component">
								<? foreach(projectcomponents($project['id']) as $component) { ?>
								<option value="<?=$component['id']?>"><?=$component['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"></th>
						<td></td>
					</tr>
				</table>
			</fieldset>
			<p></p>
			<div class="buttons">
				<input type="submit" value="Submit Ticket" /> <input type="button" value="Cancel" onclick="javascript:history.back()" />
			</div>
		</form>
	</div>
<? include(template('footer')); ?>
</body>
</html>