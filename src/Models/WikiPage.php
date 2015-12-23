<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

namespace Traq\Models;

use Avalon\Database\Model;

/**
 * Wiki Page database model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class WikiPage extends Model
{
    protected static $_validations = [
        'title' => ['required'],
        'slug'  => ['required'],
    ];

    protected static $_before = [
        'create' => ['_setSlug'],
        'save'   => ['_setSlug']
    ];

    protected $revision;

    /**
     * @param array   $data
     * @param boolean $is_new
     */
    public function __construct(array $data = [], $isNew = true)
    {
        parent::__construct($data, $isNew);

        if ($isNew) {
            $this->revision = new WikiRevision([
                'revision' => 1,
                'content'  => (isset($data['content']) ? $data['content'] : '')
            ]);
        }
    }

    public function revision()
    {
        if ($this->revision) {
            return $this->revision;
        }

        return WikiRevision::find($this->revision_id);
    }

    /**
     * Set revision.
     *
     * @param WikiRevision $revision
     */
    public function setRevision(WikiRevision $revision)
    {
        $this->revision = $revision;
    }

    /**
     * Removes spaces from the slug.
     */
    protected function _setSlug()
    {
        $this->slug = str_replace(' ', '_', $this->slug);
    }

    /**
     * Checks if the pages data is valid.
     *
     * @return bool
     */
    public function validates($data = null)
    {
        parent::validates($data);

        // Make sure the slug isnt in use..
        $checkSlug = static::select('id')
            ->where('id != ?', ($this->_isNew ? 0 : $this->id))
            ->andWhere('slug = ?', $this->slug)
            ->andWhere('project_id = ?', $this->project_id);

        if ($checkSlug->rowCount()) {
            $this->addError('slug', 'unique', [
                    'field' => 'slug',
                    'error' => "already_in_use"
            ]);
        }

        return count($this->_errors) == 0;
    }

    public function delete()
    {
        static::connection()->delete(static::tableName(), ['id' => $this->id]);
        static::connection()->delete(WikiRevision::tableName(), ['wiki_page_id' => $this->id]);
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'title'      => $this->title,
            'slug'       => $this->slug,
            'main'       => $this->main,
            'project_id' => $this->project_id,
            'revision'   => $this->revision()->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
