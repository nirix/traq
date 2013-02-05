<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

namespace traq\plugins;

use \FishHook;
use \HTML;
use avalon\Autoloader;
use avalon\Database;
use avalon\http\Router;
use avalon\http\Request;
use avalon\output\View;

use traq\models\Setting;

/**
 * Security Questions Plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class SecurityQuestions extends \traq\libraries\Plugin
{
    protected static $info = array(
        'name'    => 'Security Questions',
        'version' => '1.0',
        'author'  => 'Jack P.'
    );

    public static function init()
    {
        // Register namespace
        Autoloader::registerNamespace('SecurityQuestions', __DIR__);

        // Add routes
        Router::add('/admin/settings/security_questions', 'SecurityQuestions::controllers::Questions.index');
        Router::add('/admin/settings/security_questions/new_question', 'SecurityQuestions::controllers::Questions.new_question');

        // Hook into the settings navbar
        FishHook::add('template:admin/settings/_nav', array(get_called_class(), 'admin_nav'));

        // Hook into register form
        FishHook::add('template:users/register', array(get_called_class(), 'question_field'));

        // Hook into the register action
        FishHook::add('controller:users.register', array(get_called_class(), 'check_answer'));

        // Allow other plugins to use this plugin
        FishHook::add('use:plugins:security_questions.question_field', array(get_called_class(), 'question_field'));
        FishHook::add('use:plugins:security_questions.check_answer', array(get_called_class(), 'check_answer'));
    }

    /**
     * Adds the link to the settings navbar.
     */
    public static function admin_nav()
    {
        echo '<li' . iif(active_nav('/admin/settings/security_questions'), ' class="active"') . '>' . HTML::link(l('security_questions'), "/admin/settings/security_questions") . '</li>';
    }

    /**
     * Adds the question field to the register form.
     */
     public static function question_field()
     {
         // Get the questions
         $questions = json_decode(settings('security_questions'), true);

         // Get a random question
         $id = rand(0, count($questions) - 1);
         $question = $questions[$id];
         $_SESSION['question_id'] = $id;

         echo View::render('users/_question_field', array('question' => $question));
     }

     /**
      * Checks the submitted answer.
      *
      * @param object $model
      */
     public static function check_answer(&$model)
     {
        $questions = json_decode(settings('security_questions'), true);
        $question  = $questions[$_SESSION['question_id']];
        $answers   = explode('|', $question['answers']);

        if (!in_array(Request::$post['answer'], $answers)) {
            $model->_add_error('answer', l('errors.security_questions.answer_is_wrong'));
        }
     }

    /**
     * Creates the setting row.
     */
    public static function __install()
    {
        Database::connection()->insert(array('setting' => 'security_questions', 'value' => '{}'))->into('settings')->exec();
    }

    /**
     * Deletes the setting row.
     */
    public static function __uninstall()
    {
        Database::connection()->delete()->from('settings')->where('setting', 'security_questions')->exec();
    }
}
