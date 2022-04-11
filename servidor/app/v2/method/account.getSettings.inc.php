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

    $lat = isset($_POST['lat']) ? $_POST['lat'] : '0.000000';
    $lng = isset($_POST['lng']) ? $_POST['lng'] : '0.000000';

    $lat = helper::clearText($lat);
    $lat = helper::escapeText($lat);

    $lng = helper::clearText($lng);
    $lng = helper::escapeText($lng);

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $auth = new auth($dbo);

    if (!auth::isSession()) {

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }
    }

    $result = array(
        "error" => false,
        "error_code" => ERROR_SUCCESS
    );

    $account = new account($dbo, $accountId);
    $accountInfo = $account->get();
    unset($account);

    // Get new messages count

    $messages_count = 0;
    $notifications_count = 0;
    $guests_count = 0;

    if (APP_MESSAGES_COUNTERS) {

        $msg = new msg($dbo);
        $msg->setRequestFrom($accountId);

        $messages_count = $msg->getNewMessagesCount();

        unset($msg);
    }

    // Get notifications count

    $notifications = new notify($dbo);
    $notifications->setRequestFrom($accountId);

    $notifications_count = $notifications->getNewCount($accountInfo['lastNotifyView']);

    unset($notifications);


    // Get new guests count

    $guests = new guests($dbo, $accountId);
    $guests->setRequestFrom($accountId);

    $guests_count = $guests->getNewCount($accountInfo['lastGuestsView']);

    unset($guests);

    $result['messagesCount'] = $messages_count;
    $result['notificationsCount'] = $notifications_count;
    $result['guestsCount'] = $guests_count;

    echo json_encode($result);
    exit;
}
