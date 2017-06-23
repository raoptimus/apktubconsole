<?php
$_PROJECTS = [
    ".......",
    ".......",
    "...........",
    ".......",
    ".......",
    ".......",
    ".......",
    "....net",
    "....net",
    ".......",
    ".......",
    "....com",
    ".......",
    ".......",
];
function getProjectName()
{
    global $_PROJECTS;
    $project = getenv("PROJECT");

    if (!empty($project)) {
        return $project;
    }
    $projects = $_PROJECTS;
    $project = $projects[0];

    if (!isset($_SERVER['REQUEST_URI'])) {
        throw new RuntimeException("");
    }

    $path = explode("/", ltrim($_SERVER['REQUEST_URI'], "/"));

    if (in_array($path[0], $projects)) {
        $project = $path[0];
    }

    return $project;
}

$_PROJECT = getProjectName();
