<?php

namespace Rvdlee\AsanaCLI\Arguments;

use Rvdlee\AsanaCLI\Commands\TaskCommand;
use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;

class ChangeStatusTaskArgument implements ArgumentInterface
{
    public const ARG = 'status';
    public const HELP = 'Changes status on a task.';
    public const REQUIRED = true;
    public const COMMAND = TaskCommand::COMMAND;
}