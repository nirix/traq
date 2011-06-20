<?php

class ErrorController extends AppController
{
	public function action_404()
	{
		View::set('request', Request::url());
	}
}