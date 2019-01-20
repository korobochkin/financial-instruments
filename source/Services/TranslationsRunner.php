<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Korobochkin\WPKit\Translations\PluginTranslations;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TranslationsRunner implements RunnerInterface
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
         * @var $translations PluginTranslations
         */
        $translations = self::getContainer()->get('wp.plugins.financial_instruments.translations');
        $translations->loadTranslations();
    }
}
