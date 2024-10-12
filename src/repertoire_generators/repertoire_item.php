<?php

class RepertoireItem
{
    public function __construct(
        public int $timestamp,
        public string $title,
        public string $location,
        public string $ticketLink,
    ) {}
}
