<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Login'))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor()?>">Projects</a></li>
			<li class="last"><a href="http://rainbirdstudios.com/projects/traq/">Traq</a></li>
		</ul>
	</div>
	<div id="content">
		<h1>Login</h1>
		<form action="<?=$uri->anchor('user','login')?>" method="post">
			<input type="hidden" name="action" value="login" />
			<div class="form login">
				<fieldset>
					<label>Username</label>
					<input type="text" name="username" />
				</fieldset>
				<fieldset>
					<label>Password</label>
					<input type="password" name="password" />
				</fieldset>
				<fieldset>
					<label for="remember">Remember</label> <input type="checkbox" name="remember" value="1" id="remember" />
					<input type="submit" value="Login" /> <input type="button" onclick="javascript:history.back()" value="Cancel" />
				</fieldset>
			</div>
		</form>
	</div>
<? include(template('footer')); ?>
</body>
</html>