<?php
namespace Korobochkin\FinancialInstruments\Services\FixerAPI\Actions;

use Korobochkin\FinancialInstruments\Services\FixerAPI\Entities\FixerIEntityInterface;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\APIEndpointDoesNotExistException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\FixerAPIException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\Http\HttpException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\InvalidAPIKeyException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\MaximumAllowedAPIRequestsException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\ResourceDoesNotExistException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\Exceptions\SubscriptionPlanDoesNotSupportAPIEndpointException;
use Korobochkin\FinancialInstruments\Services\FixerAPI\FixerAPI;
use Psr\Http\Message\ResponseInterface;

class AbstractAction
{
    /**
     * @var FixerAPI Main class to use API.
     */
    protected $api;

    /**
     * @var ResponseInterface Response after HTTP request.
     */
    protected $response;

    /**
     * @var FixerIEntityInterface
     */
    protected $responseEntity;

    /**
     * @var string HTTP method (for example GET, POST, PUT...).
     */
    protected $httpMethod;

    /**
     * @var array Used to prepare request URL and request body.
     */
    protected $details;

    /**
     * Construct Action instance and setup API.
     *
     * @param FixerAPI $api
     */
    public function __construct(FixerAPI $api)
    {
        $this->api = $api;
        $this->lateConstruct();
    }

    /**
     * Called from construct method.
     *
     * Used to configure instance without rewriting construct method.
     */
    public function lateConstruct()
    {
    }

    /**
     * @inheritdoc
     */
    public function request()
    {
        $response = $this->getClient()->request(
            $this->getHttpMethod(),
            $this->getUrl(),
            $this->details['request_options']
        );

        $this->setResponse($response);

        return $this;
    }

    /**
     * Handle response HTTP status codes.
     *
     * @throws HttpException
     */
    public function handleResponseHttpCodes()
    {
        switch ($this->getResponse()->getStatusCode()) {
            case 200:
                return $this;

            default:
                throw new HttpException();
        }
    }

    /**
     * Handle errors in response.
     *
     * @throws APIEndpointDoesNotExistException
     * @throws InvalidAPIKeyException
     * @throws MaximumAllowedAPIRequestsException
     * @throws ResourceDoesNotExistException
     * @throws SubscriptionPlanDoesNotSupportAPIEndpointException
     * @throws FixerAPIException
     */
    public function handleResponseErrors()
    {
        $data = $this->getResponseEntity()->getData();
        switch ($data['error']['code']) {
            case 404:
                throw new ResourceDoesNotExistException($data['error']['info'], $data['error']['code']);

            case 101:
                throw new InvalidAPIKeyException($data['error']['info'], $data['error']['code']);
                break;

            case 103:
                throw new APIEndpointDoesNotExistException($data['error']['info'], $data['error']['code']);

            case 104:
                throw new MaximumAllowedAPIRequestsException($data['error']['info'], $data['error']['code']);

            case 105:
                throw new SubscriptionPlanDoesNotSupportAPIEndpointException(
                    $data['error']['info'],
                    $data['error']['code']
                );

            default:
                throw new FixerAPIException($data['error']['info'], $data['error']['code']);
        }
    }

    public function decodeResponse()
    {
        return $data = \GuzzleHttp\json_decode($this->getResponse()->getBody()->getContents(), true);
    }

    /**
     * @inheritdoc
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @inheritdoc
     */
    public function setApi(FixerAPI $api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return FixerIEntityInterface
     */
    public function getResponseEntity()
    {
        return $this->responseEntity;
    }

    /**
     * @param FixerIEntityInterface $responseEntity
     * @return $this
     */
    public function setResponseEntity(FixerIEntityInterface $responseEntity)
    {
        $this->responseEntity = $responseEntity;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @inheritdoc
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @inheritdoc
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->getApi()->getClient();
    }
}
