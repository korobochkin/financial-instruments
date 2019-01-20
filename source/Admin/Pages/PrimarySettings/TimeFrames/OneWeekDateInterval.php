<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames;

use Korobochkin\FinancialInstruments\Plugin;

class OneWeekDateInterval extends SmartDateInterval
{
    public function __construct()
    {
        parent::__construct('P1W');
        $this->setLabel(__('Week', Plugin::NAME));
    }
}
