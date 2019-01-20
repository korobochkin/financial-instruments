<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\FinancialInstruments\Admin\Cron\UpdateRatesCronEvent;

class Activation
{
    /**
     * @var UpdateRatesCronEvent
     */
    protected $updateRatesCronEvent;

    /**
     * Activation constructor.
     * @param UpdateRatesCronEvent $updateRatesCronEvent
     */
    public function __construct(
        UpdateRatesCronEvent $updateRatesCronEvent
    ) {
        $this->updateRatesCronEvent = $updateRatesCronEvent;
    }

    /**
     * @return $this
     */
    public function run()
    {
        $this->updateRatesCronEvent->unscheduleAll()->schedule();
        return $this;
    }
}
