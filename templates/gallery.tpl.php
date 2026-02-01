        <link rel = "stylesheet" href = "static/css/gallery.css">
        <!-- <script src = "static/js/library/gallery.js"></script> -->
    </head>
    
    <body>    
        <?php
            include "templates/navbar.tpl.php";
            include_once 'backend/Defaults/connect.php';
            include_once 'backend/pastGames.inc.php';
        ?>
        <div id="gallery-content">
            <h1>Last Gamejam's Games</h1>
            <div id="games">
                <?php
                // sql query to select games from previous year only
                $lastYear = sqlQueryObject(
                    $conn,
                    'SELECT MAX(year) FROM pastgames'
                )[0];

                foreach ($pastGames[$lastYear] as $game) {
                    ?>
                    <div class='game-container'>
                        <a class='game-thumbnail-container' href='index.php?filename=game&gameId=<?php echo $game->gameId ?>'>
                            <img class='grid game-thumbnail' src='<?php echo $game->thumbnail ?? '' ?>'>
                        </a>
                        <span class='grid name'><?php echo htmlspecialchars($game->title ?? '') ?></span>
                        <span class='grid creator'><?php echo htmlspecialchars($game->creators ?? '') ?></span>
                        <span class='grid genre'><?php echo htmlspecialchars($game->genre ?? '') ?></span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </body>
<html>
