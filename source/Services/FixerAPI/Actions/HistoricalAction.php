<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI\Actions;

use Korobochkin\FinancialInstruments\Services\FixerAPI\Entities\HistoricalEntity;
use Korobochkin\FinancialInstruments\Services\FixerAPI\FixerAPIEndpoints;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoricalAction extends AbstractAction
{
    /**
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\APIEndpointDoesNotExistException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\FixerAPIException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\Http\HttpException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\InvalidAPIKeyException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\MaximumAllowedAPIRequestsException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\ResourceDoesNotExistException
     * @throws \Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\SubscriptionPlanDoesNotSupportAPIEndpointException
     *
     * @return HistoricalEntity
     */
    public function handleResponse()
    {
        $this->handleResponseHttpCodes();

        $entity = new HistoricalEntity();
        $entity->setData($this->decodeResponse());
        $this->setResponseEntity($entity);

        if (!$entity->isSuccess()) {
            $this->handleResponseErrors();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this->setHttpMethod('GET');
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        $symbols = implode(',', $this->details['symbols']);
        $date    = $this->details['date']->format('Y-m-d');

        return sprintf(
            FixerAPIEndpoints::HISTORICAL,
            rawurlencode($this->getApi()->getCredits()->getKey()),
            rawurlencode($this->details['base']),
            rawurlencode($symbols),
            rawurlencode($date)
        );
    }

    /**
     * Prepare any additional details for request.
     *
     * Calling this method is not required but by using it
     * you can be sure that you have all required data for request.
     *
     * @param array $options Your options which will be merged with defaults.
     *
     * @throws \Exception If required args is not presented in your $options or have invalid type.
     *
     * @return array Your options merged with defaults.
     */
    public function configureDetails(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefault('base', $this->getApi()->getBaseCurrency());
        $resolver->setAllowedTypes('base', array('string'));

        $resolver->setDefault('request_options', array());
        $resolver->setAllowedTypes('request_options', array('array'));

        $resolver->setRequired(array('symbols'));
        $resolver->setAllowedTypes('symbols', array('array'));

        $resolver->setRequired(array('date'));
        $resolver->setAllowedTypes('date', \DateTime::class);

        $options = $resolver->resolve($options);

        return $options;
    }
}
