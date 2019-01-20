<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI\Entities;

use Korobochkin\FinancialInstruments\Services\FixerAPI\Entities\AbstractFixerEntity;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Entities\FixerIEntityInterface;

class LatestEntity extends AbstractFixerEntity implements FixerIEntityInterface
{
    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->data['timestamp'];
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->data['base'];
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->data['date'];
    }

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->data['rates'];
    }
}
