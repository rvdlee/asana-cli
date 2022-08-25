<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;
use Rvdlee\AsanaCLI\Interfaces\CommandInterface;
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
     * The instanced classname
     * @var AbstractCommand|string
     */
    protected string $class;

    public function __construct(CLI $cli, Options &$options)
    {
        $this->cli = $cli;
        $this->class = get_class($this);
        $this->options = $options;

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
}