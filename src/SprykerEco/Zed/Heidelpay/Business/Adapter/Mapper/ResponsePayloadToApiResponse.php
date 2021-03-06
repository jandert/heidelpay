<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Heidelpay\Business\Adapter\Mapper;

use Heidelpay\PhpApi\Response;
use SprykerEco\Zed\Heidelpay\Dependency\Service\HeidelpayToUtilEncodingServiceInterface;

class ResponsePayloadToApiResponse implements ResponsePayloadToApiResponseInterface
{
    /**
     * @var \SprykerEco\Zed\Heidelpay\Dependency\Service\HeidelpayToUtilEncodingServiceInterface
     */
    protected $utilEncoding;

    /**
     * @param \SprykerEco\Zed\Heidelpay\Dependency\Service\HeidelpayToUtilEncodingServiceInterface $utilEncoding
     */
    public function __construct(HeidelpayToUtilEncodingServiceInterface $utilEncoding)
    {
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @param string $transactionPayload
     * @param \Heidelpay\PhpApi\Response $heidelpayResponse
     *
     * @return void
     */
    public function map($transactionPayload, Response $heidelpayResponse)
    {
        $transactionPayloadArray = $this->getTransactionPayloadArray($transactionPayload);

        foreach ($transactionPayloadArray as $parameterGroup => $values) {
            $this->mapRequestParameterGroup($parameterGroup, $values, $heidelpayResponse);
        }
    }

    /**
     * @param string $parameterGroup
     * @param array $values
     * @param \Heidelpay\PhpApi\Response $heidelpayResponse
     *
     * @return void
     */
    protected function mapRequestParameterGroup($parameterGroup, array $values, Response $heidelpayResponse)
    {
        $parameterGroupObject = $this->getRequestParameterObject($parameterGroup, $heidelpayResponse);

        if ($parameterGroupObject === null) {
            return;
        }

        foreach ($values as $fieldName => $fieldValue) {
            $this->setParameterGroupProperty($parameterGroupObject, $fieldName, $fieldValue);
        }
    }

    /**
     * @param string $parameterGroup
     * @param \Heidelpay\PhpApi\Response $heidelpayResponse
     *
     * @return object|null
     */
    protected function getRequestParameterObject($parameterGroup, Response $heidelpayResponse)
    {
        $getParameterMethod = 'get' . ucfirst($parameterGroup);

        if (!method_exists($heidelpayResponse, $getParameterMethod)) {
            return null;
        }

        return $heidelpayResponse->$getParameterMethod();
    }

    /**
     * @param object $parameterGroupObject
     * @param string $fieldName
     * @param mixed $fieldValue
     *
     * @return void
     */
    protected function setParameterGroupProperty($parameterGroupObject, $fieldName, $fieldValue)
    {
        if (property_exists($parameterGroupObject, $fieldName)) {
            $parameterGroupObject->$fieldName = $fieldValue;
        }
    }

    /**
     * @param string $transactionPayload
     *
     * @return array
     */
    protected function getTransactionPayloadArray($transactionPayload)
    {
        return (array)$this->utilEncoding->decodeJson($transactionPayload, true);
    }
}
