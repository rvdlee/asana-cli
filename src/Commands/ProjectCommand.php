<?php

namespace Rvdlee\AsanaCLI\Commands;

class ProjectCommand extends AbstractCommand
{
    public const COMMAND = 'project';
    public const HELP = 'Anything related to projects are done here.';

    public const ARGS = [];

    public function run()
    {
        echo $this->options->help();
    }
}