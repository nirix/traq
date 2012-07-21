<?php form('/step/3'); ?>
	<div class="tabular">
		<div class="group<?php echo isset($errors['username']) ? ' error' : ''; ?>">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" />
		</div>
		<div class="group<?php echo isset($errors['name']) ? ' error' : ''; ?>">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" />
		</div>
		<div class="group<?php echo isset($errors['password']) ? ' error' : ''; ?>">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" />
		</div>
		<div class="group<?php echo isset($errors['email']) ? ' error' : ''; ?>">
			<label for="email">Email</label>
			<input type="text" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
		</div>
		<div class="group">
			<input type="submit" value="Next" />
		</div>
	</div>
</form>