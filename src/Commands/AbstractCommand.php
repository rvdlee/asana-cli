<?php

namespace Rvdlee\AsanaCLI\Commands;

use DateTime;
use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;
use Rvdlee\AsanaCLI\Interfaces\CommandInterface;
use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

abstract class AbstractCommand implements CommandInterface
{
    public const ARGS = [];
    public const OPTIONS = [];

    /**
     * CLI instance
     * @var CLI
     */
    protected CLI $cli;

    /**
     * CLI Options
     * @var Options
     */
    protected Options $options;

    /**
     * @var array
     */
    protected array $userInput;

    /**
     * The instanced classname
     * @var AbstractCommand|string
     */
    protected string $class;

    /**
     * @var AsanaService
     */
    protected AsanaService $asanaService;

    public function __construct(CLI $cli, Options &$options, AsanaService $asanaService)
    {
        $this->cli = $cli;
        $this->class = get_class($this);
        $this->options = $options;
        $this->asanaService = $asanaService;

        if (defined(sprintf('%s::ARGS', $this->class)) && $this->class::ARGS !== []) {
            /** @var ArgumentInterface $argument */
            foreach ($this->class::ARGS as $argument) {
                $this->options->registerArgument(
                    $argument::ARG,
                    $argument::HELP,
                    $argument::REQUIRED,
                    $argument::COMMAND
                );
            }
        }

        if (defined(sprintf('%s::OPTIONS', $this->class)) && $this->class::OPTIONS !== []) {
            /** @var ArgumentInterface $argument */
            foreach ($this->class::OPTIONS as $argument) {
                $this->options->registerOption(
                    $argument::ARG_LONG,
                    $argument::HELP,
                    $argument::ARG_SHORT,
                    $argument::NEEDS_ARGS,
                    $argument::COMMAND
                );
            }
        }
    }

    public function printToConsole(string $message): void
    {
        echo sprintf(
            '[%s] %s%s',
            (new DateTime())->format('Y-m-d H:i:s'),
            $message,
            "\r\n"
        );
    }

    public function readUserInput(string $name, string $message): string
    {
        echo sprintf('%s:', $message);
        $input = fopen("php://stdin","r");
        $this->userInput[$name] = $input;
        return fgets($input);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getUserInput(string $name)
    {
        return key_exists($name, $this->userInput) ? $this->userInput[$name] : null;
    }
}