<?php

namespace Traq\Controllers;

use Avalon\Http\Controller;

class Misc extends Controller
{
    protected $layout = null;

    public function jsAction()
    {
        $resp = $this->render('misc/js.php');
        $resp->contentType = "application/javascript";
        return $resp;
    }
}
