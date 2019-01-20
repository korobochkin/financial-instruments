<?php
namespace Korobochkin\FinancialInstruments\Admin\Cron;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\FinancialInstruments\Services\UpdateRates\Rates;
use Korobochkin\WPKit\Cron\AbstractCronEvent;

class UpdateRatesCronEvent extends AbstractCronEvent
{
    /**
     * @var Rates
     */
    protected $updateRates;

    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence('daily');
        $this->setName(Plugin::_NAME_ . '_cron_update_rates');
    }

    public function execute()
    {
        try {
            $this->updateRates->getAndSaveRates();
        } catch (\Exception $exception) {
            // Do nothing.
        }
    }

    /**
     * @return Rates
     */
    public function getUpdateRates()
    {
        return $this->updateRates;
    }

    /**
     * @param Rates $updateRates
     * @return $this
     */
    public function setUpdateRates(Rates $updateRates)
    {
        $this->updateRates = $updateRates;
        return $this;
    }
}
