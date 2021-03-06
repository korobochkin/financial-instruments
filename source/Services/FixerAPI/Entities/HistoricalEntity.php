<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI\Entities;

class HistoricalEntity extends AbstractFixerEntity implements FixerIEntityInterface
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

    /**
     * @return bool Will always true.
     */
    public function isHistorical()
    {
        return $this->data['historical'];
    }
}
