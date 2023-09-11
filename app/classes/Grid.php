<?php
    class Grid {
        public $rows = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        public $cols = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
        public $coordinates = array();
        public $ships = array();
        public $victory = false;

        private $message;
        
        function __construct() {
            foreach ($this->rows as $row) {
                foreach ($this->cols as $col) {
                    $this->coordinates[$row.$col] = new Coordinate($row, $col);
                }
            }
        }        

        public function shoot($xy, Grid &$computerGrid = null) {
            if ($computerGrid === null) $computerGrid = &$this;

            if ($computerGrid->coordinates[$xy]->occupied == 2) {
                $this->coordinates[$xy]->status = 'shot';
                $computerGrid->coordinates[$xy]->status = 'shot';

                $struckShip = $this->findShip($computerGrid->ships, $xy);
                $struckShip->shipSquares[$xy]->status = 'shot';

                
                $this->isShipSunk($xy, $computerGrid);                
                if ($struckShip->status != 'sunk') $this->message = '<div class="msg">You\'ve just shot at '.$xy.' destroying mast of a '.$struckShip->masts.' masts ship!</div>';
            }

            if ($computerGrid->coordinates[$xy]->occupied < 2) {
                $this->coordinates[$xy]->status = 'missed';
                $computerGrid->coordinates[$xy]->status = 'missed';

                $this->message = '<div class="msg">You\'ve missed at '.$xy.'!</div>';
            }

            $this->victory($computerGrid);

        }

        private function findShip(array &$ships, $xy) {
            foreach ($ships as &$ship) {
                if (in_array($xy, array_keys($ship->shipSquares))) return $ship;
            }
        }

        private function isShipSunk($xy, Grid &$computerGrid) {
            $shipSunk = false;
            foreach ($computerGrid->ships as &$ship) {
                if (in_array($xy, array_keys($ship->shipSquares))) { // check only for ship shot at
                    
                    $shipSquareStatuses = array();
                    
                    foreach ($ship->shipSquares as $square) {
                        array_push($shipSquareStatuses, $square->status);
                    }

                    if (!in_array('alive', $shipSquareStatuses)) $shipSunk = true;

                    if ($shipSunk) {
                        $ship->status = 'sunk';
                        $this->message = '<div class="msg">You\'ve just sunk a '.$ship->masts.' masts ship!</div>';
                    }
                }
            }
        }

        private function victory(Grid &$computerGrid) {
            $shipStatuses = array();
            foreach ($computerGrid->ships as $ship) {
                array_push($shipStatuses, $ship->status);
            }

            if (in_array('alive', $shipStatuses)) return false;
            else {
                $this->victory = 'victory';
                return true;
            }
        }

        public function draw(string $gridName) {
            $gridHTML = '<div class="grid '.$this->victory.'">';
            foreach ($this->rows as $row) {
                $gridHTML .= '<div class="row">';
                foreach ($this->cols as $col) {
                    if ($gridName == 'player') {
                        $buttonStatus = ($this->coordinates[$row.$col]->status !== 'unknown' || $this->victory) ? 'disabled="true"' : '';
                        $gridHTML .= '<form method="post" class="square '.$this->coordinates[$row.$col]->status.'">';
                        $gridHTML .= '<input data-status="'.$this->coordinates[$row.$col]->status.'" type="hidden" name="shot" value="'.$row.$col.'">';
                        $gridHTML .= '<input type="submit" value="" '.$buttonStatus.'>';
                        $gridHTML .= '</form>';
                    }
                    if ($gridName == 'computer') {
                        $gridHTML .= '<div class="square '.$this->coordinates[$row.$col]->status.'"></div>';
                    }
                }
                $gridHTML .= '</div>';
            }
            $gridHTML .= '</div>';

            echo '<div class="wrapper '.$gridName.'">';
                echo '<div class="cols">';
                    foreach ($this->cols as $col) echo '<div class="square col">'.$col.'</div>';
                echo '</div>';
                echo '<div class="rows">';
                    foreach ($this->rows as $row) echo '<div class="square row">'.$row.'</div>';
                echo '</div>';
                echo $gridHTML;
                echo $this->message;
            echo '</div>';

        }
    }
