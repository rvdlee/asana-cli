<?php

namespace Rvdlee\AsanaCLI\Interfaces;

use Rvdlee\AsanaCLI\Commands\LoginCommand;

interface OptionInterface
{
    public const ARG_LONG = '';
    public const HELP = '';
    public const ARG_SHORT = '';
    public const NEEDS_ARGS = false;
    public const COMMAND = '';
}