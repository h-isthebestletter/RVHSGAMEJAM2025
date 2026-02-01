<?php
include_once 'backend/Defaults/connect.php';

$pastGamesRaw = sqlQueryAllObjects(
    $conn,
    'SELECT * FROM pastgames ORDER BY year DESC'
);

$pastGames = [];
foreach ($pastGamesRaw as $game) {
    $year = $game->year;
    $gameInfo = [
        'id' => $game->gameId,
        'title' => $game->name,
        'link' => $game->link,
        'description' => $game->description,
        'creators' => $game->creators,
        'genre' => $game->genre,
        'logo' => $game->logo,
        'video' => $game->video,
        'thumbnail' => $game->thumbnail,
    ];

    $pastGames[$year][$game->gameId] = $gameInfo;
}
