<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

namespace SecurityQuestions\controllers;

use avalon\http\Request;
use avalon\output\View;

/**
 * Security Questions settings controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package SecurityQuestions
 * @subpackage Controllers
 */
class Questions extends \traq\controllers\admin\AppController
{
    /**
     * Question management page.
     */
    public function action_index()
    {
        // Set page title
        $this->title(l('security_questions'));

        // Extract questions
        $questions = json_decode(settings('security_questions'), true);

        // Add an empty question
        if (!count($questions)) {
            $questions[] = array('question' => '', 'answers' => '');
        }

        // Check if the form has been submitted
        $errors = array();
        if (Request::method() == 'post') {
            // Process questions
            $updated_questions = array();
            foreach (Request::$post['questions'] as $id => $question) {
                // Check fields
                foreach ($question as $field => $value) {
                    if (empty($value)) {
                        $errors[$id][$field] = true;
                    }
                }

                // Add if no errors
                if (!isset($errors[$id])) {
                    $updated_questions[] = $question;
                }
            }

            // Save and redirect
            if (!count($errors)) {
                $this->db->update('settings')->set(array('value' => json_encode($updated_questions)))->where('setting', 'security_questions')->exec();
                Request::redirect(Request::requestUri());
            }
        }

        View::set(compact('questions', 'errors'));
    }

    /**
     * Used to create a blank question box.
     *
     * @return string
     */
    public function action_new_question()
    {
        $this->render['layout'] = false;
        return View::get('questions/_question', array(
            'id' => time(),
            'question' => array(
                'question' => '',
                'answers'   => ''
            )
        ));
    }
}
