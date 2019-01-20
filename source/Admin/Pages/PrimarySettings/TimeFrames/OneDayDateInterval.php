<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames;

use Korobochkin\FinancialInstruments\Plugin;

class OneDayDateInterval extends SmartDateInterval
{
    public function __construct()
    {
        parent::__construct('P1D');
        $this->setLabel(__('Day', Plugin::NAME));
    }
}
