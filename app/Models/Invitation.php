<?php

namespace Yap\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Yap\Models\Invitation
 *
 * @property-read \Yap\Models\User $creator
 * @property-read \Yap\Models\User $user
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
        'user_id'     => 'int',
        'created_by'  => 'int',
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


    public function isTokenValid(): bool
    {
        if ($this->creator->is_banned || $this->is_depleted || ! (is_null($this->valid_until) ?: ! $this->valid_until->lessThan(Carbon::now()))) {
            return false;
        }

        return true;
    }


    public function deplete(): self
    {
        $this->is_depleted = true;
        $this->depleted_at = Carbon::now();
        $this->save();

        return $this;
    }
}
