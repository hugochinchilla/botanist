<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

use Composer\IO\NullIO;
use Composer\Util\ProcessExecutor;

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
        $executor = new ProcessExecutor();
        $executor->execute("chown -R {$this->uid}:{$this->gid} {$path}");
    }

    public static function isSupported(): bool
    {
        $executor = new ProcessExecutor(new NullIO());
        $status = $executor->execute('which chown');
        return $status === 0;
    }
}