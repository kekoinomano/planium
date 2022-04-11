<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $accountId = auth::getCurrentUserId();

    $account = new account($dbo, $accountId);

    $error = false;
    $send_status = false;
    $fullname = "";

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowShowMyFriends = isset($_POST['allowShowMyFriends']) ? $_POST['allowShowMyFriends'] : '';
        $allowShowMyGallery = isset($_POST['allowShowMyGallery']) ? $_POST['allowShowMyGallery'] : '';
        $allowShowMyGifts = isset($_POST['allowShowMyGifts']) ? $_POST['allowShowMyGifts'] : '';
        $allowShowMyInfo = isset($_POST['allowShowMyInfo']) ? $_POST['allowShowMyInfo'] : '';

        $allowShowMyFriends = helper::clearText($allowShowMyFriends);
        $allowShowMyFriends = helper::escapeText($allowShowMyFriends);

        $allowShowMyGallery = helper::clearText($allowShowMyGallery);
        $allowShowMyGallery = helper::escapeText($allowShowMyGallery);

        $allowShowMyGifts = helper::clearText($allowShowMyGifts);
        $allowShowMyGifts = helper::escapeText($allowShowMyGifts);

        $allowShowMyInfo = helper::clearText($allowShowMyInfo);
        $allowShowMyInfo = helper::escapeText($allowShowMyInfo);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ($allowShowMyGallery === "on") {

                $allowShowMyGallery = 1;

            } else {

                $allowShowMyGallery = 0;
            }

            if ($allowShowMyGifts === "on") {

                $allowShowMyGifts = 1;

            } else {

                $allowShowMyGifts = 0;
            }

            if ($allowShowMyFriends === "on") {

                $allowShowMyFriends = 1;

            } else {

                $allowShowMyFriends = 0;
            }

            if ($allowShowMyInfo === "on") {

                $allowShowMyInfo = 1;

            } else {

                $allowShowMyInfo = 0;
            }

            $account->setPrivacySettings($allowShowMyGallery, $allowShowMyGifts, $allowShowMyFriends, $allowShowMyInfo);

            header("Location: /account/settings/privacy/?error=false");
            exit;
        }

        header("Location: /account/settings/privacy/?error=true");
        exit;
    }

    $accountInfo = $account->get();

    auth::newAuthenticityToken();

    $page_id = "settings_privacy";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-profile-settings']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");
?>

<body class="settings-page">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="profile-content standard-page">

                    <h1><?php echo $LANG['page-profile-settings']; ?></h1>

                    <form accept-charset="UTF-8" action="/account/settings/privacy" autocomplete="off" class="edit_user" id="settings-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                        <div class="tabbed-content">

                            <div class="tab-container">
                                <nav class="tabs">
                                    <a href="/account/settings/profile"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                    <a href="/account/settings/privacy"><span class="tab active"><?php echo $LANG['label-privacy']; ?></span></a>
                                    <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                    <a href="/account/settings/profile/password"><span class="tab"><?php echo $LANG['label-password']; ?></span></a>
                                    <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                                    <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                    <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['label-blacklist']; ?></span></a>
                                    <a href="/account/settings/profile/deactivation"><span class="tab"><?php echo $LANG['action-deactivation-profile']; ?></span></a>

                                </nav>
                            </div>

                            <?php

                            if ( isset($_GET['error']) ) {

                                switch ($_GET['error']) {

                                    case "true" : {

                                        ?>

                                        <div class="errors-container" style="margin-top: 15px;">
                                            <ul>
                                                <?php echo $LANG['msg-error-unknown']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }

                                    default: {

                                        ?>

                                        <div class="success-container" style="margin-top: 15px;">
                                            <ul>
                                                <b><?php echo $LANG['label-thanks']; ?></b>
                                                <br>
                                                <?php echo $LANG['label-settings-saved']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }
                                }
                            }
                            ?>

                            <div class="errors-container" style="margin-top: 15px; <?php if (!$error) echo "display: none"; ?>">
                                <ul>
                                    <?php echo $LANG['ticket-send-error']; ?>
                                </ul>
                            </div>

                            <div class="tab-pane active form-table">

                                <div class="link-preference form-row">
                                    <div class="form-cell left">
                                        <h2><?php echo $LANG['label-allow-show-friends-desc']; ?></h2>
                                    </div>

                                    <div class="form-cell">
                                        <div class="opt-in">
                                            <input id="allowShowMyFriends" name="allowShowMyFriends" type="checkbox" <?php if ($accountInfo['allowShowMyFriends'] == 1) echo "checked=\"checked\""; ?>>
                                            <label for="allowShowMyFriends"><?php echo $LANG['label-allow-show-friends']; ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="link-preference form-row">
                                    <div class="form-cell left">
                                        <h2><?php echo $LANG['label-allow-show-gallery-desc']; ?></h2>
                                    </div>

                                    <div class="form-cell">
                                        <div class="opt-in">
                                            <input id="allowShowMyGallery" name="allowShowMyGallery" type="checkbox" <?php if ($accountInfo['allowShowMyGallery'] == 1) echo "checked=\"checked\""; ?>>
                                            <label for="allowShowMyGallery"><?php echo $LANG['label-allow-show-gallery']; ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="link-preference form-row">
                                    <div class="form-cell left">
                                        <h2><?php echo $LANG['label-allow-show-gifts-desc']; ?></h2>
                                    </div>

                                    <div class="form-cell">
                                        <div class="opt-in">
                                            <input id="allowShowMyGifts" name="allowShowMyGifts" type="checkbox" <?php if ($accountInfo['allowShowMyGifts'] == 1) echo "checked=\"checked\""; ?>>
                                            <label for="allowShowMyGifts"><?php echo $LANG['label-allow-show-gifts']; ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="link-preference form-row">
                                    <div class="form-cell left">
                                        <h2><?php echo $LANG['label-allow-show-info-desc']; ?></h2>
                                    </div>

                                    <div class="form-cell">
                                        <div class="opt-in">
                                            <input id="allowShowMyInfo" name="allowShowMyInfo" type="checkbox" <?php if ($accountInfo['allowShowMyInfo'] == 1) echo "checked=\"checked\""; ?>>
                                            <label for="allowShowMyInfo"><?php echo $LANG['label-allow-show-info']; ?></label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <input style="margin-top: 25px" class="blue primary_btn" name="commit" type="submit" value="<?php echo $LANG['action-save']; ?>">

                    </form>
                </div>


            </div>
        </div>


    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript">

        $('textarea[name=status]').autosize();

    </script>


</body
</html>