<?php

declare(strict_types = 1);

namespace hugochinchilla\botanist;


interface FixerInterface
{
    public function setOwner(int $uid, int $gid);

    public function fixPathRecursive(string $path);

    public static function isSupported(): bool;
}
