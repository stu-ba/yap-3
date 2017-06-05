<?php

namespace Yap\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $members
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yap\Models\User[] $participants
 * @property-read \Yap\Models\ProjectType $type
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project confirmed()
 * @method static \Illuminate\Database\Query\Builder|\Yap\Models\Project filter($filterName = null)
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


    public function setArchiveAtAttribute($value)
    {
        //TODO: finish this
        if (is_null($value)) {
            $this->attributes['archive_at'] = null;
        } elseif (is_string($value)) {
            $this->attributes['archive_at'] = Carbon::createFromFormat('d/m/Y', $value)->endOfDay();
        } elseif ($value instanceof Carbon) {
            $this->attributes['archive_at'] = $value->endOfDay();
        }
    }


    public function type()
    {
        return $this->hasOne(ProjectType::class, 'id', 'project_type_id');
    }


    public function leaders()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', true)
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }


    public function scopeConfirmed($query)
    {
        //TODO:add policy to list also banned users
        return $query->where('users.is_confirmed', '=', true);
    }


    public function scopeFilter(Builder $query, string $filterName = null): Builder
    {
        $query->with([
            'leaders'      => function ($query) {
                $query->select('name', 'username', 'is_confirmed')->filled()->orderBy('username');
            },
            'participants' => function ($query) {
                $query->select('name', 'username')->filled()->orderBy('username');
            },
        ]);

        //TODO: above can be simplified to use only members relationship + collection magic to divide leaders and non leaders

        switch ($filterName) {
            case 'mine':
                $mineProjectIds = auth()->user()->projects()->select('id')->pluck('id');

                return $query->whereIn('id', $mineProjectIds);//where('pivot_user_id', '=', auth()->user()->id);
            case 'archived':
                return $query->whereTime('archive_at', '<', Carbon::now());
            default:
                return $query;
        }
    }


    public function syncMembers(array $leaderIds, array $participantIds)
    {
        $leaderIds      = array_fill_keys($leaderIds, ['is_leader' => true, 'to_be_deleted' => false]);
        $participantIds = array_fill_keys($participantIds, ['is_leader' => false, 'to_be_deleted' => false]);
        $memberIds      = $participantIds + $leaderIds;
        $changed        = $this->members()->syncWithoutDetaching($memberIds);
        $this->removeMembers($this->toDetach($memberIds));
        //TODO: fire events if updated (to make leader or to make participant)
    }


    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }


    public function removeMembers(array $userIds): void
    {
        foreach ($userIds as $userId) {
            $this->removeMember($userId);
        }
    }


    public function removeMember(int $userId): void
    {
        $this->members()->updateExistingPivot($userId, ['to_be_deleted' => true]);
    }


    private function toDetach(array $ids): array
    {
        $current = $this->members->pluck('id')->all();

        return array_diff($current, array_keys($ids));
    }


    public function addMember(int $userId, bool $leader = false): void
    {
        //TODO: possible error if already attached + marked for deletion
        $this->members()->attach($userId, ['is_leader' => $leader]);
    }


    public function participants()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->wherePivot('is_leader', '=', false)
                    ->withPivot('is_leader', 'has_github_team', 'has_taiga_membership', 'to_be_deleted')
                    ->withTimestamps();
    }
}
