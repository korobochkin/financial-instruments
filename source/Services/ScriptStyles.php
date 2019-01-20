<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\ScriptsStyles\AbstractScriptsStyles;

class ScriptStyles extends AbstractScriptsStyles
{
    /**
     * @return $this
     */
    public function register()
    {
        wp_register_style(
            Plugin::NAME . '-widgets',
            $this->baseUrl . 'assets/styles/frontend/frontend.css',
            array(),
            Plugin::VERSION
        );

        wp_register_style(
            Plugin::NAME . '-fonts',
            self::fontsUrl(),
            array(),
            Plugin::VERSION
        );

        return $this;
    }

    /**
     * @return string
     */
    public static function fontsUrl()
    {
        $fontsUrl = '';
        $fonts    = array();
        $subsets  = 'latin,latin-ext';

        if ('off' !== _x('on', 'Open Sans font: on or off', Plugin::NAME)) {
            $fonts[] = 'Open Sans:300,400';
        }

        /*
         * Translators: To add an additional character subset specific to your language,
         * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
         */
        $subset = _x('no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', Plugin::NAME);
        if ('cyrillic' == $subset) {
            $subsets .= ',cyrillic,cyrillic-ext';
        } elseif ('greek' == $subset) {
            $subsets .= ',greek,greek-ext';
        } elseif ('devanagari' == $subset) {
            $subsets .= ',devanagari';
        } elseif ('vietnamese' == $subset) {
            $subsets .= ',vietnamese';
        }
        if ($fonts) {
            $fontsUrl = add_query_arg(array(
                'family' => urlencode(implode('|', $fonts)),
                'subset' => urlencode($subsets),
            ), 'https://fonts.googleapis.com/css');
        }
        return $fontsUrl;
    }
}
