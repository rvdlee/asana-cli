<?php

namespace Rvdlee\AsanaCLI\Arguments;

use Rvdlee\AsanaCLI\Commands\TaskCommand;
use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;

class TagTaskArgument implements ArgumentInterface
{
    public const ARG = 'tag';
    public const HELP = 'Tags your project with short reference tags (requires ./asana-cli project configure)';
    public const REQUIRED = true;
    public const COMMAND = TaskCommand::COMMAND;
}