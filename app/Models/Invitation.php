<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Yap\Models\Invitation
 *
 * @property int $id
 * @property int $user_id
 * @property int $created_by
 * @property string $email
 * @property string $token
 * @property bool $is_depleted
 * @property \Carbon\Carbon $depleted_at
 * @property \Carbon\Carbon $valid_until
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Yap\Models\User $creator
 * @property-read \Yap\Models\User $user
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereDepletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereIsDepleted($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereValidUntil($value)
 * @mixin \Eloquent
 */
class Invitation extends Model
{

    protected $fillable = [
        'user_id',
        'created_by',
        'email',
        'token',
        'is_depleted',
        'depleted_at',
        'valid_until'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'depleted_at',
        'valid_until'
    ];

    protected $casts = [
        'is_depleted' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
