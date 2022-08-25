<?php

namespace Rvdlee\AsanaCLI\Interfaces;

interface ArgumentInterface
{
    /**
     * Argument name
     * @var string
     */
    public const ARG = '';

    /**
     * Little helper to get the user going
     * @var string
     */
    public const HELP = '';

    /**
     * Is this argument required?
     * @var bool
     */
    public const REQUIRED = true;

    /**
     * The command this argument is linked to. It needs to be the command name registered
     * Example: task
     * @var string
     */
    public const COMMAND = '';
}