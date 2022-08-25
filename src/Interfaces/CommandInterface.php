<?php

namespace Rvdlee\AsanaCLI\Interfaces;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

interface CommandInterface
{
    public const COMMAND = '';
    public const HELP = '';

    public function __construct(CLI $cli, Options &$options);

    public function run();
}