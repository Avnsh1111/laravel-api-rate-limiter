<?php

namespace Avnsh1111\LaravelApiRateLimiter;

use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;

class YourPluginClass implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public static function getSubscribedEvents()
    {
        return [
            PluginEvents::INIT => 'onInit',
        ];
    }

    public function onInit()
    {
        // Your plugin's initialization code here
    }

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
}
