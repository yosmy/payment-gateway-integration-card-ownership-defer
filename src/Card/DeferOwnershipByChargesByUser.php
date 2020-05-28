<?php

namespace Yosmy\Payment\Card;

use Yosmy;
use Yosmy\Payment;

/**
 * @di\service({
 *     private: true,
 *     tags: [
 *         'yosmy.payment.card.defer_ownership',
 *     ]
 * })
 */
class DeferOwnershipByChargesByUser implements Payment\Card\DeferOwnership
{
    /**
     * @var string
     */
    private $period;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var Payment\Charge\ComputeAmount
     */
    private $computeAmount;

    /**
     * @di\arguments({
     *     period: "%yosmy_payment_card_ownership_defer_period%",
     *     amount: "%yosmy_payment_card_ownership_defer_amount%"
     * })
     *
     * @param string                       $period
     * @param int                          $amount
     * @param Payment\Charge\ComputeAmount $computeAmount
     */
    public function __construct(
        string $period,
        int $amount,
        Payment\Charge\ComputeAmount $computeAmount
    ) {
        $this->period = $period;
        $this->amount = $amount;
        $this->computeAmount = $computeAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function defer(
        Payment\Card $card,
        int $amount
    ) {
        $from = strtotime(sprintf(
            '%s -%s',
            date('Y-m-d H:i:s'),
            $this->period
        ));

        $total = $this->computeAmount->compute(
            [$card->getUser()],
            null,
            $from,
            null
        );

        if ($total + $amount > $this->amount) {
            throw new NotDeferrableOwnershipException('by-charges-by-user');
        }
    }
}