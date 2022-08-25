<?php

namespace Rvdlee\AsanaCLI\Service;

use Asana\Client as AsanaClient;
use Rvdlee\AsanaCLI\Model\Settings;
use Rvdlee\AsanaCLI\Model\User;
use Rvdlee\AsanaCLI\Model\Workspace;

class AsanaService
{
    public const CACHE_PATH = '../../cache';
    public const DEFAULT_OFFSET = 0;
    public const DEFAULT_LIMIT = 50;

    /**
     * @var string
     */
    protected string $accessToken;

    /**
     * @var AsanaClient
     */
    protected AsanaClient $asanaClient;

    /**
     * @var Settings|null
     */
    protected ?Settings $settings;

    public function __construct()
    {
        $this->settings = null;
        $this->settings = $this->fetchSettings();
        $this->accessToken = $_ENV['ASANA_ACCESS_TOKEN'];
        $this->asanaClient = AsanaClient::accessToken($this->accessToken);
    }

    public function __destruct()
    {
        $this->writeSettings();
    }

    public function hasUser(): bool
    {
        if ($this->settings->getUser() === []) {
            return false;
        }

        return true;
    }

    public function login(bool $force = false)
    {
        if ($this->hasUser() === false || $force === true) {
            $userData = $this->asanaClient->users->me();

            $user = [
                'gid' => $userData->gid,
                'name' => $userData->name,
                'resource_type' => $userData->resource_type,
            ];

            $workspaces = [];
            $projects = [];

            foreach ($userData->workspaces as $workspace) {
                $workspaces[] = [
                    'gid' => $workspace->gid,
                    'name' => $workspace->name,
                    'resource_type' => $workspace->resource_type,
                ];

                $projects = array_merge($projects, $this->getProjects($workspace->gid));
            }

            if (count($projects) === 1) {
                $this->settings->setCurrentProject((array) $projects[0]);
            }

            $this->settings->setUser($user);
            $this->settings->setWorkspaces($workspaces);
            $this->settings->setProjects($projects);
        }
    }

    /**
     * @param int $offset
     */
    public function getTasks(int $offset = self::DEFAULT_OFFSET): array
    {
        $tasks = $this->asanaClient->tasks->getTasks(
            [
                'limit' => self::DEFAULT_LIMIT,
                'offset' => $offset,
                'opt_fields' => implode(
                    ',',
                    [
                        'resource_type',
                        'gid',
                        'created_at',
                        'completed',
                        'completed_at',
                        'tags',
                    ]
                )
            ]
        );

        var_dump($tasks);
        return [];
    }

    /**
     * @param int $workspaceGid
     * @return array
     */
    public function getProjects(int $workspaceGid): array
    {
        $projects = $this->asanaClient->projects->getProjects(
            [
                'opt_fields' => implode(
                    ',',
                    [
                        'gid',
                        'name',
                        'current_status',
                        'notes',
                        'html_notes',
                        'public',
                        'team',
                        'workspace',
                    ]
                ),
                'limit' => self::DEFAULT_LIMIT,
                'workspace' => $workspaceGid,
            ],
            [
                'iterator_type' => false
            ]
        );

        return $projects->data;
    }

    /**
     * @param int $workspaceGid
     * @return array
     */
    public function getProjectsByWorkspace(int $workspaceGid): array
    {
        $projects = $this->asanaClient->projects->getProjectsForWorkspace(
            $workspaceGid,
            [
                'limit' => self::DEFAULT_LIMIT,
                'offset' => self::DEFAULT_OFFSET,
                'opt_fields' => implode(
                    ',',
                    [
                        'gid',
                        'name',
                        'current_status',
                        'notes',
                        'notes',
                        'html_notes',
                        'workspace',
                    ]
                )
            ]
        );

        echo var_dump($projects);
        return [];
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array  $data
     */
    private function request(string $method, string $endpoint, array $data)
    {

    }

    private function fetchSettings()
    {
        if ($this->settings === null) {
            $data = [];

            $file = sprintf('%s/%s/settings.json', __DIR__, self::CACHE_PATH);
            if (file_exists($file)) {
                $data = json_decode(file_get_contents($file), true);
            }

            $this->settings = new Settings($data);
        }

        return $this->settings;
    }

    private function writeSettings()
    {
        $file = sprintf('%s/%s/settings.json', __DIR__, self::CACHE_PATH);
        file_put_contents($file, json_encode($this->settings->toArray()));
    }
}