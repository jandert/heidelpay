<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Heidelpay\Form;

use SprykerEco\Shared\Heidelpay\HeidelpayConfig;

class IdealSubForm extends AbstractHeidelpaySubForm
{
    const PAYMENT_METHOD = HeidelpayConfig::PAYMENT_METHOD_IDEAL;
}
