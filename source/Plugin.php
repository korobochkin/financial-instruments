<?php
namespace Korobochkin\FinancialInstruments;

use Korobochkin\FinancialInstruments\Admin\Cron\CronEventsRunner;
use Korobochkin\FinancialInstruments\Admin\Cron\UpdateRatesCronEvent;
use Korobochkin\FinancialInstruments\Admin\Notices\NoticesStackRunner;
use Korobochkin\FinancialInstruments\Admin\Pages\AdminPages;
use Korobochkin\FinancialInstruments\Admin\Pages\AdminPagesFormFactory;
use Korobochkin\FinancialInstruments\Admin\Pages\AdminPagesRunner;
use Korobochkin\FinancialInstruments\Admin\Pages\Plugins;
use Korobochkin\FinancialInstruments\Admin\Pages\PluginsRunner;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\PrimarySettingsPage;
use Korobochkin\FinancialInstruments\Admin\Pages\TwigFactory;
use Korobochkin\FinancialInstruments\Admin\Services\AdminScriptStyles;
use Korobochkin\FinancialInstruments\Admin\Services\AdminScriptStylesRunner;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerAPIKeyOption;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerBaseCurrencyOption;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerSymbolsOption;
use Korobochkin\FinancialInstruments\Options\RatesOption;
use Korobochkin\FinancialInstruments\Options\TimeFrameOption;
use Korobochkin\FinancialInstruments\Services\Activation;
use Korobochkin\FinancialInstruments\Services\ActivationRunner;
use Korobochkin\FinancialInstruments\Services\CurrencyExchange;
use Korobochkin\FinancialInstruments\Services\CurrencyExchangeFactory;
use Korobochkin\FinancialInstruments\Services\FixerAPI\FixerAPI;
use Korobochkin\FinancialInstruments\Services\FixerAPI\FixerAPIFactory;
use Korobochkin\FinancialInstruments\Services\WidgetCaption;
use Korobochkin\FinancialInstruments\Services\ScriptStyles;
use Korobochkin\FinancialInstruments\Services\ScriptStylesRunner;
use Korobochkin\FinancialInstruments\Services\TranslationsRunner;
use Korobochkin\FinancialInstruments\Services\UpdateRates\Rates;
use Korobochkin\FinancialInstruments\Widgets\CurrencyTable\CurrencyTableWidget;
use Korobochkin\FinancialInstruments\Widgets\RegisterWidgetsRunner;
use Korobochkin\WPKit\DataComponents\NodeFactory;
use Korobochkin\WPKit\Notices\NoticesStack;
use Korobochkin\WPKit\Plugins\AbstractPlugin;
use Korobochkin\WPKit\Translations\PluginTranslations;
use Korobochkin\WPKit\Utils\RequestFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

class Plugin extends AbstractPlugin
{

    const NAME = 'financial-instruments';

    const _NAME_ = 'financial_instruments';

    const VERSION = '1.0.0';

    /**
     * @inheritdoc
     */
    public function run()
    {
        ActivationRunner::setContainer($this->container);
        register_activation_hook($this->getFile(), array(ActivationRunner::class, 'run'));

        TranslationsRunner::setContainer($this->container);
        add_action('plugins_loaded', array(TranslationsRunner::class, 'run'));

        if (defined('DOING_CRON') && DOING_CRON) {
            CronEventsRunner::setContainer($this->container);
            add_action('init', array(CronEventsRunner::class, 'run'));
        }

        ScriptStylesRunner::setContainer($this->container);
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'run'));

        RegisterWidgetsRunner::setContainer($this->container);
        add_action('widgets_init', array(RegisterWidgetsRunner::class, 'run'));

        return $this;
    }

    /**
     * @return $this For chain calls.
     */
    public function runAdmin()
    {
        AdminPagesRunner::setContainer($this->container);
        add_action('admin_menu', array(AdminPagesRunner::class, 'run'));

        NoticesStackRunner::setContainer($this->container);
        add_action('admin_notices', array(NoticesStackRunner::class, 'run'));

        PluginsRunner::setContainer($this->container);
        add_filter('plugin_action_links_' . $this->getBasename(), array(PluginsRunner::class, 'addActionLinks'));

        AdminScriptStylesRunner::setContainer($this->container);
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'run'));
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'enqueue'));
        add_action('customize_controls_enqueue_scripts', array(AdminScriptStylesRunner::class, 'enqueueCustomizer'));
        add_action('customize_controls_enqueue_scripts', array(AdminScriptStylesRunner::class, 'enqueue'));

        return $this;
    }

    /**
     * Configure DI container.
     *
     * @param ContainerBuilder $container DI container.
     *
     * @return $this For chain calls.
     */
    public function configureDependencies(ContainerBuilder $container)
    {
        if (!$container->hasParameter('wp.plugins.financial_instruments.cache_dir')) {
            $container->setParameter(
                'wp.plugins.financial_instruments.cache_dir',
                false
            );
        }

        $container->setParameter(
            'wp.plugins.financial_instruments.flags.dir_url',
            $this->getUrl() . 'assets/flags/flags-iso/'
        );

        $container->setParameter(
            'wp.plugins.financial_instruments.flags.sizes',
            array(
                16 => '16 px',
                24 => '24 px',
                32 => '32 px',
                48 => '48 px',
                64 => '64 px',
            )
        );

        $container->setParameter(
            'wp.plugins.financial_instruments.templates_path',
            $this->getDir() . 'twig-templates'
        );

        $container->setParameter(
            'wp.plugins.financial_instruments.languages_path',
            dirname($this->getBasename()) . '/languages'
        );

        $container
            ->register('wp.plugins.financial_instruments.request', Request::class)
            ->setFactory(array(RequestFactory::class, 'create'))
            ->setLazy(true);

        $container
            ->register('wp.plugins.financial_instruments.twig')
            ->setFactory(array(TwigFactory::class, 'create'))
            ->addArgument('%wp.plugins.financial_instruments.cache_dir%')
            ->addArgument('%wp.plugins.financial_instruments.templates_path%')
            ->setLazy(true);

        $container
            ->register('wp.plugins.financial_instruments.validator')
            ->setFactory(array(Validation::class, 'createValidator'))
            ->setLazy(true);

        $container
            ->register('wp.plugins.financial_instruments.form_factory_for_factory', AdminPagesFormFactory::class)
            ->addArgument(new Reference('wp.plugins.financial_instruments.validator'));

        $container
            ->register('wp.plugins.financial_instruments.form_factory')
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.form_factory_for_factory'), 'create'))
            ->setLazy(true);

        $container
            ->register('wp.plugins.financial_instruments.translations', PluginTranslations::class)
            ->addArgument(self::NAME)
            ->addArgument('%wp.plugins.financial_instruments.languages_path%');

        $container
            ->register('wp.plugins.financial_instruments.fixer_api', FixerAPI::class)
            ->setFactory(array(FixerAPIFactory::class, 'create'))
            ->addArgument(new Reference(FixerAPIKeyOption::class))
            ->addArgument(new Reference(FixerBaseCurrencyOption::class));

        $container
            ->register(Rates::class, Rates::class)
            ->addArgument(new Reference('wp.plugins.financial_instruments.fixer_api'))
            ->addArgument(new Reference(FixerSymbolsOption::class))
            ->addArgument(new Reference(TimeFrameOption::class))
            ->addArgument(new Reference(RatesOption::class));

        $container
            ->register(Activation::class, Activation::class)
            ->addArgument(new Reference(UpdateRatesCronEvent::class));

        $container
            ->register(AdminPages::class, AdminPages::class)
            ->addArgument(new Reference('wp.plugins.financial_instruments.twig'))
            ->addArgument(new Reference('wp.plugins.financial_instruments.form_factory'))
            ->addArgument(array(
                new Reference(PrimarySettingsPage::class),
            ));

        $container
            ->register(Plugins::class, Plugins::class)
            ->addArgument(new Reference(PrimarySettingsPage::class));

        $container
            ->register(PrimarySettingsPage::class, PrimarySettingsPage::class)
            ->addMethodCall('setFixerAPIKeyOption', array(new Reference(FixerAPIKeyOption::class)))
            ->addMethodCall('setTimeFrameOption', array(new Reference(TimeFrameOption::class)))
            ->addMethodCall('setRequest', array(new Reference('wp.plugins.financial_instruments.request')))
            ->addMethodCall('setNoticesStack', array(new Reference('wp.plugins.financial_instruments.notices')));

        $container
            ->register('wp.plugins.financial_instruments.notices', NoticesStack::class);

        $container
            ->register('wp.plugins.financial_instruments.node_factory', NodeFactory::class)
            ->addArgument(new Reference('wp.plugins.financial_instruments.validator'));

        $container
            ->register(FixerAPIKeyOption::class, FixerAPIKeyOption::class)
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.node_factory'), 'create'))
            ->addArgument(FixerAPIKeyOption::class);

        $container
            ->register(FixerBaseCurrencyOption::class, FixerBaseCurrencyOption::class)
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.node_factory'), 'create'))
            ->addArgument(FixerBaseCurrencyOption::class);

        $container
            ->register(FixerSymbolsOption::class, FixerSymbolsOption::class)
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.node_factory'), 'create'))
            ->addArgument(FixerSymbolsOption::class);

        $container
            ->register(RatesOption::class, RatesOption::class)
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.node_factory'), 'create'))
            ->addArgument(RatesOption::class);

        $container
            ->register(TimeFrameOption::class, TimeFrameOption::class)
            ->setFactory(array(new Reference('wp.plugins.financial_instruments.node_factory'), 'create'))
            ->addArgument(TimeFrameOption::class);

        $container->setParameter(
            'wp.plugins.financial_instruments.cron_events',
            array(
                new Reference(UpdateRatesCronEvent::class),
            )
        );

        $container
            ->register(UpdateRatesCronEvent::class, UpdateRatesCronEvent::class)
            ->addMethodCall('setUpdateRates', array(new Reference(Rates::class)));

        $container
            ->register(CurrencyExchange::class, CurrencyExchange::class)
            ->setFactory(array(CurrencyExchangeFactory::class, 'create'))
            ->addArgument(new Reference(RatesOption::class))
            ->addArgument('%wp.plugins.financial_instruments.flags.dir_url%')
            ->setLazy(true);

        $container
            ->register(WidgetCaption::class, WidgetCaption::class)
            ->addArgument(new Reference(CurrencyExchange::class));

        $container
            ->register(ScriptStyles::class, ScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(false);

        $container
            ->register(CurrencyTableWidget::class, CurrencyTableWidget::class)
            ->addArgument(new Reference(CurrencyExchange::class))
            ->addArgument('%wp.plugins.financial_instruments.flags.sizes%')
            ->addArgument(new Reference('wp.plugins.financial_instruments.form_factory'))
            ->addArgument(new Reference('wp.plugins.financial_instruments.twig'))
            ->addArgument(new Reference('wp.plugins.financial_instruments.request'))
            ->addArgument(new Reference(WidgetCaption::class));

        $container
            ->register(AdminScriptStyles::class, AdminScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(false);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }
}
