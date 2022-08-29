<?php

namespace Rvdlee\AsanaCLI\Options;

use Rvdlee\AsanaCLI\Commands\ProjectCommand;
use Rvdlee\AsanaCLI\Interfaces\OptionInterface;

class SelectProjectOption implements OptionInterface
{
    public const ARG_LONG = 'select';
    public const HELP = 'Select current project to perform tasks on.';
    public const ARG_SHORT = 's';
    public const NEEDS_ARGS = false;
    public const COMMAND = ProjectCommand::COMMAND;
}