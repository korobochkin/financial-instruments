<?php
namespace Korobochkin\FinancialInstruments\Admin\Services;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminScriptStylesRunner implements RunnerInterface
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
        /**
         * @var $scriptStyles AdminScriptStyles
         */
        $scriptStyles = self::getContainer()->get(AdminScriptStyles::class);
        $scriptStyles->register();
    }

    /**
     * @param $hook string Current WordPress admin page slug (file name).
     */
    public static function enqueue($hook)
    {
        /**
         * @var $scriptStyles AdminScriptStyles
         */
        $scriptStyles = self::getContainer()->get(AdminScriptStyles::class);
        $scriptStyles->enqueue($hook);
    }

    /**
     * For WordPress customizer page.
     */
    public static function enqueueCustomizer()
    {
        /**
         * @var $scriptStyles AdminScriptStyles
         */
        $scriptStyles = self::getContainer()->get(AdminScriptStyles::class);
        $scriptStyles->enqueueCustomizer();
    }
}
