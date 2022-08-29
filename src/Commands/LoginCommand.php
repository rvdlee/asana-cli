<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Options\ForceLoginOption;
use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class LoginCommand extends AbstractCommand
{
    public const COMMAND = 'login';
    public const HELP = 'Create a auth token for Asana CLI to work with.';

    public const OPTION_FORCE = ForceLoginOption::ARG_LONG;

    public const OPTIONS = [
        self::OPTION_FORCE => ForceLoginOption::class
    ];

    public function run()
    {
        $force = $this->options->getOpt(ForceLoginOption::ARG_LONG);
        if ($this->asanaService->hasToken() === false || $force) {
            $this->cli->info($this->asanaService->login($force));
        }
    }
}