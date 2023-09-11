<?php
    class Coordinate {
        public $x;
        public $y;
        public $occupied;
        public $status;

        function __construct(string $x, string $y, int $occupied = 0, string $status = 'unknown') {
            $this->x = $x;
            $this->y = $y;
            $this->occupied = $occupied;
            $this->status = $status;
        }
    }