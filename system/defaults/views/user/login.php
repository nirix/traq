<form action="<?php echo baseurl('login')?>" method="post">
	<div class="box">
		<p>
			<label for="username"><?php _l('Username')?></label>
			<input type="text" name="username" id="username" required>
		</p>
		<p>
			<label for="password"><?php _l('Password')?></label>
			<input type="password" name="password" id="password" required>
		</p>
		<p>
			<span><input type="checkbox" id="remember_me"> <label for="remember_me"><?php _l('Remember')?></label></span>
			<input type="submit" value="<?php _l('Login')?>">
		</p>
	</div>
</form>