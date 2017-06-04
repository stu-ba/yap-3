<?php

namespace Yap\Listeners;

use Illuminate\Queue\InteractsWithQueue;

trait DelayJob
{

    use InteractsWithQueue {
        InteractsWithQueue::release as parentRelease;
    }

    protected $checker;

    protected $delay = 5;


    public function __call($method, $parameters)
    {
        if ($method == 'handle') {
            $args = [
                call_user_func([$this, 'check']),
                function () use ($parameters) {
                    call_user_func_array([$this, 'handle'], $parameters);
                },
            ];

            return call_user_func_array([$this, 'runIf'], $args);
        }
    }


    public function runIf($condition, $callback)
    {
        if ($condition) {
            $callback();
        } else {
            $this->release();
        }
    }


    protected function release(): void
    {
        $db         = resolve(\Illuminate\Database\DatabaseManager::class);
        $releasedId = $this->parentRelease($this->delay * 60 + 10); //+10 in case of $delay = 0
        $db->table(config('queue.connections.database.table'))->where('id', $releasedId)->decrement('attempts');
    }


    public function check(): bool
    {
        return $this->checker->check();
    }

}