<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

class CoreUtilsFixer implements FixerInterface
{
    protected int $uid;
    protected int $gid;

    public function setOwner(int $uid, int $gid) {
        $this->uid = $uid;
        $this->gid = $gid;
    }

    public function fixPathRecursive($path)
    {
        exec("chown -R {$this->uid}:{$this->gid} {$path}");
    }

    public static function isSupported(): bool
    {
        return exec('which chown') !== false;
    }
}