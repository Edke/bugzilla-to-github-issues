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



        "





        echo 'initing';
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


