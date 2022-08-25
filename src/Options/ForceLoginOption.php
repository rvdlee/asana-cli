<?php

namespace Rvdlee\AsanaCLI\Options;

use Rvdlee\AsanaCLI\Commands\LoginCommand;
use Rvdlee\AsanaCLI\Interfaces\OptionInterface;

class ForceLoginOption implements OptionInterface
{
    public const ARG_LONG = 'force';
    public const HELP = 'Forcing to login and refresh data retrieved.';
    public const ARG_SHORT = 'f';
    public const NEEDS_ARGS = false;
    public const COMMAND = LoginCommand::COMMAND;
}