<?php
namespace Korobochkin\FinancialInstruments\Admin\Cron;

use Korobochkin\WPKit\Cron\CronEventInterface;
use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class CronEventsRunner implements RunnerInterface
{
    /**
     * @var ContainerInterface Container with services.
     */
    protected static $container;

    /**
     * Returns the ContainerBuilder with services.
     *
     * @return ContainerInterface Container with services.
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Sets the ContainerBuilder with services.
     *
     * @param ContainerInterface $container Container with services.
     */
    public static function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function run()
    {
        $events = self::getContainer()->getParameter('wp.plugins.financial_instruments.cron_events');
        foreach ($events as $eventReference) {
            /**
             * @var $eventReference Reference
             * @var $event CronEventInterface
             */
            $event = self::getContainer()->get($eventReference);
            add_action($event->getName(), array($event, $event->getHook()));
        }
    }
}
