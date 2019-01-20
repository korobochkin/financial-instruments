<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames;

use Korobochkin\FinancialInstruments\Plugin;

class OneMonthDateInterval extends SmartDateInterval
{
    public function __construct()
    {
        parent::__construct('P1M');
        $this->setLabel(__('Month', Plugin::NAME));
    }
}
