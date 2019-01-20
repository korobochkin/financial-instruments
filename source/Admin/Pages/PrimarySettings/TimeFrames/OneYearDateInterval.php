<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames;

use Korobochkin\FinancialInstruments\Plugin;

class OneYearDateInterval extends SmartDateInterval
{
    public function __construct()
    {
        parent::__construct('P1Y');
        $this->setLabel(__('Year', Plugin::NAME));
    }
}
