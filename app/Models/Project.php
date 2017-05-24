<?php

namespace Yap\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Yap\Events\ProjectCreated;

/**
 * Yap\Models\Project
 *
 * @property int                                                              $id
 * @property int                                                              $github_team_id
 * @property int                                                              $github_repository_id
 * @property int                                                              $taiga_id
 * @property int                                                              $project_type_id
 * @property string                                                           $name
 * @property string                                                           $description
 * @property bool                                                             $is_archived
 * @property \Carbon\Carbon                                                   $archive_at
 * @property \Carbon\Carbon                                                   $created_at
 * @property \Carbon\Carbon                                                   $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $leaders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $participants
 * @property-read \Yap\Models\ProjectType                                     $type
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


    public function leaders()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', '1')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }


    public function syncMembers(array $leaderIds, array $participantIds)
    {
        $leaderIds      = array_fill_keys($leaderIds, ['is_leader' => true, 'to_be_deleted' => false]);
        $participantIds = array_fill_keys($participantIds, ['is_leader' => false, 'to_be_deleted' => false]);
        $memberIds      = $participantIds + $leaderIds;
        $changed        = $this->members()->syncWithoutDetaching($memberIds);
        $this->removeMembers($this->toDetach($memberIds)); //this can be improved with $changed['to_detach']
        //TODO: fire events if updated (to make leader or make participant)
    }


    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }


    public function removeMembers(array $userIds)
    {
        foreach ($userIds as $userId) {
            $this->removeMember($userId);
        }
    }


    public function removeMember(int $userId)
    {
        return $this->members()->updateExistingPivot($userId, ['to_be_deleted' => true]);
    }


    private function toDetach(array $ids): array
    {
        $current = $this->members->pluck('id')->all();

        return array_diff($current, array_keys($ids));
    }


    public function participants()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', '0')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }

}
