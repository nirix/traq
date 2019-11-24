<?php

namespace Traq;

use Illuminate\Database\Eloquent\Model;

class WikiPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'project_id',
    ];

    public function revisions()
    {
        return $this->hasMany(WikiRevision::class);
    }

    public function latestRevision(): WikiRevision
    {
        return $this->revisions()->latest()->first();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
