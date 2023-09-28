<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

use Composer\IO\IOInterface;

class ActionLogger
{
    private IOInterface $io;

    public function __construct(IOInterface $io) {
        $this->io = $io;
    }

    public function write(string $msg)
    {
        $this->io->write("[stumpgrinder] <fg=green>{$msg}</>");
    }
}