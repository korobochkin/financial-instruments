<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI;

class AuthCredits
{
    /**
     * @var string API Key.
     */
    protected $key;

    /**
     * AuthCredits constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
}
