<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

use Avalon\Language;

/**
 * Wiki page model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class WikiPage extends Model
{
    protected static $_validations = [
        'title' => ['required'],
        'slug' => ['required']
    ];

    protected static $_hasMany = [
        'revisions' => ['model' => 'WikiRevision']
    ];

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $project_id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var int
     */
    public $revision_id;

    /**
     * @var WikiRevision
     */
    protected $revision;

    /**
     * @return WikiRevision
     */
    public function revision()
    {
        if ($this->revision) {
            return $this->revision;
        }

        return $this->revision = $this->revisions()->orderBy('revision', 'DESC')->fetch();
    }

    /**
     * Delete wiki page, revisions and timeline events.
     */
    public function delete()
    {
        foreach ($this->revisions()->fetchAll() as $revision) {
            $revision->delete();
        }

        $timelineEvents = Timeline::select()
            ->where("owner_type = :ownerType")
            ->andWhere('owner_id = :ownerId')
            ->setParameter(':ownerType', 'WikiPage')
            ->setParameter(':ownerId', $this['id'])
            ->fetchAll();

        foreach ($timelineEvents as $event) {
            $event->delete();
        }

        return parent::delete();
    }

    /**
     * @return bool
     */
    public function validate()
    {
        // Validate unique slug for the project
        $result = static::where('project_id = :project_id')
            ->andWhere('slug = :slug')
            ->setParameter(':project_id', $this->project_id)
            ->setParameter(':slug', $this->slug);

        if ($this->id) {
            $result->andWhere('id != :id')
                ->setParameter(':id', $this->id);
        }

        $result = $result->rowCount();

        if ($result) {
            $this->addError(
                'slug',
                Language::translate(
                    'errors.validations.unique',
                    [
                        'field' => Language::translate('slug')
                    ]
                )
            );
        }

        return parent::validate();
    }
}
