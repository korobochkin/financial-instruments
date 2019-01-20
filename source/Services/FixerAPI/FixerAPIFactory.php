<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI;

use GuzzleHttp\Client;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerAPIKeyOption;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerBaseCurrencyOption;
use Korobochkin\FinancialInstruments\Services\WordPressHandler;

class FixerAPIFactory
{
    /**
     * Creates the API instance.
     *
     * @param FixerAPIKeyOption $fixerAPIKeyOption
     * @param FixerBaseCurrencyOption $fixerBaseCurrencyOption
     *
     * @return FixerAPI New API instance.
     */
    public static function create(
        FixerAPIKeyOption $fixerAPIKeyOption,
        FixerBaseCurrencyOption $fixerBaseCurrencyOption
    ) {
        $client = new Client(array(
            'handler' => new WordPressHandler(),
            'base_uri' => FixerAPIEndpoints::API_PROD,
        ));

        $credits = new AuthCredits($fixerAPIKeyOption->get());

        $api = new FixerAPI($credits, $client);
        $api->setBaseCurrency($fixerBaseCurrencyOption->get());

        return $api;
    }
}
