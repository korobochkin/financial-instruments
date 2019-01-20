<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI\Entities;

class AbstractFixerEntity
{
    /**
     * @var array Data From Fixer API.
     */
    protected $data;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->data['success'];
    }
}
