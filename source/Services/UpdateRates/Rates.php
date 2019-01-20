<?php
namespace Korobochkin\FinancialInstruments\Services\UpdateRates;

use Korobochkin\FinancialInstruments\Options\Fixer\FixerSymbolsOption;
use Korobochkin\FinancialInstruments\Options\RatesOption;
use Korobochkin\FinancialInstruments\Options\TimeFrameOption;
use Korobochkin\FinancialInstruments\Services\ContinueExecution\OutOfTimeException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Actions\HistoricalAction;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Actions\LatestAction;
use Korobochkin\FinancialInstruments\Services\FixerAPI\FixerAPI;
use Symfony\Component\Validator\Exception\ValidatorException;

class Rates
{
    /**
     * @var FixerAPI
     */
    protected $api;

    /**
     * @var LatestAction
     */
    protected $latestRatesAction;

    /**
     * @var HistoricalAction
     */
    protected $historicalRatesAction;

    /**
     * @var FixerSymbolsOption
     */
    protected $symbolsOption;

    /**
     * @var TimeFrameOption
     */
    protected $timeFrameOption;

    /**
     * @var RatesOption
     */
    protected $ratesOption;

    /**
     * @var callable
     */
    protected $continueExecution;

    /**
     * Rates constructor.
     * @param FixerAPI $api
     * @param FixerSymbolsOption $symbolsOption
     * @param TimeFrameOption $timeFrameOption
     * @param RatesOption $ratesOption
     */
    public function __construct(
        FixerAPI $api,
        FixerSymbolsOption $symbolsOption,
        TimeFrameOption $timeFrameOption,
        RatesOption $ratesOption
    ) {
        $this->api             = $api;
        $this->symbolsOption   = $symbolsOption;
        $this->timeFrameOption = $timeFrameOption;
        $this->ratesOption     = $ratesOption;

        $this->latestRatesAction = new LatestAction($api);
    }

    /**
     * Retrieve and save latest rates.
     *
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\APIEndpointDoesNotExistException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\FixerAPIException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\Http\HttpException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\InvalidAPIKeyException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\MaximumAllowedAPIRequestsException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\ResourceDoesNotExistException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\SubscriptionPlanDoesNotSupportAPIEndpointException
     *
     * @throws ValidatorException
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function getAndSaveRates()
    {
        $this->latestRatesAction     = new LatestAction($this->api);
        $this->historicalRatesAction = new HistoricalAction($this->api);

        $latestEntity = $this->latestRatesAction
            ->setDetails($this->latestRatesAction->configureDetails(array('symbols' => $this->symbolsOption->get())))
            ->request()
            ->handleResponse();

        /**
         * @var $timeFrame \DateInterval
         */
        $timeFrame = $this->timeFrameOption->get();
        $dateTime  = new \DateTime();
        $dateTime->sub($timeFrame);

        $historicalEntity = $this->historicalRatesAction
            ->setDetails(
                $this->historicalRatesAction->configureDetails(array(
                    'symbols' => $this->symbolsOption->get(),
                    'date' => $dateTime,
                ))
            )
            ->request()
            ->handleResponse();


        $rates   = array();
        $rates[] = $historicalEntity->getData();
        $rates[] = $latestEntity->getData();

        $validateResults = $this->ratesOption->validateValue($rates);

        if (0 !== count($validateResults)) {
            throw new ValidatorException();
        }

        $this->ratesOption->updateValue($rates);

        return $this;
    }

    /**
     * Checks should we break process or not.
     *
     * @throws OutOfTimeException In case if need to stop progress.
     *
     * @return $this For chain calls.
     */
    public function continueExecution()
    {
        call_user_func($this->getContinueExecution());
        return $this;
    }

    /**
     * Returns callable which used to check process relevance.
     *
     * @return callable Something that will be called after each iteration to check
     * if current PHP process still not obsolete.
     */
    public function getContinueExecution()
    {
        return $this->continueExecution;
    }

    /**
     * Sets callable which used to check process relevance.
     *
     * @param callable $continueExecution Something that will be called after each iteration to check
     * if current PHP process still not obsolete.
     *
     * @return $this For chain calls.
     */
    public function setContinueExecution($continueExecution)
    {
        $this->continueExecution = $continueExecution;
        return $this;
    }
}
