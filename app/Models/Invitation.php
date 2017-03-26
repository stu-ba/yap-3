<?php

namespace Yap\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Yap\Models\Invitation.
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
 *
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
        'valid_until',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'depleted_at',
        'valid_until',
    ];

    protected $casts = [
        'user_id' => 'int',
        'created_by' => 'int',
        'is_depleted' => 'boolean',
    ];

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->attributes['valid_until'] = $model->determineValidUntil();
            $model->attributes['token'] = $model->attributes['token'] ?? base64_encode(str_random(64));
            $model->attributes['created_by'] = $model->attributes['created_by'] ?? $model->determineCreator();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDepleted(): bool
    {
        if ($this->creator->is_banned || $this->is_depleted || ! (is_null($this->valid_until) ?: ! $this->valid_until->lessThan(Carbon::now()))) {
            return true;
        }

        return false;
    }

    public function deplete(): self
    {
        $this->is_depleted = true;
        $this->depleted_at = Carbon::now();
        $this->save();

        return $this;
    }

    public function prolong(Carbon $date = null): self
    {
        if (is_null($date)) {
            $date = Carbon::now()->addDays(config('yap.invitations.valid_until'));
        }

        $this->valid_until = $date;
        $this->save();

        return $this;
    }

    private function determineCreator()
    {
        return auth()->id() ?? systemAccount()->id;
    }

    private function determineValidUntil()
    {
        if (isset($this->attributes['valid_until']) && $this->attributes['valid_until'] === 0) {
            return null;
        }

        return $this->attributes['valid_until'] ?? Carbon::now()->addDays(config('yap.invitations.valid_until'));
    }
}
