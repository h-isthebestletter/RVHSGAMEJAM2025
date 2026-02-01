        <link rel="stylesheet" href="static/css/game.css">
        <script src="static/js/game.js"></script>
    </head>

    <body>
        <?php
            include_once 'templates/navbar.tpl.php';
            include_once 'templates/stars.tpl.php';
            include_once 'backend/pastGames.inc.php';
            require_once 'backend/Defaults/connect.php';

            // there are 2 parts to this chunk of php:
            // the first chunk gets game info and displays it to the user
            // the second chunk runs on the server and handles POST requests for ratings

            $gameId = $_GET['gameId'];

            $gameInfo = null;
            foreach ($pastGames as $year) {
                foreach ($year as $id => $info) {
                    if ($id === $gameId) {
                        $gameInfo = $info;
                        break;
                    }
                }
            }

            $thumbnailExists = file_exists($gameInfo->thumbnail);
            $trailerExists = isset($gameInfo->video);

            // get comment info
            $userEmail = $_SESSION['userEmail'];
            $userId = sqlQueryObject(
                $conn,
                'SELECT userId FROM users WHERE email = ?',
                [$userEmail]
            )->userId;

            $commentInfo = sqlQueryAllObjects(
                $conn,
                'SELECT pfp, username, `comment` FROM comments LEFT JOIN users ON comments.userId = users.userId WHERE gameId = ? AND (users.userId = ? OR users.whitelisted = 1)',
                [$gameId, $userId]
            );
        ?>
        <div id="game-header">
            <h1><?php echo htmlspecialchars($gameInfo->name ?? '') ?></h1>
            <div><?php echo htmlspecialchars($gameInfo->genre ?? '') ?></div>
            <div>Created by <?php echo htmlspecialchars($gameInfo->creators ?? '') ?></div>
        </div>
        
        <div id="game-content">
            <div id="game-carousel">
                <?php
                // show arrows only if both trailer and thumbnail exist
                if ($trailerExists && $thumbnailExists) {
                    ?>
                    <a href="javascript:void(0)" onclick="update()"><div id="left-arrow">&#x2190;</div></a>
                    <?php
                }

                if ($trailerExists || $thumbnailExists) {
                    ?>
                    <div class="thumbnail-container">
                    <?php
                }
                if ($trailerExists) {
                    ?>
                    <iframe id="trailer" class="thumbnail" src="<?php echo $gameInfo->video ?>"></iframe>
                    <?php
                }
                if ($thumbnailExists) {
                    ?>
                    <img id="thumbnail" class="thumbnail" src="<?php echo $gameInfo->thumbnail ?>">
                    <?php
                }
                if ($trailerExists || $thumbnailExists) {
                    ?>
                    </div>
                    <?php
                }
                
                // show arrows only if both trailer and thumbnail exist
                if ($trailerExists && $thumbnailExists) {
                    ?>
                    <a href="javascript:void(0)" onclick="update()"><div id="right-arrow">&#x2192;</div></a>

                    <!-- js to make the arrows functional -->
                    <script>
                    let trailer = document.getElementById("trailer");
                    let thumbnail = document.getElementById("thumbnail");
                    let leftArrow = document.getElementById("left-arrow");
                    let rightArrow = document.getElementById("right-arrow");
                
                    let trailerSelected = true;

                    function update() {
                        if (trailerSelected) {
                            leftArrow.style.display = "none";
                            thumbnail.style.display = "none";
                            rightArrow.style.display = "unset";
                            trailer.style.display = "unset";
                        } else {
                            leftArrow.style.display = "unset";
                            thumbnail.style.display = "unset";
                            rightArrow.style.display = "none";
                            trailer.style.display = "none";
                        }
                        trailerSelected = !trailerSelected;
                    }

                    update();
                    </script>
                    <?php
                }
                ?>
            </div>
            <a href="<?php echo $gameInfo->link ?>">
                <div class="anchor-button">Play Game</div>
            </a>

            <div id='game-description'><?php echo nl2br($gameInfo->description) ?></div>
            <?php
            if (isset($_SESSION['userEmail'])) {
                ?>
                <!-- review form -->
                <h2>Rate</h2>
                <form action='<?php echo "index.php?filename=game&gameId=$gameId" ?>' method='POST' class="cont-v-ch">
                    <?php
                    $rating = sqlQueryObject(
                        $conn,
                        'SELECT MainRating main, ThemeRating related, AestheticRating aesthetic, FunRating fun FROM ratings WHERE userId = (SELECT userId FROM users WHERE email = ?) AND gameId = ?',
                        [$_SESSION['userEmail'], $gameId]
                    );
                    ?>
                    <div class="ratings"> 
                        <b>Overall</b>
                        <div id="overall">
                            <!-- name=rating-overall -->
                            <?php makeNewRating('overall', $rating?->main) ?>
                        </div>
                        Relatedness to Theme
                        <div id="related">
                            <!-- name=rating-related -->
                            <?php makeNewRating('related', $rating?->related) ?>
                        </div>
                        Aesthetic
                        <div id="aesthetic">
                            <!-- name=rating-aesthetic -->
                            <?php makeNewRating('aesthetic', $rating?->aesthetic) ?>
                        </div>
                        Fun
                        <div id="fun">
                            <!-- name=rating-fun -->
                            <?php makeNewRating('fun', $rating?->fun) ?>
                        </div>
                    </div>
                    <button class="submit" type='submit'>Submit ratings</button>
                </form>

                <!-- comment form -->
                <h2>Comment</h2>
                <form action='<?php echo "index.php?filename=game&gameId=$gameId" ?>' method='POST' class="cont-v-ch">
                    <!-- <label for="comment-input">Create a comment:</label> -->
                    <!-- name=comment -->
                    <textarea placeholder="Enter a comment" id="comment-input" name="comment"></textarea>
                    <button class="submit" type='submit'>Add comment</button>
                </form>
                <?php
            } else {
                ?>
                <div class='login-notice'><b>You must log in to leave a review.</b></div>
                <?php
            }
            ?>

            <!-- show all comments -->
            <h2>All Comments</h2>
            <div class="comment-container">
                <?php
                foreach ($commentInfo as $comment) {
                    // hide blank comments because for some reason there are a ton of them in the database
                    // TODO: hide whitespace comments also (comments that are made of spaces)
                    if ($comment->comment === '' || strlen(trim($comment->comment)) === 0) { continue; }
                    ?>
                    <div class="comment">
                        <div class="commenter">
                            <img class='pfp' src='<?php echo $comment->pfp ?>'>
                            <div><?php echo htmlspecialchars($comment->username) ?></div>
                        </div>
                        <div><?php echo htmlspecialchars($comment->comment) ?></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </body>
</html>
