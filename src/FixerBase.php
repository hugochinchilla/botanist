<?php

declare(strict_types = 1);

namespace hugochinchilla\stumpgrinder;

abstract class FixerBase implements FixerInterface
{
    protected int $uid;
    protected int $gid;
    protected ActionLogger $actionLogger;

    public function __construct(int $uid, int $gid, ActionLogger $actionLogger) {
        $this->uid = $uid;
        $this->gid = $gid;
        $this->actionLogger = $actionLogger;
    }
}