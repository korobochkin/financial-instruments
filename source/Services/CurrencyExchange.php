<?php
namespace Korobochkin\FinancialInstruments\Services;

class CurrencyExchange
{
    const CHANGE_DOWN = 'down';

    const CHANGE_FLAT = 'flat';

    const CHANGE_UP = 'up';

    /**
     * @var string
     */
    protected $baseCurrency;

    /**
     * @var \DateTime
     */
    protected $previousRatesDateTime;

    /**
     * @var float[]
     */
    protected $previousRates;

    /**
     * @var \DateTime
     */
    protected $ratesDateTime;

    /**
     * @var float[]
     */
    protected $rates;

    /**
     * @var string
     */
    protected $flagsDirUrl;

    /**
     * @var string Extension of image with flag.
     */
    protected $defaultFlagExtension = 'png';

    /**
     * CurrencyExchange constructor.
     * @param string $baseCurrency
     * @param \DateTime $previousRatesDateTime
     * @param float[] $previousRates
     * @param \DateTime $ratesDateTime
     * @param float[] $rates
     * @param string|null $flagsDirUrl
     */
    public function __construct(
        $baseCurrency,
        \DateTime $previousRatesDateTime,
        array $previousRates,
        \DateTime $ratesDateTime,
        array $rates,
        $flagsDirUrl = null
    ) {
        $this->baseCurrency          = $baseCurrency;
        $this->previousRatesDateTime = $previousRatesDateTime;
        $this->previousRates         = $previousRates;
        $this->ratesDateTime         = $ratesDateTime;
        $this->rates                 = $rates;
        $this->flagsDirUrl           = trailingslashit($flagsDirUrl);
    }

    /**
     * Change currency from one to another.
     *
     * @param string $fromCurrency Currency ISO code.
     * @param string $toCurrency Currency ISO code.
     * @param float|int $amount
     *
     * @throws \UnexpectedValueException
     *
     * @return int|float
     */
    public function convert($fromCurrency, $toCurrency, $amount = 1)
    {
        if ($fromCurrency === $toCurrency) {
            return 1 * $amount;
        }

        $this->isCurrencyAvailable($fromCurrency)->isCurrencyAvailable($toCurrency);

        $fromRate = $fromCurrency === ($this->baseCurrency) ? 1 : $this->rates[$fromCurrency];
        $toRate   = $toCurrency   === ($this->baseCurrency) ? 1 : $this->rates[$toCurrency];

        return ($toRate / $fromRate) * $amount;
    }

    /**
     * Change currency from one to another.
     *
     * @param string $fromCurrency Currency ISO code.
     * @param string $toCurrency Currency ISO code.
     *
     * @throws \UnexpectedValueException
     *
     * @return array Data of currency change.
     */
    public function getConvertData($fromCurrency, $toCurrency)
    {
        $data = array(
            'previous_price'    => 1,
            'price'             => 1,
            'change'            => 0,
            'change_percentage' => 0,
            'trend'             => self::CHANGE_FLAT,
        );

        if ($fromCurrency === $toCurrency) {
            return $data;
        }

        $this->isCurrencyAvailable($fromCurrency)->isCurrencyAvailable($toCurrency);

        $data['price']             = $this->convertCurrent($fromCurrency, $toCurrency);
        $data['previous_price']    = $this->convertPrevious($fromCurrency, $toCurrency);
        $data['change']            = $data['price'] - $data['previous_price'];
        $data['change_percentage'] = 100 - (($data['price'] * 100) / $data['previous_price']);

        if ($data['change'] > 0) {
            $data['trend'] = self::CHANGE_UP;
        } elseif ($data['change'] < 0) {
            $data['trend']             = self::CHANGE_DOWN;
            $data['change_percentage'] = $data['change_percentage'] * -1;
        }

        return $data;
    }

    /**
     * Change currency from one to another.
     *
     * @param string $fromCurrency Currency ISO code.
     * @param string $toCurrency Currency ISO code.
     *
     * @return float Price of currency.
     */
    protected function convertCurrent($fromCurrency, $toCurrency)
    {
        $fromRate = $fromCurrency === ($this->baseCurrency) ? 1 : $this->rates[$fromCurrency];
        $toRate   = $toCurrency   === ($this->baseCurrency) ? 1 : $this->rates[$toCurrency];

        return $toRate / $fromRate;
    }

    /**
     * Change currency from one to another.
     *
     * @param string $fromCurrency Currency ISO code.
     * @param string $toCurrency Currency ISO code.
     *
     * @return float Price of currency.
     */
    protected function convertPrevious($fromCurrency, $toCurrency)
    {
        $fromRate = $fromCurrency === ($this->baseCurrency) ? 1 : $this->previousRates[$fromCurrency];
        $toRate   = $toCurrency   === ($this->baseCurrency) ? 1 : $this->previousRates[$toCurrency];

        return $toRate / $fromRate;
    }

    /**
     * @param string $currency Currency ISO code.
     *
     * @throws \UnexpectedValueException
     *
     * @return $this For chain calls.
     */
    public function isCurrencyAvailable($currency)
    {
        if (isset($this->rates[$currency]) || $currency === $this->baseCurrency) {
            return $this;
        }
        throw new \UnexpectedValueException();
    }

    /**
     * @param $currencyCode string Three letters currency code (USD).
     * @return bool|string Country code (US) of false on failure.
     */
    public function getCountryCodeFromCurrencyCode($currencyCode)
    {
        return substr($currencyCode, 0, 2);
    }

    /**
     * Get flag for country.
     *
     * @param string $currency Currency ISO code.
     * @param int $size 16, 24, 32, 48, 64.
     * @param string $style flat or shiny.
     *
     * @throws \RuntimeException If country not have flag.
     *
     * @return string Url to flag.
     */
    public function getFlagUrlByCurrency($currency, $size = 16, $style = 'flat')
    {
        $iso = $this->getCountryCodeFromCurrencyCode($currency);

        if (!$iso) {
            throw new \RuntimeException();
        }

        return $this->flagsDirUrl . $style . '/'. $size . '/' . $iso . '.' . $this->defaultFlagExtension;
    }

    /**
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Get all available currencies.
     *
     * @return array All available currencies.
     */
    public function getAllAvailable()
    {
        $keys   = array_keys($this->rates);
        $keys[] = $this->baseCurrency;
        return $keys;
    }

    /**
     * @param string $baseCurrency
     *
     * @return $this
     */
    public function setBaseCurrency($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRatesDateTime()
    {
        return $this->ratesDateTime;
    }

    /**
     * @param \DateTime $ratesDateTime
     *
     * @return $this
     */
    public function setRatesDateTime(\DateTime $ratesDateTime)
    {
        $this->ratesDateTime = $ratesDateTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFlagExtension()
    {
        return $this->defaultFlagExtension;
    }

    /**
     * @param string $defaultFlagExtension
     * @return $this
     */
    public function setDefaultFlagExtension($defaultFlagExtension)
    {
        $this->defaultFlagExtension = $defaultFlagExtension;
        return $this;
    }
}
