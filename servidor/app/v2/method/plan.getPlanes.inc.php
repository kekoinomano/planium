<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $distance = isset($_POST['distance']) ? $_POST['distance'] : 30;
    $distancia = isset($_POST['dist']) ? $_POST['dist'] : 0;
    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $sex = isset($_POST['sex']) ? $_POST['sex'] : -1;

    $lat = isset($_POST['lat']) ? $_POST['lat'] : '0.000000';
    $lng = isset($_POST['lng']) ? $_POST['lng'] : '0.000000';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : 0;

    $distance = helper::clearInt($distance);
    $distancia = helper::clearInt($distancia);
    $categoria = helper::clearInt($categoria);
    $itemId = helper::clearInt($itemId);

    if ($sex != -1) $sex = helper::clearInt($sex);

    $lat = helper::clearText($lat);
    $lat = helper::escapeText($lat);

    $lng = helper::clearText($lng);
    $lng = helper::escapeText($lng);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    // Update geolocation in db

    $account = new account($dbo, $accountId);

    if (strlen($lat) > 0 && strlen($lng) > 0 && $itemId > 0) {

        $result = $account->setGeoLocation($lat, $lng);
    }

    unset($account);

    // Get People List

    $geo = new geo($dbo);
    $geo->setRequestFrom($accountId);

    //$result = $geo->getPlanes2($itemId, $lat, $lng, $distance, $sex, $distancia);
    $result = $geo->getPlanes3($itemId, $lat, $lng, $distance, $sex, $distancia, $categoria);

    echo json_encode($result);
    exit;
}
