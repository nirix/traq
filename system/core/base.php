<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

require_once SYSPATH . '/core/error.php';
require_once SYSPATH . '/core/load.php';
require_once SYSPATH . '/core/controller.php';
require_once SYSPATH . '/core/avalon.php';
require_once SYSPATH . '/core/database.php';

Load::lib('request');
Load::lib('router');
Load::lib('output');
Load::lib('view');