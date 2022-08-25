<?php

namespace Rvdlee\AsanaCLI\Arguments;

use Rvdlee\AsanaCLI\Commands\TaskCommand;
use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;

class CreateTaskArgument implements ArgumentInterface
{
    public const ARG = 'create';
    public const HELP = 'Creates a task on the given project.';
    public const REQUIRED = true;
    public const COMMAND = TaskCommand::COMMAND;
}