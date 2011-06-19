<?php

class ErrorController extends AppController
{
	public function action_404($request)
	{
		View::set('request', $request);
	}
}