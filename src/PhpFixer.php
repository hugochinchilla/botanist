<?php

declare(strict_types = 1);

namespace hugochinchilla\botanist;

use Symfony\Component\Filesystem\Filesystem;

class PhpFixer implements FixerInterface
{
    protected $uid;
    protected $gid;

    public function setOwner(int $uid, int $gid) {
        $this->uid = $uid;
        $this->gid = $gid;
    }

    public function fixPathRecursive(string $path)
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
