<?php
namespace hugochinchilla\stumpgrinder;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private ActionLogger $actionLogger;
    private string $vendorPath;
    private int $runCount = 0;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->vendorPath = $composer->getConfig()->get("vendor-dir");
        $this->actionLogger = new ActionLogger($io);
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

        $this->setPathOwnershipFromParentPath($this->vendorPath);
        $this->setPathOwnershipFromParentPath('composer.lock');
    }

    private function setPathOwnershipFromParentPath($path)
    {
        $parentPath = dirname(realpath($path));
        $uid = fileowner($parentPath);
        $gid = filegroup($parentPath);

        $fixer = $this->createFixer($uid, $gid);

        if (is_file($path)) {
            $fixer->fixFile($path);
        } else {
            $fixer->fixDirectoryRecursive($path);
        }
    }

    private function createFixer(bool $uid, bool $gid): FixerInterface
    {
        if (CoreUtilsFixer::isSupported()) {
            return new CoreUtilsFixer($uid, $gid, $this->actionLogger);
        }

        return new PhpFixer($uid, $gid, $this->actionLogger);
    }
}