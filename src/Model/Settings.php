<?php

namespace Rvdlee\AsanaCLI\Model;

class Settings extends AbstractModel
{
    public const MAPPINGS = [
        'user' => 'user',
        'projects' => 'projects',
        'tasks' => 'tasks',
        'current_project' => 'currentProject',
        'project_cache' => 'projectCache',
        'workspaces' => 'workspaces',
    ];

    /**
     * @var array
     */
    protected array $user = [];

    /**
     * @var array
     */
    protected array $projects = [];

    /**
     * @var array
     */
    protected array $tasks = [];

    /**
     * @var array
     */
    protected array $currentProject = [];

    /**
     * @var array
     */
    protected array $projectCache = [];

    /**
     * @var array
     */
    protected array $workspaces = [];

    public function toArray(): array
    {
        return [
            'user' => $this->getUser(),
            'projects' => $this->getProjects(),
            'tasks' => $this->getTasks(),
            'current_project' => $this->getCurrentProject(),
            'project_cache' => $this->getProjectCache(),
            'workspaces' => $this->getWorkspaces(),
        ];
    }

    /**
     * @param string $reference
     * @return array
     */
    public function setProjectShortReference(string $reference): array
    {
        $currentProject = $this->getCurrentProject();
        if (!key_exists('properties', $currentProject)) {
            $currentProject['properties'] = [];
        }

        $currentProject['properties']['short_reference'] = $reference;
        $this->setCurrentProject($currentProject);

        return $currentProject;
    }

    /**
     * @return string
     */
    public function getProjectShortReference(): string
    {
        return $this->getCurrentProject()['properties']['short_reference'] ?? null;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?array
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * @param array $projects
     */
    public function setProjects(array $projects): void
    {
        $this->projects = $projects;
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @param array $tasks
     */
    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return array
     */
    public function getCurrentProject(): array
    {
        return $this->currentProject;
    }

    /**
     * @param array $currentProject
     */
    public function setCurrentProject(array $currentProject): void
    {
        $projects = $this->getProjects();
        if ($projects !== []) {
            foreach ($projects as $index => $project) {
                if ($project['gid'] === $currentProject['gid']) {
                    $projects[$index] = $currentProject;
                    break;
                }
            }

            $this->setProjects($projects);
        }

        $this->currentProject = $currentProject;
    }

    /**
     * @return array
     */
    public function getProjectCache(): array
    {
        return $this->projectCache;
    }

    /**
     * @param array $projectCache
     */
    public function setProjectCache(array $projectCache): void
    {
        $this->projectCache = $projectCache;
    }

    /**
     * @return array|Workspace[]
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     * @param array|Workspace[] $workspaces
     */
    public function setWorkspaces($workspaces): void
    {
        $this->workspaces = $workspaces;
    }
}