<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

use Symfony\Component\Filesystem\Filesystem;

class PhpFixer implements FixerInterface
{
    protected int $uid;
    protected int $gid;

    public function setOwner(int $uid, int $gid) {
        $this->uid = $uid;
        $this->gid = $gid;
    }

    public function fixPathRecursive($path)
    {
        $fileSystem = new Filesystem();
        $fileSystem->chown($path, $this->uid, true);
        $fileSystem->chgrp($path, $this->gid, true);
    }

    public static function isSupported(): bool
    {
        return true;
    }
}