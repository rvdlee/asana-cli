#!/usr/bin/php
<?php
require __DIR__ . '/vendor/autoload.php';

use Rvdlee\AsanaCLI\Commands\LoginCommand;
use Rvdlee\AsanaCLI\Commands\ProjectCommand;
use Rvdlee\AsanaCLI\Commands\TaskCommand;
use Rvdlee\AsanaCLI\Commands\VersionCommand;
use Rvdlee\AsanaCLI\Interfaces\CommandInterface;
use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

class AsanaCLI extends CLI
{
    public const TASK_COMMAND = TaskCommand::class;
    public const VERSION_COMMAND = VersionCommand::class;
    public const LOGIN_COMMAND = LoginCommand::class;
    public const PROJECT_COMMAND = ProjectCommand::class;

    public const COMMANDS = [
        self::TASK_COMMAND,
        self::VERSION_COMMAND,
        self::LOGIN_COMMAND,
        self::PROJECT_COMMAND,
    ];

    protected $logdefault = 'debug';

    /**
     * @var CommandInterface[]|array
     */
    protected $commands = [];

    /**
     * @var AsanaService
     */
    protected AsanaService $asanaService;

    public function __construct($autocatch = true)
    {
        $this->asanaService = new AsanaService();

        parent::__construct($autocatch);
    }

    protected function setup(Options $options)
    {
        $options->setHelp('A CLI interface for developers using Asana.');

        /** @var CommandInterface $fqcn */
        foreach (self::COMMANDS as $fqcn) {
            $options->registerCommand($fqcn::COMMAND, $fqcn::HELP);
            $this->commands[$fqcn] = new $fqcn($this, $options, $this->asanaService);
        }
    }

    protected function main(Options $options)
    {
        $command = $options->getCmd();

        /** @var CommandInterface $fqcn */
        foreach (self::COMMANDS as $fqcn) {
            if ($fqcn::COMMAND === $command) {
                /** @var CommandInterface $cmd */
                $cmd = $this->commands[$fqcn];
                return $cmd->run();
            }
        }

        echo $options->help();
    }
}

(new AsanaCLI())->run();