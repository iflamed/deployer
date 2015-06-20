<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\StoreProjectRequest;
use App\Jobs\SetupProject;
use App\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use Lang;

/**
 * The controller for managging projects.
 */
class ProjectController extends Controller
{
    /**
     * Shows all projects.
     *
     * @param  ProjectRepositoryInterface $projectRepository
     * @return Response
     */
    public function index(
        ProjectRepositoryInterface $projectRepository,
        TemplateRepositoryInterface $templateRepository
    ) {
        $projects = $projectRepository->getAll();

        return view('admin.projects.listing', [
            'title'     => Lang::get('projects.manage'),
            'templates' => $templateRepository->getAll(),
            'projects'  => $projects->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url'
        ));

        if ($request->has('template_id')) {
            $this->dispatch(new SetupProject(
                $project,
                $request->template_id
            ));
        }

        return $project;
    }

    /**
     * Update the specified project in storage.
     *
     * @param  Project             $project
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function update(Project $project, StoreProjectRequest $request)
    {
        $project->update($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url'
        ));

        return $project;
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  Project  $project
     * @return Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return [
            'success' => true,
        ];
    }
}
