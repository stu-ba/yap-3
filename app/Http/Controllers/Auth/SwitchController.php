<?php

namespace Yap\Http\Controllers\Auth;

use Yap\Auxiliary\ApiAdaptors\Github;
use Yap\Auxiliary\ApiAdaptors\Taiga;
use Yap\Http\Controllers\Controller;
use Yap\Models\Project;
use Yap\Models\User;

class SwitchController extends Controller
{

    public function toTaiga()
    {
        return redirect()->away(toTaiga('discover'));
    }


    public function toTaigaProject(Project $project, Taiga $taigaAdaptor)
    {
        if (is_null($project->taiga_id)) {
            alert('warning', 'Project \''.$project->name.'\' does not have double in Taiga yet, try in few minutes.');

            return redirect()->back();
        }

        $taigaProject = $taigaAdaptor->getProjectById($project->taiga_id);
        if (is_null($taigaProject)) {
            alert('error', 'Data corruption! Contact administrator and explain what you did.');

            //TODO: this may return NULL therefore data is corrupted, log this or something.
            return redirect()->back();
        }

        return redirect()->away(toTaiga('project/'.$taigaProject->slug));
    }


    public function toTaigaUser(User $user)
    {
        if (is_null($user->username)) {
            alert('warning', 'User does not have username, please do not play with URL.');

            return redirect()->back();
        }

        return redirect()->away(toTaiga('profile/'.$user->username));
    }


    public function toGithubRepository(Project $project, Github $githubAdaptor)
    {
        if (is_null($project->github_repository_id)) {
            alert('warning', 'Project \''.$project->name.'\' does not have GitHub repository.');

            return redirect()->back();
        }

        $repository = $githubAdaptor->getRepositoryById($project->github_repository_id);

        return redirect()->away($repository['html_url']);
    }
}
