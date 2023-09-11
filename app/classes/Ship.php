<?php
    class Ship {
        public $status = 'alive';
        public $shipSquares = array();
        public $masts;
        
        private $orientation;

        private $start_x;
        private $start_y;

        private $end_x;
        private $end_y;

        private $occupiedSquares = array();

        function __construct(Grid $grid, array $options = null) {
            /**
             * Gather occupied squares
             */
            $this->occupiedSquares = $this->filterOccupiedSquares($grid);
            /**
             * Set properties
             */
            $this->orientation = isset($options['orientation']) ? $options['orientation'] : $this->randomShipOrientation();
            $this->masts = isset($options['masts']) ? $options['masts'] : null;
            $this->start_x = isset($options['x']) ? array_search($options['x'], $grid->rows) : null;
            $this->start_y = isset($options['y']) ? array_search($options['y'], $grid->cols) : null;

            $this->shipSquares = $this->createShipLocation($grid);

            $this->placeOnGrid($grid, $this->shipSquares);

            $this->restrictAreaAroundShip($grid);

            /**
             * Add $this ship to grid object
             */
            array_push($grid->ships, $this);
        }

        private function placeOnGrid(Grid $grid, $shipCoords) {
            foreach ($shipCoords as $xy => $_) {
                $grid->coordinates[$xy]->occupied = 2;
                $grid->coordinates[$xy]->status = 'alive';
            }
        }        

        private function restrictAreaAroundShip(Grid $grid) {
            foreach ($this->shipSquares as $c) {
                
                $indexOfRow = array_search($c->x, $grid->rows);
                $indexOfCol = array_search($c->y, $grid->cols);

                for ($i = -1; $i <= 1; $i++) {
                    for ($j = -1; $j <= 1; $j++) {
                        // prevent going out of grid bounds
                        if ((($indexOfRow + $i) >= 0 && ($indexOfRow + $i) <= 9) && (($indexOfCol + $j) >= 0 && ($indexOfCol + $j) <= 9)) {
                            // do not override ship square
                            if ($grid->coordinates[$grid->rows[$indexOfRow + $i].$grid->cols[$indexOfCol + $j]]->occupied == 0) $grid->coordinates[$grid->rows[$indexOfRow + $i].$grid->cols[$indexOfCol + $j]]->occupied = 1;
                        }
                    }
                }

            }
        }

        private function shipStartEnd(Grid $grid) {
            $this->start_x = $this->randomStartRow();
            $this->start_y = $this->randomStartCol();
            $starting_point = $grid->rows[$this->start_x].$grid->cols[$this->start_y];

            if ($this->orientation == 'horizontal') {
                $this->end_x = $this->start_x;
                $this->end_y = $this->start_y + $this->masts - 1;
                $ending_point = $grid->rows[$this->end_x].$grid->cols[$this->end_y];
            } else {
                $this->end_x = $this->start_x + $this->masts - 1;
                $this->end_y = $this->start_y;
                $ending_point = $grid->rows[$this->end_x].$grid->cols[$this->end_y];
            }

            /**
             * If current starting or ending point is at restricted (already occupied) square, try another location (call function again).
             * This approach is not optimal, but does the job for given requirements (one 5 masts ship, two 4 masts ships)
             */
            if (in_array($starting_point, array_keys($this->occupiedSquares)) || in_array($ending_point, array_keys($this->occupiedSquares))) {
                $this->shipStartEnd($grid);
            }
        }

        private function createShipLocation(Grid $grid) {
            if ($this->start_x === null || $this->start_y === null) {
                $this->shipStartEnd($grid);
            }
            
            $shipCoords = array();

            for ($i = 0; $i < $this->masts; $i++) {
                if ($this->orientation == 'horizontal') {
                    $_x = $this->start_x;
                    $_y = $this->start_y + $i;
                } else {
                    $_x = $this->start_x + $i;
                    $_y = $this->start_y;
                }

                $x = $grid->rows[$_x];
                $y = $grid->cols[$_y];                

                $shipCoords[$x.$y] = new Coordinate($grid->rows[$_x], $grid->cols[$_y], 2, 'alive');
            }

            return $shipCoords;
        }

        private function randomShipOrientation() {
            return (rand(0, 99) % 2 == 0) ? 'horizontal' : 'vertical';
        }

        private function randomStartRow() {
            if ($this->orientation == 'horizontal') return intval(rand(0, 9));
            else return intval(rand(0, 9 - $this->masts));
        }

        private function randomStartCol() {
            if ($this->orientation == 'horizontal') return intval(rand(0, 9 - $this->masts));
            else return intval(rand(0, 9));
        }

        private function filterOccupiedSquares(Grid $grid) {
            return array_filter($grid->coordinates, (function ($square) {
                if ($square->occupied === 0) return false;
                else return true;
            }));
        }
    }