<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

use Symfony\Component\Filesystem\Filesystem;

class PhpFixer extends FixerBase implements FixerInterface
{
    public function fixFile($path)
    {
        if ($this->uid === fileowner($path) && $this->gid === filegroup($path)) {
            return;
        }

        $fileSystem = new Filesystem();
        $fileSystem->chown($path, $this->uid);
        $fileSystem->chgrp($path, $this->gid);
        $this->actionLogger->write("changed owner of {$path}");
    }

    public function fixDirectoryRecursive($path)
    {
        $fileSystem = new Filesystem();
        $fileSystem->chown($path, $this->uid, true);
        $fileSystem->chgrp($path, $this->gid, true);
        $this->actionLogger->write("changed owner of {$path}");
    }

    public static function isSupported(): bool
    {
        return true;
    }
}