<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\FinancialInstruments\Plugin;

class WidgetCaption
{
    /**
     * @var CurrencyExchange
     */
    protected $currencyExchange;

    /**
     * PluginDeveloper constructor.
     * @param CurrencyExchange $currencyExchange
     */
    public function __construct(CurrencyExchange $currencyExchange)
    {
        $this->currencyExchange = $currencyExchange;
    }

    /**
     * @param string $baseCurrency
     * @return string Widget caption.
     */
    public function getCaption($baseCurrency)
    {
        return sprintf(
            /*
             * Translators: %1$s - base currency ticker (ISO code).
             * %2$s - date of update currency rate in regional format
             * (only month, date and year available right now).
             * Available date variables - http://php.net/manual/en/function.date.php.
             */
            __('Currency exchange rates in <span class="financial-instruments-base">%1$s</span> on %2$s', Plugin::NAME),
            esc_html($baseCurrency),
            esc_html($this->currencyExchange->getRatesDateTime()->format(
                /*
                 * Translators: date of update currencies rates in regional format
                 * (only month, date and year available right now).
                 * Variables - http://php.net/manual/en/function.date.php.
                 */
                __('F j, Y', Plugin::NAME)
            ))
        );
    }
}
