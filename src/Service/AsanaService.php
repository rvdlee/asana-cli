<?php

namespace Rvdlee\AsanaCLI\Service;

use Asana\Client as AsanaClient;
use Asana\Dispatcher\OAuthDispatcher;
use DateTime;
use Rvdlee\AsanaCLI\Model\Settings;

class AsanaService
{
    public const CACHE_PATH    = '../../cache';
    public const DEFAULT_LIMIT = 100;

    /**
     * @var string
     */
    protected string $clientId;

    /**
     * @var string
     */
    protected string $clientSecret;

    /**
     * @var string
     */
    protected string $accessToken = '';

    /**
     * @var AsanaClient
     */
    protected $asanaClient;

    /**
     * @var Settings|null
     */
    protected ?Settings $settings;

    public function __construct()
    {
        $this->settings = null;
        $this->settings = $this->fetchSettings();
        $this->clientId = $_ENV['ASANA_CLIENT_ID'];
        $this->clientSecret = $_ENV['ASANA_CLIENT_SECRET'];
        $this->accessToken = $this->fetchAuthToken();

        if ($this->accessToken === '') {
            $this->login(true);
            exit;
        }

        $this->asanaClient = AsanaClient::oauth(
            [
                'client_id' => $this->clientId,
                'token'     => $this->accessToken,
            ]
        );
        $this->asanaClient->options['iterator_type'] = false;
        $this->getSettings()->setProjectShortReference('FUN');
    }

    public function __destruct()
    {
        $this->writeSettings();
    }

    public function hasToken(): bool
    {
        if ($this->fetchAuthToken() === '') {
            return false;
        }

        return true;
    }

    public function login(bool $force = false)
    {
        if ($this->hasToken() === false || $force === true) {
            echo "== Example using OAuth Client ID and Client Secret:\n";

            // create a $client->with the OAuth credentials:
            $client = AsanaClient::oauth(
                [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri'  => OAuthDispatcher::NATIVE_REDIRECT_URI,
                ]
            );
            echo "authorized=" . $client->dispatcher->authorized . "\n";

            # get an authorization URL:
            $state = null;
            $url = $client->dispatcher->authorizationUrl($state);
            try {
                // in a web app you'd redirect the user to this URL when they take action to
                // login with Asana or connect their account to Asana
                exec("xdg-open " . escapeshellarg($url));
            } catch (Exception $e) {
                echo "Open the following URL in a browser to authorize:\n";
                echo "$url\n";
            }

            echo "Copy and paste the returned code from the browser and press enter:\n";

            $code = trim(fgets(fopen("php://stdin", "r")));
            // exchange the code for a bearer token
            $token = $client->dispatcher->fetchToken($code);
            if ($client->dispatcher->authorized) {
                echo "Authorization successful\n";
            } else {
                echo "Authorization failed, exiting...";
                exit(1);
            }

            echo "Hello " . $client->users->me()->name . "\n";
            echo "Your access token is: " . json_encode($token) . "\n";
            $this->writeAuthToken($token);
            echo "Exchanging your refresh token for a new access token because access tokens expire\n";

            // access tokens will expire, use a refresh token to get a fresh access token
            $token = $client->dispatcher->refreshAccessToken();

            echo "Your new access token is: " . json_encode($token) . "\n";
            echo "You are a member of the following workspaces:\n";
            $workspaces = $client->workspaces->findAll();
            foreach ($workspaces as $workspace) {
                echo $workspace->name . "\n";
            }

            // normally you'd persist this token somewhere
            $token = json_encode($token); // (see below)

            // demonstrate creating a client using a previously obtained bearer token
            echo "== Example using OAuth Access Token:\n";


            echo "Your registered email address is: " . $client->users->me()->email;

            $userData = $this->asanaClient->users->me();

            $user = [
                'gid'           => $userData->gid,
                'name'          => $userData->name,
                'resource_type' => $userData->resource_type,
            ];

            $workspaces = [];
            $projects = [];

            foreach ($userData->workspaces as $workspace) {
                $workspaces[] = [
                    'gid'           => $workspace->gid,
                    'name'          => $workspace->name,
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
     * Returns the last set short reference tag
     *
     * @return array
     */
    public function getLastShortReference(): array
    {
        $lastReference = 0;
        $lastGid = null;

        return [$lastReference, $lastGid];
    }

    /**
     * Return the total task count. Used in comparing the synced file
     *
     * @return int
     */
    public function getProjectTaskCount(): int
    {
        $currentProjectGid = $this->getSettings()->getCurrentProject()['gid'];
        $taskCount = $this->asanaClient->projects->getTaskCountsForProject(
            $currentProjectGid,
            [
                'opt_fields' => 'num_tasks',
            ]
        );

        /** @var int $count */
        $count = $taskCount->num_tasks;

        $projectCache = $this->getSettings()->getProjectCache();
        if ( ! key_exists($currentProjectGid, $projectCache)) {
            $projectCache[$currentProjectGid] = [];
        }
        $projectCache[$currentProjectGid]['task_count'] = $count;
        $this->getSettings()->setProjectCache($projectCache);

        return $count;
    }

    /**
     * Check if the task count is the same as in our cache
     *
     * @return bool
     */
    public function isTaskCacheComplete(int $syncCount): bool
    {
        if (count($this->getSettings()->getTasks()) !== $syncCount) {
            return false;
        }

        return true;
    }

    /**
     * @param int $syncCount
     * @return array
     */
    public function syncTasks(int $syncCount): array
    {
        $tasks = [];
        $offset = null;
        $currentProjectGid = $this->getSettings()->getCurrentProject()['gid'];
        $paginationCycles = ceil($syncCount / self::DEFAULT_LIMIT);

        for ($i = 0; $i < $paginationCycles; $i++) {
            $args = [
                'limit'           => self::DEFAULT_LIMIT,
                'opt_fields'      => implode(
                    ',',
                    [
                        'gid',
                        'resource_type',
                        'created_at',
                        'completed',
                        'modified_at',
                        'modified_at',
                        'name',
                        'notes',
                        'html_notes',
                        'projects',
                        'start_on',
                        'tags',
                        'workspace',
                        'assignee',
                        'assignee_status',
                        'parent',
                    ]
                ),
            ];

            if ($offset !== null) {
                $args['offset'] = $offset;
            }

            $tasksData = (array) $this->asanaClient->tasks->getTasksForProject(
                $currentProjectGid,
                $args,
                ['opt_pretty' => 'true']
            );

            if (key_exists('data', $tasksData) && !empty($tasksData['data'])) {
                $tasks = array_merge($tasks, $tasksData['data']);
            }

            $offset = null;
            if (key_exists('next_page', $tasksData) && !empty($tasksData['next_page'])) {
                $offset = $tasksData['next_page']->offset;
            }
        }

        $this->getSettings()->setTasks($tasks);

        return [];
    }

    public function createTag(string $tag, array $task)
    {
        $existingTags = $this->asanaClient->tags->getTagsForTask($task['gid']);

        $tagResponse = $this->asanaClient->tags->createTag([
            'color' => 'light-green',
            'name' => $tag,
            'workspace' => $task['workspace']['gid'],
        ]);

        $this->asanaClient->tasks->addTagForTask($task['gid'], ['tag' => $tagResponse->gid]);
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
                'limit'      => self::DEFAULT_LIMIT,
                'workspace'  => $workspaceGid,
            ],
            [
                'iterator_type' => false,
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
                'limit'      => self::DEFAULT_LIMIT,
                'offset'     => self::DEFAULT_OFFSET,
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
                ),
            ]
        );

        echo var_dump($projects);
        return [];
    }

    public function getSettings()
    {
        return $this->settings;
    }

    private function fetchAuthToken(): string
    {
        $token = '';
        $file = sprintf('%s/%s/auth.json', __DIR__, self::CACHE_PATH);
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $this->accessToken = $data['token'];
            $token = $this->accessToken;
        }

        return $token;
    }

    private function writeAuthToken(string $token)
    {
        $file = sprintf('%s/%s/auth.json', __DIR__, self::CACHE_PATH);
        file_put_contents($file, json_encode(['token' => $token]));
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