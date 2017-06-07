<?php

namespace Yap\Foundation\Notifications;

use Illuminate\Database\Eloquent\Builder;

/**
 * Yap\Foundation\Notifications\DatabaseNotification
 *
 * @property string $id
 * @property string $type
 * @property int $notifiable_id
 * @property string $notifiable_type
 * @property array $data
 * @property \Carbon\Carbon $read_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification filter($filterName = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereNotifiableId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereNotifiableType($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereReadAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Foundation\Notifications\DatabaseNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DatabaseNotification extends \Illuminate\Notifications\DatabaseNotification
{

    public function scopeFilter(Builder $query, $filterName = null)
    {
        switch ($filterName) {
            case 'read':
                return $query->whereNotNull('read_at');
            case 'all':
                return $query;
            default:
                return $query->whereNull('read_at');
        }
    }

}