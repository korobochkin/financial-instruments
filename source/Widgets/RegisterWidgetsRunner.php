<?php
namespace Korobochkin\FinancialInstruments\Widgets;

use Korobochkin\FinancialInstruments\Widgets\CurrencyTable\CurrencyTableWidget;
use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegisterWidgetsRunner implements RunnerInterface
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
        register_widget(self::getContainer()->get(CurrencyTableWidget::class));
    }
}
