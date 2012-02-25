<?php
class API
{
	public static function user_array($user)
	{
		return array(
			'id' => $user->id,
			'username' => $user->username,
			'name' => $user->name,
			'email' => $user->email,
			'group_id' => $user->group_id
		);
	}
};
?>