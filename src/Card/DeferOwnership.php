<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
interface DeferOwnership
{
    /**
     * @param Payment\Card $card
     * @param int          $amount
     *
     * @throws NotDeferrableOwnershipException
     */
    public function defer(
        Payment\Card $card,
        int $amount
    );
}