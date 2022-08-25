<?php

namespace Rvdlee\AsanaCLI\Options;

use Rvdlee\AsanaCLI\Commands\TaskCommand;
use Rvdlee\AsanaCLI\Interfaces\OptionInterface;

class TagTaskOption implements OptionInterface
{
    public const ARG_LONG = 'tag';
    public const HELP = 'Tags your project with short reference tags (requires ./asana-cli project configure)';
    public const ARG_SHORT = 't';
    public const NEEDS_ARGS = false;
    public const COMMAND = TaskCommand::COMMAND;
}