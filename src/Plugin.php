<?php

namespace hugochinchilla\botanist;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private IOInterface $io;
    private string $vendorPath;
    private int $runCount = 0;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->vendorPath = $composer->getConfig()->get("vendor-dir");
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

    public function fixOwnershipOnce()
    {
        if ($this->runCount === 0) {
            $this->fixOwnership();
            $this->runCount++;
        }
    }

    private function fixOwnership()
    {
        $isRoot = posix_getuid() === 0;
        if (!$isRoot) {
            return;
        }

        $fixer = $this->createFixer();
        $this->setOwnerFromParentPath($fixer, $this->vendorPath);
        $this->setOwnerFromParentPath($fixer, 'composer.lock');
    }

    private function setOwnerFromParentPath(FixerInterface $fixer, string $path)
    {
        $ownership = new Ownership($path);
        if (!$ownership->needsUpdate()) {
            return;
        }
        $fixer->setOwner($ownership->newUid, $ownership->newGid);
        $fixer->fixPathRecursive($path);
        $this->io->write("[botanist] <fg=green>changed owner of {$path}</>");
    }

    private function createFixer(): FixerInterface
    {
        if (CoreUtilsFixer::isSupported()) {
            return new CoreUtilsFixer();
        }

        $this->io->write("[botanist] <fg=yellow>coreutils not available, falling back to php, this may be slower</>");
        return new PhpFixer();
    }
}
