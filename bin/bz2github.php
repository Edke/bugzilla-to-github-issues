#!/usr/bin/php
<?php

require __DIR__ . '/../vendors/Nette/nette.min.php';
require __DIR__ . '/../vendors/Edke/CliApplication.php';

use Nette\Diagnostics\Debugger;

/**
 * Bugzilla to Github Issues migration tool
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @phpversion 5.3
 */

class bzMigrator extends CliApplication
{

    public function run($arguments)
    {
        parent::run($arguments);
        $_this = $this;

        //Debugger::dump($this->arguments);

        if (count($this->arguments) != 3 ) {
            $this->help();
        }

        $command = $this->getCommand();
        $config = $this->getConfig();

        if (array_key_exists($command, $this->commands)) {
            call_user_func($this->commands[$command]->callback, $_this);
        } else {
            echo "Invalid command $command\n\n";
            $this->help();
        }
    }


    public function getCommand()
    {
        return $this->arguments[1];
    }

    public function getConfig()
    {
        return $this->arguments[2];
    }

}

$migrator = new bzMigrator();
$migrator->setVersion('0.1');
$migrator->setCopyright('Copyright 2012 Eduard Kracmar <eduard.kracmar@gmail.com>.');
$migrator->setLegal('This program is free software.  You may modify or distribute it
under the terms of GNU GPL version 3 or later <http://gnu.org/licenses/gpl.html>.');
$migrator->setDescription('bz2github migrates Bugzilla bugs to Github issues.');

$migrator->addCommand('init-config', function($migrator)
    {
        $template = "
bugzilla:
    url:
    host:
    login:
    password:
    product:
    last-bug:

github:
    login:
    password:
    repository:
";
        $file = getcwd().'/'.$migrator->getConfig().'.neon';
        if (file_exists($file)) {
            echo "File $file already exists, terminating.\n";
            exit;

        }

        file_put_contents($file, $template);



        echo "File $file saved.\n";
        exit;
    }, 'creates config file template in current directory'
);

$migrator->addCommand('list-bugs', function($migrator)
    {
        echo 'initing';
        exit;
    }, 'lists bugs according to config'
);

$migrator->run($argv);


