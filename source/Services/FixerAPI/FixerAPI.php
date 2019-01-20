<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI;

use GuzzleHttp\ClientInterface;

class FixerAPI
{
    /**
     * @var AuthCredits
     */
    protected $credits;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string Base currency ticker.
     */
    protected $baseCurrency;

    /**
     * FixerAPI constructor.
     * @param AuthCredits $credits
     * @param ClientInterface $client
     */
    public function __construct(AuthCredits $credits, ClientInterface $client)
    {
        $this->credits = $credits;
        $this->client  = $client;
    }

    /**
     * @return AuthCredits
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @param AuthCredits $credits
     * @return $this;
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * @param string $base
     * @return $this
     */
    public function setBaseCurrency($base)
    {
        $this->baseCurrency = $base;
        return $this;
    }
}
