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

namespace traq\models;

use avalon\database\Model;

/**
 * Wiki Page database model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P. <jack@traq.io>
 * @copyright (c) Jack P. <jack@traq.io>
 */
class WikiPage extends Model
{
    protected static $_name = 'wiki';
    protected static $_properties = array(
        'id',
        'project_id',
        'title',
        'slug',
        'main',
        'revision_id'
    );

    protected static $_escape = array(
        'title'
    );

    protected static $_has_many = array(
        'revisions' => array('model' => "WikiRevision", 'foreign_key' => 'wiki_page_id')
    );

    protected static $_belongs_to = array(
        'project',
        'revision' => array('model' => "WikiRevision")
    );

    protected static $_filters_before = array(
        'create' => array('_set_slug'),
        'save'   => array('_set_slug')
    );

    /**
     * @param array   $data
     * @param boolean $is_new
     */
    public function __construct($data = array(), $is_new = true)
    {
        parent::__construct($data, $is_new);

        if ($is_new) {
            $this->revision = new WikiRevision(array(
                'revision' => 1,
                'content'  => (isset($data['content']) ? $data['content'] : '')
            ));
        }
    }

    /**
     * Returns the URI for the page.
     *
     * @param string $uri Extra segments to be appended to the URI.
     *
     * @return string
     */
    public function href($uri = null)
    {
        return "/{$this->project->slug}/wiki/{$this->slug}" . ($uri !== null ? '/' . implode('/', func_get_args()) : '');
    }

    /**
     * Removes spaces from the slug.
     */
    protected function _set_slug()
    {
        $this->slug = str_replace(' ', '_', $this->slug);
    }

    /**
     * Checks if the pages data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check if the name is set
        if (empty($this->_data['title'])) {
            $errors['title'] = l('errors.page_title_blank');
        }

        // Make sure the slug isnt in use..
        $select_slug = static::select('id')->where('id', ($this->_is_new() ? 0 : $this->id), '!=')
            ->where('slug', $this->_data['slug'])->where('project_id', $this->_data['project_id']);

        if ($select_slug->exec()->row_count()) {
            $errors['slug'] = l('errors.slug_in_use');
        }

        // Check if the slug is set.
        if (empty($this->_data['slug'])) {
            $errors['slug'] = l('errors.slug_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }
}
