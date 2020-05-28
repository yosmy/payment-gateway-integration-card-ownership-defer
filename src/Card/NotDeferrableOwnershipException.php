<?php

namespace Yosmy\Payment\Card;

use Exception;

class NotDeferrableOwnershipException extends Exception
{
    /**
     * @var string
     */
    private $reason;

    /**
     * @param string $reason
     */
    public function __construct(
        string $reason
    ) {
        parent::__construct();

        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'reason' => $this->reason
        ];
    }
}