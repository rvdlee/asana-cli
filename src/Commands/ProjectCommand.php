<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Options\SelectProjectOption;
use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class ProjectCommand extends AbstractCommand
{
    public const COMMAND = 'project';
    public const HELP = 'Anything related to projects are done here.';

    public const OPTION_TAG = SelectProjectOption::ARG_LONG;

    public const OPTIONS = [
        self::OPTION_TAG => SelectProjectOption::class
    ];

    public function run()
    {
        $selectProjectArg = $this->options->getOpt(SelectProjectOption::ARG_LONG);
        if ($selectProjectArg) {
            $projects = $this->asanaService->getSettings()->getProjects();

            if (count($projects) === 1) {
                $project = $projects[0];
                $this->printToConsole(sprintf(
                    'You only have one project in your workspace(s). Currently selected: %s',
                    $project['name'],
                ));

                return $this->asanaService->getSettings()->setCurrentProject($project);
            }

//            $response = $this->asanaService->getTasks(0);
//            $this->cli->info($response);
        }
    }
}