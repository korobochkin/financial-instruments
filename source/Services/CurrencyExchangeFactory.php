<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\FinancialInstruments\Options\RatesOption;

class CurrencyExchangeFactory
{
    /**
     * @param RatesOption $rates
     * @param string|null $flagsDirUrl
     *
     * @throws \Exception If DateTime or DateTimeZone object was not created.
     *
     * @return CurrencyExchange
     */
    public static function create(RatesOption $rates, $flagsDirUrl = null)
    {
        $data = $rates->get();

        $previousDateTime = new \DateTime('now', DateTimeFactory::createTimeZone());
        $previousDateTime->setTimestamp($data[0]['timestamp']);

        $currentDateTime = new \DateTime('now', DateTimeFactory::createTimeZone());
        $currentDateTime->setTimestamp($data[1]['timestamp']);

        return new CurrencyExchange(
            $data[0]['base'],
            $previousDateTime,
            $data[0]['rates'],
            $currentDateTime,
            $data[1]['rates'],
            $flagsDirUrl
        );
    }
}
