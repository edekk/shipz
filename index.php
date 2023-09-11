<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHIPZ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        require_once __DIR__.'/app/configuration/bootstrap.php';

        session_start(array(
            'name' => SESSION_NAME,
        ));
        
        if (isset($_POST['victory'])) { // restart game
            $_SESSION = array();
        }

        if (!isset($_SESSION['game'])) { // prepare new game
            $_POST = array(); // clear $_POST

            $_SESSION['game'] = array();
            $_SESSION['game']['id'] = session_id();

            /**
             * Create computer grid
             */
            $computerGrid = new Grid();

            /**
             * Create computer ships
             */
            new Ship($computerGrid, array('masts' => 5));
            new Ship($computerGrid, array('masts' => 4));
            new Ship($computerGrid, array('masts' => 4));

            $_SESSION['game']['computer']['grid'] = $computerGrid;
            
            /**
             * Create player grid
             */
            $playerGrid = new Grid();
            $_SESSION['game']['player']['grid'] = $playerGrid;
        }
        
        $computerGrid = &$_SESSION['game']['computer']['grid'];
        $playerGrid = &$_SESSION['game']['player']['grid'];


        /**
         * Shooting
         */
        if (isset($_POST['shot'])) {
            $playerGrid->shoot($_POST['shot'], $computerGrid);
        }

        if ($playerGrid->victory) {
            echo '<div class="victory-msg">';
                echo '<span style="margin-right: 5px">You win! Congratulations!</span>';
                echo '<form method="post"><input type="submit" name="victory" value="Play again"></form>';
            echo '</div>';
        }        

        /**
         * Draw computer grid
         */
        // $computerGrid->draw('computer');

        /**
         * Draw player grid
         */
        $playerGrid->draw('player');

    ?>
</body>
</html>