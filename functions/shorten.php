<?php

session_start();

require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/UrlShortener.php");

$errors       = false;
$insertCustom = false;

$urlShortener = new UrlShortener();

if (($_POST['onoffswitch'] == 'on') && (isset($_POST['custom']))) {
    $customCode = $_POST['custom'];

    if (!$urlShortener->checkUrlExistInDatabase($customCode)) {
        $insertCustom = true;
    } else {
        $errors            = true;
        $_SESSION['error'] = 'Esta URL personalizada <a href="' . BASE_URL . $_POST['custom'] . '">' . BASE_URL . $_POST['custom'] . "</a> ya existe";
    }
}

if (isset($_POST['url']) && !$errors) {
    $orignalURL = $_POST['url'];

    if (!$insertCustom) {
        if ($uniqueCode = $urlShortener->validateUrlAndReturnCode($orignalURL)) {
            $_SESSION['success'] = $urlShortener->generateLinkForShortURL($uniqueCode);
        } else {
            $_SESSION['error'] = "Hubo un problema, la URL es inválida";
        }
    } else {
        if ($urlShortener->returnCustomCode($orignalURL, $customCode)) {
            $_SESSION['success'] = $urlShortener->generateLinkForShortURL($customCode);
        } else {
            header("Location: ../index.php?error=inurl");
            die();
        }
    }
}

header("Location: ../index.php");
exit();
