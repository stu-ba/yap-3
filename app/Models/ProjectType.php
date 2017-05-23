<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Yap\Models\ProjectType
 *
 * @property int $id
 * @property int $taiga_id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereTaigaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\ProjectType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProjectType extends Model
{

    protected $fillable = ['taiga_id', 'name', 'description'];

    protected $casts = [
        'taiga_id' => 'int',
    ];
}
