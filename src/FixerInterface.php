<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;


interface FixerInterface
{
    public function fixFile($path);

    public function fixDirectoryRecursive($path);

    public static function isSupported(): bool;
}