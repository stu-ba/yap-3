<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{

    protected $fillable = ['taiga_id', 'name', 'description'];

    protected $casts = [
        'taiga_id' => 'int',
    ];
}
