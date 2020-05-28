<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service({
 *     private: true,
 *     tags: [
 *         'yosmy.payment.card.defer_ownership',
 *     ]
 * })
 */
class DeferOwnershipByChargesByCard implements Payment\Card\DeferOwnership
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
     * @var Payment\CollectCards
     */
    private $collectCards;

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
     * @param Payment\CollectCards         $collectCards
     * @param Payment\Charge\ComputeAmount $computeAmount
     */
    public function __construct(
        string $period,
        int $amount,
        Payment\CollectCards $collectCards,
        Payment\Charge\ComputeAmount $computeAmount
    ) {
        $this->period = $period;
        $this->amount = $amount;
        $this->collectCards = $collectCards;
        $this->computeAmount = $computeAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function defer(
        Payment\Card $card,
        int $amount
    ) {
        /** @var Payment\Card[] $cardsWithSameFingerprint */
        $cardsWithSameFingerprint = $this->collectCards->collect(
            null,
            null,
            $card->getFingerprint(),
            true,
            null,
            null
        );

        $cards = [];

        foreach ($cardsWithSameFingerprint as $cardWithSameFingerprint) {
            $cards[] = $cardWithSameFingerprint->getId();
        }

        $from = strtotime(sprintf(
            '%s -%s',
            date('Y-m-d H:i:s'),
            $this->period
        ));

        $total = $this->computeAmount->compute(
            null,
            $cards,
            $from,
            null
        );

        if ($total + $amount > $this->amount) {
            throw new NotDeferrableOwnershipException('by-charges-by-card');
        }
    }
}