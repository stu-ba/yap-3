<?php

namespace Yap\Foundation\Notifications;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
                return $query->whereBetween('read_at', [Carbon::now(), Carbon::now()->addMinute()])->orWhereNull('read_at');
        }
    }
}