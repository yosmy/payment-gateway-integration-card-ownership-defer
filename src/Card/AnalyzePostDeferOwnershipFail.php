<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

interface AnalyzePostDeferOwnershipFail
{
    /**
     * @param Payment\Card                    $card
     * @param int                             $amount
     * @param NotDeferrableOwnershipException $e
     */
    public function analyze(
        Payment\Card $card,
        int $amount,
        NotDeferrableOwnershipException $e
    );
}