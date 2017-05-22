<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Project extends Model
{

    use Sortable;

    public $sortable = [
        'name',
        'description',
        'archive_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'github_team_id',
        'github_repository_id',
        'taiga_id',
        'project_type_id',
        'name',
        'description',
        'is_archived',
        'archive_at',
    ];

    protected $dates = [
        'archive_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'github_team_id'       => 'int',
        'github_repository_id' => 'int',
        'taiga_id'             => 'int',
        'project_type_id'      => 'int',
        'is_archived'          => 'boolean',
    ];


    public function type()
    {
        return $this->hasOne(ProjectType::class, 'id', 'project_type_id');
    }


    public function addLeaders(array $userIds)
    {
        foreach ($userIds as $userId) {
            $this->addLeader($userId);
        }
    }


    public function addLeader(int $userId)
    {
        return $this->leaders()->attach($userId, ['is_leader' => true]);
    }


    public function leaders()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', '1')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership')->withTimestamps();
    }


    public function removeLeader(int $userId)
    {
        return $this->leaders()->detach($userId);
    }


    public function addParticipants(array $userIds)
    {
        foreach ($userIds as $userId) {
            $this->addParticipant($userId);
        }
    }


    public function addParticipant(int $userId)
    {
        return $this->participants()->attach($userId);
    }


    public function participants()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', '0')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership')->withTimestamps();
    }


    public function removeParticipant(int $userId)
    {
        return $this->participants()->detach($userId);
    }
}
