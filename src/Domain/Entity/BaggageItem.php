<?php

class BaggageItem {
    private int $baggageId;
    private int $itemId;

    public function __construct(int $baggageId, int $itemId) {
        $this->baggageId = $baggageId;
        $this->itemId = $itemId;
    }

    public function getBaggageId(): int {
        return $this->baggageId;
    }

    public function getItemId(): int {
        return $this->itemId;
    }
}