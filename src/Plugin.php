<?php
namespace hugochinchilla\stumpgrinder;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Filesystem\Filesystem;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private IOInterface $io;
    private Composer $composer;
    private int $runCount = 0;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            'post-install-cmd' => 'fixOwnershipOnce',
            'post-update-cmd' => 'fixOwnershipOnce',
            'post-autoload-dump' => 'fixOwnershipOnce',
        );
    }

    public function fixOwnershipOnce($path)
    {
        if ($this->runCount === 0) {
            $this->fixOwnership($path);
            $this->runCount++;
        }
    }

    private function fixOwnership($path)
    {
        $isRoot = posix_getuid() === 0;
        if (!$isRoot) {
            return;
        }

        $vendorPath = $this->composer->getConfig()->get("vendor-dir");
        $this->setPathOwnershipFromParentPath($vendorPath);
        $this->setPathOwnershipFromParentPath('composer.lock');
    }

    private function setPathOwnershipFromParentPath($path)
    {
        $parentPath = dirname(realpath($path));
        $uid = fileowner($parentPath);
        $gid = filegroup($parentPath);

        if (!$this->fileNeedsFixing($path, $uid, $gid)) {
            return;
        }

        $fileSystem = new Filesystem();
        $fileSystem->chown($path, $uid, true);
        $fileSystem->chgrp($path, $gid, true);
        $this->logActionTaken($path, $uid, $gid);
    }

    private function fileNeedsFixing($path, $uid, $gid)
    {
        if (is_dir($path)) {
            return true;
        }

        return $uid !== fileowner($path) && $gid !== filegroup($path);
    }

    private function log(string $msg)
    {
        $this->io->write("[stumpgrinder] <fg=green>{$msg}</>");
    }

    private function logActionTaken($path, $uid, $gid): void
    {
        if (is_dir($path)) {
            $relativePath = mb_substr($path, mb_strlen(getcwd()) + 1);
        } else {
            $relativePath = $path;
        }
        $this->log("set {$relativePath} ownership to {$uid}:{$gid}");
    }
}