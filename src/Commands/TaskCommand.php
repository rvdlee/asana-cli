<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Options\TagTaskOption;
use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class TaskCommand extends AbstractCommand
{
    public const COMMAND = 'task';
    public const HELP = 'Anything related to tasks are done here.';

    public const OPTION_TAG = TagTaskOption::COMMAND;

    public const OPTIONS = [
        self::OPTION_TAG => TagTaskOption::class
    ];

    /**
     * @var AsanaService
     */
    protected AsanaService $asanaService;

    public function __construct(CLI $cli, Options &$options)
    {
        $this->asanaService = new AsanaService();

        parent::__construct($cli, $options);
    }

    public function run()
    {
        $tagTasks = $this->options->getOpt(TagTaskOption::ARG_LONG);
        if ($tagTasks) {
            $response = $this->asanaService->getTasks(0);
            $this->cli->info($response);
        }
    }
}