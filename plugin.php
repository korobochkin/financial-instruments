<?php
namespace Korobochkin\FinancialInstruments;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/*
Plugin Name: Financial Instruments
Plugin URI: https://wordpress.org/plugins/FinancialInstruments/
Description: Currency widgets for any needs.
Author: korobochkin
Author URI: https://korobochkin.com
Version: 0.1.0
Text Domain: financial-instruments
Domain Path: /languages/
Requires at least: 4.9.0
Tested up to: 5.0.3
License: GPLv2 or later
*/

if (!class_exists('Korobochkin\FinancialInstruments\Plugin')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Run plugin and return it.
 *
 * @return Plugin
 */
function financialInstrumentsRunner()
{
    global $container;

    $key = 'financial-instruments-plugin';

    if (isset($GLOBALS[$key])) {
        return $GLOBALS[$key];
    }

    $plugin = $GLOBALS[$key] = new Plugin(__FILE__);

    if (isset($container) && is_object($container) && $container instanceof ContainerInterface) {
        $plugin->configureDependencies($container)->setContainer($container);
    } else {
        $selfContainer = new ContainerBuilder();
        $plugin->configureDependencies($selfContainer)->setContainer($selfContainer);
    }

    $plugin->run();

    if (is_admin()) {
        $plugin->runAdmin();
    }

    return $plugin;
}
financialInstrumentsRunner();
