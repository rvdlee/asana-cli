<?php

namespace Rvdlee\AsanaCLI\Model;

class Settings extends AbstractModel
{
    public const MAPPINGS = [
        'user' => 'user',
        'projects' => 'projects',
        'current_project' => 'current_project',
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
    protected array $currentProject = [];

    /**
     * @var array
     */
    protected array $workspaces = [];

    public function toArray(): array
    {
        return [
            'user' => $this->getUser(),
            'projects' => $this->getProjects(),
            'current_project' => $this->getCurrentProject(),
            'workspaces' => $this->getWorkspaces(),
        ];
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
    public function getCurrentProject(): array
    {
        return $this->currentProject;
    }

    /**
     * @param array $currentProject
     */
    public function setCurrentProject(array $currentProject): void
    {
        $this->currentProject = $currentProject;
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