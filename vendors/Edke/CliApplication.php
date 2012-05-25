<?php

use Nette\Diagnostics\Debugger;

/**
 * CLI Application
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @phpversion 5.3
 */

class CliApplication
{
    private $options = array();
    private $arguments;
    private $commands = array();

    private $verbose = false;
    private $version;
    private $copyright;
    private $legal;
    private $description;

    public function __construct()
    {
        # verbose
        $this->addOption('v', 'verbose', function($self)
            {
                $self->setVerbose(true);
            }, 'explain what is being done'
        );

        # help
        $this->addOption('h', 'help', function($self)
        {
            $self->help();
        }, 'show this help, then exit');

        # version
        $this->addOption(null, 'version', function($self)
        {
            $self->version();
        }, 'output version information, then exit');

    }

    /**
     * @param $short
     * @param null $long
     * @param $callback
     * @param $description
     */
    public function addOption($short, $long = null, $callback, $description)
    {
        $this->options[$short . $long] = (object)array(
            'short'       => $short,
            'long'        => $long,
            'callback'    => $callback,
            'description' => $description
        );
    }

    /**
     * @param $command
     * @param $callback
     * @param $description
     */
    public function addCommand($command, $callback, $description)
    {
        $this->commands[$command] = (object)array(
            'command'       => $command,
            'callback'    => $callback,
            'description' => $description
        );
    }

    /**
     * @param $arguments
     */
    public function run($arguments)
    {
        $_this = $this;
        $this->arguments = $arguments;

        unset($arguments[0]);
        Debugger::dump($arguments);

        # building and getting options
        $short = '';
        $long = array();
        foreach ($this->options as $key => $option) {
            if ($option->short) {
                $short .= $option->short;
            }

            if ($option->long) {
                $long[] = $option->long;
            }
        }
        $options = getopt($short, $long);

        # process options
        foreach ($options as $option => $valueIgnored) {
            foreach ($this->options as $key => $_option) {
                if ($option == $_option->short or $option == $_option->long) {
                    call_user_func($_option->callback, $_this);
                }
            }
        }

        # process commands
        $command = null;
        foreach($arguments as $argument) {
            if ( !preg_match('#^-#', $argument)) {
               $command = $argument;
                break;
            }
        }

        if ($command === null) {
            $this->help();
        }

        if (array_key_exists($command, $this->commands)) {
            call_user_func($this->commands[$command]->callback, $_this);
        }
        else {
            echo "Invalid command $command\n\n";
            $this->help();
        }

        //Debugger::dump($options);
    }

    /**
     *
     */
    public function setVerbose()
    {
        $this->verbose = true;
    }


    /**
     * @return string
     */
    public function getScriptName()
    {
        return basename($this->arguments[0]);
    }

    /**
     *
     */
    public function help()
    {
        $self = $this->getScriptName();
        $help = "$this->description

Usage:
  $self [OPTION]... COMMAND CONFIG

";

        # Commands
        $lines = array();
        $max = 0;
        foreach ($this->commands as $key => $command) {
                $lines[$key] = $key;
            $max = max($max, strlen($lines[$key]));
        }
        $help .="Commands:\n";
        foreach ($lines as $key => $line) {
            $help .= sprintf("  %s%s   %s\n", $line, str_repeat(' ', $max - strlen($line)), $this->commands[$key]->description);
        }

        # Options
        $lines = array();
        $max = 0;
        foreach ($this->options as $key => $option) {
            $lines[$key] = '';
            if ($option->short) {
                $lines[$key] = sprintf('-%s', $option->short);
            }
            if ($option->long) {
                $lines[$key] .= sprintf('%s--%s', (empty($lines[$key]) ? '' : ', '), $option->long);
            }
            $max = max($max, strlen($lines[$key]));
        }
        $help .="\nOptions:\n";
        foreach ($lines as $key => $line) {
            $help .= sprintf("  %s%s   %s\n", $line, str_repeat(' ', $max - strlen($line)), $this->options[$key]->description);
        }

        echo $help;
        exit;
    }

    /**
     * Prints version
     */
    public function version()
    {
        echo $this->getScriptName()." $this->version

$this->copyright

$this->legal
";
        exit;
    }

    /**
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @param $legal
     */
    public function setLegal($legal)
    {
        $this->legal = $legal;
    }

    /**
     * @param $copyright
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
