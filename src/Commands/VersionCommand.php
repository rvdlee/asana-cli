<?php

namespace Rvdlee\AsanaCLI\Commands;

use Rvdlee\AsanaCLI\Service\AsanaService;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class VersionCommand extends AbstractCommand
{
    public const COMMAND = 'version';
    public const HELP = 'Print the version of Asana CLI.';

    /**
     * The version fetched from the composer.json file
     * @var string
     */
    protected string $version;

    public function __construct(CLI $cli, Options &$options, AsanaService $asanaService)
    {
        parent::__construct($cli, $options, $asanaService);

        $filename = __DIR__ . '/../../composer.json';
        $composer = json_decode(file_get_contents($filename), true);

        $this->version = $composer['version'];
    }

    public function run()
    {
        $this->cli->debug(sprintf('Asana CLI %s (cli)', $this->version));
        $this->cli->debug('Copyright (c) Rob van der Lee');
    }
}