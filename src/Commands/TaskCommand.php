<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Options\TagTaskOption;

class TaskCommand extends AbstractCommand
{
    public const COMMAND = 'task';
    public const HELP = 'Anything related to tasks are done here.';

    public const OPTION_TAG = TagTaskOption::ARG_LONG;

    public const OPTIONS = [
        self::OPTION_TAG => TagTaskOption::class
    ];

    /**
     * @var null|array
     */
    protected $sortedTasks = null;

    public function run()
    {
        $tagTasks = $this->options->getOpt(TagTaskOption::ARG_LONG);
        if ($tagTasks) {
            $currentProject = $this->asanaService->getSettings()->getCurrentProject();
            $this->printToConsole(sprintf('Fetching task count for %s.', $currentProject['name']));

            $taskCount = $this->asanaService->getProjectTaskCount();
            $this->printToConsole(sprintf('%s has %d tasks.', $currentProject['name'], $taskCount));
            if ($this->asanaService->isTaskCacheComplete($taskCount) === false) {
                $this->asanaService->syncTasks($taskCount);
            }

            $tasks = $this->getSortedTasks();
            [$tagNumber, $lastGid] = $this->asanaService->getLastShortReference();
            foreach ($tasks as $date => $task) {
                $tagNumber++;
                $tag = sprintf('%s-%d', $this->asanaService->getSettings()->getProjectShortReference(), $tagNumber);
                $this->asanaService->createTag($tag, $task);
            }

            $this->cli->info('done');
        }
    }

    /**
     * @return array
     */
    protected function getSortedTasks(): array
    {
        if ($this->sortedTasks === null) {
            $tasks = $this->keyBy('created_at', $this->asanaService->getSettings()->getTasks());
            krsort($tasks);
            $this->sortedTasks = $tasks;
        }

        return $this->sortedTasks;
    }

    /**
     * Keys the array by given key
     * @param string $key
     * @param array  $array
     * @return array
     */
    protected function keyBy(string $key, array $array): array
    {
        $data = [];

        foreach ($array as $index => $value) {
            if (key_exists($key, $value)) {
                $data[$value[$key]] = $value;
            }
        }

        return $data;
    }
}