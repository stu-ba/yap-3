<?php

namespace Yap\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

/**
 * Yap\Models\Invitation
 *
 * @property int $id
 * @property int $user_id
 * @property int $invited_by
 * @property string $email
 * @property string $token
 * @property \Carbon\Carbon $depleted_at
 * @property \Carbon\Carbon $valid_until
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Yap\Models\User $inviter
 * @property-read \Yap\Models\User $user
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation active()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation recent($num = 10)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation sortable($defaultSortParameters = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation validUntil(\Carbon\Carbon $date = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereDepletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereInvitedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Invitation whereValidUntil($value)
 * @mixin \Eloquent
 */
class Invitation extends Model
{

    use Sortable;

    public $sortable = [
        'email',
        'invited_at',
        'created_at',
        'updated_at',
        'valid_until',
        'depleted_at',
        'invited',
    ];

    protected $fillable = [
        'user_id',
        'invited_by',
        'email',
        'token',
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
        'user_id'    => 'int',
        'invited_by' => 'int',
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
            $model->attributes['token']       = $model->attributes['token'] ?? base64_encode(str_random(63));
            $model->attributes['invited_by']  = $model->attributes['invited_by'] ?? $model->determineCreator();
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * @param Builder $query
     *
     * @param int     $num
     *
     * @return Builder
     */
    public function scopeRecent($query, int $num = 10): Builder
    {
        return $query->select(['invited_by', 'email', 'valid_until', 'created_at', 'updated_at'])
                     ->orderBy('updated_at', 'desc')->limit($num);
    }


    public function scopeActive(Builder $query): Builder
    {

        return $query->whereNull('depleted_at');
    }


    public function scopeValidUntil(Builder $query, Carbon $date = null): Builder
    {
        if (is_null($date)) {
            $date = Carbon::now()->subDays(config('yap.invitations.valid_until', 7));
        }

        return $query->whereDate('valid_until', '>', $date)->orWhereNull('valid_until');
    }


    public function swapUser(User $user): self
    {
        $originalUser = $this->user;

        $this->user_id = $user->id;
        $this->save();

        $user->swapWith($originalUser);

        return $this;
    }


    public function isDepleted(): bool
    {
        if ($this->inviter->isBanned() || ! is_null($this->depleted_at) || ! (is_null($this->valid_until) ?:
                ! $this->valid_until->lessThan(Carbon::now()))
        ) {
            return true;
        }

        return false;
    }


    public function deplete(): self
    {
        $this->depleted_at = Carbon::now();
        $this->save();

        return $this;
    }


    public function prolong(Carbon $date = null): self
    {
        if (is_null($date) || $date->lt(Carbon::now())) {
            $date = Carbon::now()->addDays(config('yap.invitations.valid_until'));
        }

        $this->valid_until = $date;
        $this->save();

        return $this;
    }


    /**
     * Update inviter iff inviter is administrator.
     *
     * @param User $inviter
     *
     * @return Invitation
     */
    public function updateInviter(User $inviter): self
    {
        if ($inviter->is_admin && $this->inviter->id !== $inviter->id) {
            $this->inviter()->associate($inviter)->save();
        }

        return $this;
    }


    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }


    /**
     * Updates valid until to indefinite if not previously set as such.
     *
     * @return Invitation
     */
    public function makeIndefinite(): self
    {
        if (is_null($this->depleted_at) && ! is_null($this->valid_until)) {
            $this->valid_until = null;
            $this->save();
        }

        return $this;
    }


    private function determineCreator()
    {
        return auth()->id() ?? auth('yap')->id() ?? systemAccount()->id;
    }


    private function determineValidUntil()
    {
        if (isset($this->attributes['valid_until']) && $this->attributes['valid_until'] === 0) {
            return null;
        }

        return $this->attributes['valid_until'] ?? Carbon::now()->addDays(config('yap.invitations.valid_until'));
    }
}
