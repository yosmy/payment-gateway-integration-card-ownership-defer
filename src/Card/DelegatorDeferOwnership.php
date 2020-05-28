<?php

namespace Yosmy\Payment\Card;

use Yosmy\Payment;

/**
 * @di\service()
 */
class DelegatorDeferOwnership implements DeferOwnership
{
    /**
     * @var PickOwnership
     */
    private $pickOwnership;

    /**
     * @var DeferOwnership[]
     */
    private $deferOwnershipServices;

    /**
     * @var AnalyzePostDeferOwnershipFail[]
     */
    private $analyzePostDeferOwnershipFailServices;

    /**
     * @di\arguments({
     *     deferOwnershipServices:                '#yosmy.payment.card.defer_ownership',
     *     analyzePostDeferOwnershipFailServices: '#yosmy.payment.card.post_defer_ownership_fail'
     * })
     *
     * @param PickOwnership $pickOwnership
     * @param DeferOwnership[] $deferOwnershipServices
     * @param AnalyzePostDeferOwnershipFail[] $analyzePostDeferOwnershipFailServices
     */
    public function __construct(
        PickOwnership $pickOwnership,
        array $deferOwnershipServices,
        array $analyzePostDeferOwnershipFailServices
    ) {
        $this->pickOwnership = $pickOwnership;
        $this->deferOwnershipServices = $deferOwnershipServices;
        $this->analyzePostDeferOwnershipFailServices = $analyzePostDeferOwnershipFailServices;
    }

    /**
     * {@inheritDoc}
     */
    public function defer(
        Payment\Card $card,
        int $amount
    ) {
        try {
            $ownership = $this->pickOwnership->pick(
                $card
            );

            if ($ownership->isProved()) {
                return;
            }
        } catch (Payment\Card\NonexistentOwnershipException $e) {
        }

        foreach ($this->deferOwnershipServices as $deferOwnership) {
            try {
                $deferOwnership->defer(
                    $card,
                    $amount
                );
            } catch (NotDeferrableOwnershipException $e) {
                foreach ($this->analyzePostDeferOwnershipFailServices as $analyzePostDeferOwnershipFail) {
                    $analyzePostDeferOwnershipFail->analyze(
                        $card,
                        $amount,
                        $e
                    );
                }

                throw $e;
            }
        }
    }
}