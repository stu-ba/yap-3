<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Yap\Events\ProjectCreated;

/**
 * Yap\Models\Project
 *
 * @property int $id
 * @property int $github_team_id
 * @property int $github_repository_id
 * @property int $taiga_id
 * @property int $project_type_id
 * @property string $name
 * @property string $description
 * @property bool $is_archived
 * @property \Carbon\Carbon $archive_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $leaders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $participants
 * @property-read \Yap\Models\ProjectType $type
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project sortable($defaultSortParameters = null)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereArchiveAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereGithubRepositoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereGithubTeamId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereIsArchived($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereProjectTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereTaigaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

    protected $events = [
        'created' => ProjectCreated::class,
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
        //TODO: remove can be only on participant that has false and false on membership to gh and taiga
        return $this->participants()->detach($userId);
    }
}
