<?php

namespace App\Common\Exception;

class GasPriceUpdateServiceException extends \Exception
{
    const GAS_PRICE_INSTANT_EMPTY = 'Gas Price Instant file is empty.';
}
