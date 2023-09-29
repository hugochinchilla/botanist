<?php

use Composer\Util\ProcessExecutor;

class ComposerInstallTest extends \PHPUnit\Framework\TestCase
{
    const PROJECT_PATH = __DIR__ . '/resources/example_project';

    public static function setUpBeforeClass(): void
    {
        self::runCommand('rm -rf vendor composer.lock');
    }

    public function test_testsuite_is_not_run_as_root(): void
    {
        $current_user = posix_getuid();

        $this->assertNotEquals($current_user, 0);
    }

    public function test_composer_install_creates_root_directory(): void
    {
        self::runCommand('docker compose run composer install');

        $this->assertEquals(self::getOwner('vendor'), '0:0');
    }

    /**
     * @depends test_composer_install_creates_root_directory
     */
    public function test_install_plugins_removes_root_dirs(): void
    {
        $current_user = posix_getuid();

        self::runCommand('docker compose run composer require hugochinchilla/stumpgrinder @dev');

        $this->assertEquals(self::getOwner('vendor'), "{$current_user}:{$current_user}");
    }

    public static function tearDownAfterClass(): void
    {
        self::runCommand('git checkout composer.json');
    }

    private static function runCommand(string $command): void
    {
        $executor = new ProcessExecutor();
        $result = $executor->execute($command, $output, self::PROJECT_PATH);
        if ($result !== 0) {
            $error = $executor->getErrorOutput();
            throw new Exception("failed command: $command\n\n$error");
        }
    }

    private static function getOwner(string $path): string
    {
        $abs_path = realpath(self::PROJECT_PATH . '/'. $path);
        return fileowner($abs_path) . ':' . filegroup($abs_path);
    }
}