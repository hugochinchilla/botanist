<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

class CoreUtilsFixer extends FixerBase implements FixerInterface
{
    public function fixFile($path)
    {
        if ($this->uid === fileowner($path) && $this->gid === filegroup($path)) {
            return;
        }

        exec("chown {$this->uid}:{$this->gid} {$path}");
        $this->actionLogger->write("changed owner of {$path}");
    }

    public function fixDirectoryRecursive($path)
    {
        exec("chown -R {$this->uid}:{$this->gid} {$path}");
        $this->actionLogger->write("changed owner of {$path}");
    }

    public static function isSupported(): bool
    {
        return exec('which chown') !== false;
    }
}