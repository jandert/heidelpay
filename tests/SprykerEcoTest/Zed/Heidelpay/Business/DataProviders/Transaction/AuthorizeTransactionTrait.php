<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Heidelpay\Business\DataProviders\Transaction;

use Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayTransactionLog;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use SprykerEco\Shared\Heidelpay\HeidelpayConfig;
use SprykerEcoTest\Zed\Heidelpay\Business\DataProviders\Encoder\EncoderTrait;
use SprykerEcoTest\Zed\Heidelpay\Business\HeidelpayTestConstants;

trait AuthorizeTransactionTrait
{
    use EncoderTrait;

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return void
     */
    public function createSuccessfulAuthorizeTransactionForOrder(SpySalesOrder $orderEntity)
    {
        $authorizeTransaction = new SpyPaymentHeidelpayTransactionLog();
        $authorizeTransaction
            ->setFkSalesOrder($orderEntity->getIdSalesOrder())
            ->setIdTransactionUnique('some unique id')
            ->setTransactionType(HeidelpayConfig::TRANSACTION_TYPE_AUTHORIZE)
            ->setResponseCode(HeidelpayTestConstants::HEIDELPAY_SUCCESS_RESPONSE)
            ->setRedirectUrl(HeidelpayTestConstants::CHECKOUT_EXTERNAL_SUCCESS_REDIRECT_URL)
            ->setRequestPayload('{}')
            ->setResponsePayload(
                $this->encryptData(
                    $this->prepareJsonString(
                        '{
                            "processing": {"result": "ACK"}, 
                            "payment": {"code": "CC.PA"}, 
                            "identification": {"transactionid": "' . $orderEntity->getIdSalesOrder() . '"},
                            "frontend": {"payment_frame_url": "' . HeidelpayTestConstants::CHECKOUT_EXTERNAL_SUCCESS_REDIRECT_URL . '"} 
                        }'
                    )
                )
            );

        $authorizeTransaction->save();
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return void
     */
    public function createUnsuccessfulAuthorizeTransactionForOrder(SpySalesOrder $orderEntity)
    {
        $authorizeTransaction = new SpyPaymentHeidelpayTransactionLog();
        $authorizeTransaction
            ->setFkSalesOrder($orderEntity->getIdSalesOrder())
            ->setIdTransactionUnique('some unique id')
            ->setTransactionType(HeidelpayConfig::TRANSACTION_TYPE_AUTHORIZE)
            ->setResponseCode(HeidelpayTestConstants::HEIDELPAY_UNSUCCESS_RESPONSE)
            ->setRedirectUrl(HeidelpayTestConstants::CHECKOUT_EXTERNAL_SUCCESS_REDIRECT_URL)
            ->setRequestPayload('{}')
            ->setResponsePayload(
                $this->encryptData(
                    $this->prepareJsonString(
                        '{
                            "processing": {
                                "result": "NOK",
                                "return": "Custom error",
                                "status": "REJECTED_VALIDATION"
                            }, 
                            "payment": {"code": "CC.PA"}, 
                            "identification": {"transactionid": "' . $orderEntity->getIdSalesOrder() . '"},
                            "frontend": {"payment_frame_url": "' . HeidelpayTestConstants::CHECKOUT_EXTERNAL_SUCCESS_REDIRECT_URL . '"} 
                        }'
                    )
                )
            );

        $authorizeTransaction->save();
    }

    /**
     * @param string $jsonString
     *
     * @return string
     */
    protected function prepareJsonString($jsonString)
    {
        return preg_replace("~[\n \t]+~", '', $jsonString);
    }
}
