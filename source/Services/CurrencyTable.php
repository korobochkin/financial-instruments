<?php
namespace Korobochkin\FinancialInstruments\Services;

use Donquixote\Cellbrush\Table\Table;
use Korobochkin\FinancialInstruments\Plugin;

class CurrencyTable
{
    /**
     * @var CurrencyExchange
     */
    protected $currencyExchange;

    const COL_CURRENCY = 'currency';

    const COL_PRICE = 'price';

    const COL_CHANGE = 'change';

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var Table
     */
    protected $table;

    /**
     * CurrencyTable constructor.
     * @param \Korobochkin\FinancialInstruments\Services\CurrencyExchange $currencyExchange
     */
    public function __construct(CurrencyExchange $currencyExchange)
    {
        $this->currencyExchange = $currencyExchange;
    }

    /**
     * Return HTML markup of table.
     *
     * @return string HTML markup of table.
     */
    public function getTableMarkup()
    {
        if (!$this->isValid()) {
            return '';
        }

        try {
            $table = $this->table = new Table();
            $table->addColNames(array(self::COL_CURRENCY, self::COL_PRICE, self::COL_CHANGE));

            $table->addRowName('top')
                ->th('top', self::COL_CURRENCY, $this->parameters['table_headers_currencies'])
                ->th('top', self::COL_PRICE, $this->parameters['table_headers_price'])
                ->th('top', self::COL_CHANGE, $this->parameters['table_headers_change']);

            foreach ($this->parameters['currency_list'] as $currencyKey => $currency) {
                try {
                    $this->currencyExchange->isCurrencyAvailable($currency);
                } catch (\Exception $exception) {
                    continue; // Currency not available, go to the next step.
                }

                $data = $this->currencyExchange->getConvertData($this->parameters['base_currency'], $currency);

                $colorWrapper = sprintf(
                    '<span class="financial-instruments--color-%1$s">%2$s</span>',
                    $data['trend'],
                    '%1$s'
                );

                // First column
                $firstCol = '';
                // phpcs:disable Generic.Files.LineLength.TooLong
                if ($this->parameters['flag_size'] > 0) {
                    $firstCol = sprintf(
                        '<img src="%1$s" class="financial-instruments--flag-and-ticker-icon financial-instruments--flag-and-ticker-icon-%2$s">',
                        esc_url($this->currencyExchange->getFlagUrlByCurrency($currency, $this->parameters['flag_size'])),
                        esc_attr($this->parameters['flag_size'])
                    );
                }

                $firstCol .= '<span class="financial-instruments--currency-ticker">' . esc_html($currency) . '</span>';

                // Second column
                $secondCol = sprintf(
                    $colorWrapper,
                    number_format_i18n($data['price'], 2)
                );

                // Third column
                $trend  = '<span class="financial-instruments--trend financial-instruments--trend-' . $data['trend'] . '"></span>';
                $change = sprintf(
                    /* translators: %s - currency change number (digit) in percentage. %% - one percentage symbol (typed twice for escape in sprintf() func.) */
                    __('%s<span class="financial-instruments--percentage-symbol">%%</span>', Plugin::NAME),
                    Text::numberFormatI18nPlusMinus($data['change'], 2)
                );
                $change = $trend . sprintf($colorWrapper, $change);

                // phpcs:enable Generic.Files.LineLength.TooLong

                $table->addRowName($currencyKey)
                    ->td($currencyKey, self::COL_CURRENCY, $firstCol)
                    ->td($currencyKey, self::COL_PRICE, $secondCol)
                    ->td($currencyKey, self::COL_CHANGE, $change);
            }
            return $table->render();
        } catch (\Exception $exception) {
            // Do nothing.
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->parameters['currency_list']) || !is_array($this->parameters['currency_list'])) {
            return false;
        }
        if (empty($this->parameters['base_currency'])) {
            return false;
        }
        if (!isset($this->parameters['flag_size']) || !is_int($this->parameters['flag_size'])) {
            return false;
        }

        if (!isset($this->parameters['table_headers_currencies'])
            || !isset($this->parameters['table_headers_price'])
            || !isset($this->parameters['table_headers_change'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }
}
