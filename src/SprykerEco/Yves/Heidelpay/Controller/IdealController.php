<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Heidelpay\Controller;

use Generated\Shared\Transfer\HeidelpayIdealAuthorizeFormTransfer;
use Generated\Shared\Transfer\HeidelpayTransactionLogTransfer;
use Spryker\Yves\Kernel\Controller\AbstractController;

/**
 * @method \SprykerEco\Yves\Heidelpay\HeidelpayFactory getFactory()
 * @method \SprykerEco\Client\Heidelpay\HeidelpayClientInterface getClient()
 */
class IdealController extends AbstractController
{
    /**
     * @return array
     */
    public function authorizeAction()
    {
        $orderReference = $this->getOrderReferenceFromSession();
        $idealAuthorizeFormTransfer = $this->getAuthorizeFormTransferForOrder($orderReference);

        return $this->viewResponse(
            [
                'idealAuthorizeForm' => $idealAuthorizeFormTransfer,
            ]
        );
    }

    /**
     * @return string
     */
    protected function getOrderReferenceFromSession()
    {
        return $this->getClient()
            ->getQuoteFromSession()
            ->getOrderReference();
    }

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\HeidelpayIdealAuthorizeFormTransfer
     */
    protected function getAuthorizeFormTransferForOrder($orderReference)
    {
        $authorizeTransactionLogTransfer = $this->getClient()
            ->getAuthorizeTransactionLogForOrder($orderReference);

        $idealAuthorizeFormTransfer = new HeidelpayIdealAuthorizeFormTransfer();

        $this->mapTransactionLogToAuthorizeFormTransfer(
            $authorizeTransactionLogTransfer,
            $idealAuthorizeFormTransfer
        );

        return $idealAuthorizeFormTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer $authorizeTransactionLogTransfer
     * @param \Generated\Shared\Transfer\HeidelpayIdealAuthorizeFormTransfer $idealAuthorizeFormTransfer
     *
     * @return void
     */
    protected function mapTransactionLogToAuthorizeFormTransfer(HeidelpayTransactionLogTransfer $authorizeTransactionLogTransfer, HeidelpayIdealAuthorizeFormTransfer $idealAuthorizeFormTransfer)
    {
        $this->getFactory()
            ->createHeidelpayResponseToIdealAuthorizeFormMapper()
            ->map($authorizeTransactionLogTransfer->getHeidelpayResponse(), $idealAuthorizeFormTransfer);
    }
}
