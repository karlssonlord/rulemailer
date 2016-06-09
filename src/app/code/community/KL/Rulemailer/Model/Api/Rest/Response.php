<?php

use GuzzleHttp\Psr7\Response;

class KL_Rulemailer_Model_Api_Rest_Response
{
    /**
     * @var ResponseInterface
     */
    protected $httpResponse;

    /**
     * @var array|stdClass
     */
    protected $jsonBody;

    /**
     * @var string
     */
    protected $rawBody;

    /**
     * Constructor
     *
     * Assigns the HTTP response to a property, as well as the body
     * representation. It then attempts to decode the body as JSON.
     */
    public function __construct(Response $response)
    {
        $this->httpResponse = $response;
        $this->jsonBody = json_decode($response->getBody());
    }

    /**
     * Was the request successful?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->httpResponse->getStatusCode() == 200;
    }

    /**
     * Did an error occur in the request?
     *
     * @return bool
     */
    public function isError()
    {
        return $this->httpResponse->getStatusCode() != 200;
    }

    /**
     * Fetch error code (if any)
     *
     * @return null|string
     */
    public function getError()
    {
        if ($this->isError()) {
            return $this->httpResponse->getStatusCode() . ': ' . $this->httpResponse->getReasonPhrase();
        }

        return null;
    }

    /**
     * Property overloading to JSON elements
     *
     * If a named property exists within the JSON response returned,
     * proxies to it. Otherwise, returns null.
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (null === $this->jsonBody) {
            return null;
        }

        if (!isset($this->jsonBody->{$name})) {
            return null;
        }

        return $this->jsonBody->{$name};
    }

    /**
     * Return the decoded response body
     *
     * @return array|stdClass
     */
    public function toValue()
    {
        return $this->jsonBody;
    }

}
