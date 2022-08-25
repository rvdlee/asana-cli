<?php

namespace Rvdlee\AsanaCLI\Arguments;

use Rvdlee\AsanaCLI\Commands\ProjectCommand;
use Rvdlee\AsanaCLI\Interfaces\ArgumentInterface;

class ConfigureProjectArgument implements ArgumentInterface
{
    public const ARG = 'configure';
    public const HELP = 'Configures a project to prepare for tagging.';
    public const REQUIRED = true;
    public const COMMAND = ProjectCommand::COMMAND;
}