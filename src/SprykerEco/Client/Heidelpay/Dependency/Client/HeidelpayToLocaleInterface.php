<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Heidelpay\Dependency\Client;

interface HeidelpayToLocaleInterface
{
    /**
     * @return string
     */
    public function getCurrentLocale();
}
