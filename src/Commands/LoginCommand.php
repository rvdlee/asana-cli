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

    public const OPTION_FORCE = ForceLoginOption::COMMAND;

    public const OPTIONS = [
        self::OPTION_FORCE => ForceLoginOption::class
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
        $force = $this->options->getOpt(ForceLoginOption::ARG_LONG);
        if ($this->asanaService->hasUser() === false || $force) {
            $this->cli->info($this->asanaService->login($force));
        }
    }
}