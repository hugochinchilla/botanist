<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;


interface FixerInterface
{
    public function setOwner(int $uid, int $gui);

    public function fixPathRecursive(string $path);

    public static function isSupported(): bool;
}