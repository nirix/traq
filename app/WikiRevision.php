<?php

namespace Traq;

use Illuminate\Database\Eloquent\Model;

class WikiRevision extends Model
{
    protected $fillable = [
        'content',
        'user_id',
        'revision',
    ];
}
