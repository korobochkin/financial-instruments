<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI;

class FixerAPIEndpoints
{
    const API_PROD = 'http://data.fixer.io/api/';

    /**
     * %1$s - API Key.
     * %2$s - Ticker of base currency.
     * %3$s - Tickers of currencies to retrieve separated by commas.
     */
    const LATEST = 'latest?access_key=%1$s&base=%2$s&symbols=%3$s';

    /**
     * %1$s - API Key.
     * %2$s - Ticker of base currency.
     * %3$s - Tickers of currencies to retrieve separated by commas.
     * %4$s - Date in format YYYY-MM-DD.
     */
    const HISTORICAL = '%4$s?access_key=%1$s&base=%2$s&symbols=%3$s';
}
