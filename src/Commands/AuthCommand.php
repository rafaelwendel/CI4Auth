<?php

namespace CI4Auth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;

/**
 * AuthCommand Class
 *
 * Provides a CLI Spark command to publish the CI4Auth configuration template
 * file into the application's Config directory.
 */
class AuthCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CI4Auth';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'auth:install';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Installs the CI4Auth module';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'auth:install';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write(CLI::color('Load CI4Auth Installation', 'green'));

        $source = realpath(__DIR__ . '/../Config');
        $destination = APPPATH . 'Config';

        $publisher = new Publisher($source, $destination);
        $publisher->addFiles([$source . '/AuthConfig.php']);
        try {
            if ($publisher->copy(true)) {
                CLI::write(CLI::color('File created: APPPATH\Config\AuthConfig.php', 'green'));
                CLI::write(CLI::color('CI4Auth install finished.', 'green'));
            } else {
                CLI::error("Error: Failed to publish CI4Auth 'AuthConfig' file. Check the write permissions in the 'APPPATH/Config' folder.");
            }
        } catch (\Throwable $e) {
            CLI::error("Error when loading CI4Auth");
            CLI::write($e->getMessage(), 'red');
        }
    }
}
