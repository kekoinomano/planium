<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk, https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    $profileId = $helper->getUserId($request[0]);

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error']) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    if ($profileInfo['accountType'] == ACCOUNT_TYPE_GROUP || $profileInfo['accountType'] == ACCOUNT_TYPE_PAGE) {

        include_once("../html/group.inc.php");
        exit;
    }

    $myPage = false;

    if (auth::getCurrentUserId() == $profileId) {

        $myPage = true;

        $account = new account($dbo, $profileId);
        $account->setLastActive();
        unset($account);

    } else {

        if (auth::getCurrentUserId() != 0) {

            $guests = new guests($dbo, $profileId);
            $guests->setRequestFrom(auth::getCurrentUserId());

            $guests->add(auth::getCurrentUserId());
        }
    }

    $accessMode = 0;

    if ($profileInfo['friend'] || $myPage) {

        $accessMode = 1;
    }

    $wall = new post($dbo);
    $wall->setProfileId($profileId);
    $wall->setRequestFrom(auth::getCurrentUserId());

    $posts_all = $profileInfo['postsCount'];
    $posts_loaded = 0;

    if (!empty($_POST)) {

        $postId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $postId = helper::clearInt($postId);
        $loaded = helper::clearInt($loaded);

        $result = $wall->get($profileInfo['id'], $postId, $accessMode);

        $posts_loaded = count($result['posts']);

        $result['inbox_loaded'] = $posts_loaded + $loaded;
        $result['inbox_all'] = $posts_all;

        if ($posts_loaded != 0) {

            ob_start();

            foreach ($result['posts'] as $key => $value) {

                draw::post($value, $LANG, $helper, false);
            }

            $result['html'] = ob_get_clean();


            if ($result['inbox_loaded'] < $posts_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Items.more('/<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    if (strlen($profileInfo['normalCoverUrl']) == 0) {

        if ($myPage) {

            $profileCoverUrl = "/img/cover_add.png";

        } else {

            $profileCoverUrl = "/img/cover_none.png";
        }

    } else {

        $profileCoverUrl = $profileInfo['normalCoverUrl'];
    }

    if (strlen($profileInfo['bigPhotoUrl']) == 0) {

        $profilePhotoUrl = APP_URL."/img/profile_default_photo.png";
        $photo = '';

    } else {

        $profilePhotoUrl = $profileInfo['bigPhotoUrl'];
        $photo = "/photo";
    }

    auth::newAuthenticityToken();

    $page_id = "profile";

    $css_files = array("main.css", "my.css", "tipsy.css", "gifts.css");
    $page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

    include_once("../html/common/header.inc.php");
?>

<body class="user-profile page-profile">

    <?php
    include_once("../html/common/topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div id="profile-column-main" class="content-block col-sm-12 col-md-12 col-lg-12 order-md-1 order-lg-1">

            <div class="sidebar-wrapper">

                <div class="card">

                    <!-- Profile Cover -->

                    <div class="profile-cover">
                        <div class="profile-main-cover">
                            <div class="cover-wrapper d-flex">
                                <div class="loader profile-cover-loader">
                                    <i class="fa fa-spin fa-spin"></i>
                                </div>
                                <div class="profile-cover-progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="profile-cover-img" style="background-image: url(<?php echo $profileCoverUrl ?>);"></div>
                                <?php

                                if ($myPage) {

                                    ?>
                                    <div class="btn btn-secondary cover-upload-button">
                                        <input type="file" id="cover-upload" name="uploaded_file">
                                        <i class="iconfont icofont-camera-alt"></i>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div> <!-- End Profile Cover -->

                    <div class="profile-info">

                        <div class="profile-info-block profile-main-info">

                            <div class="d-flex">

                                <div class="profile-photo-container">
                                    <div class="loader profile-photo-loader">
                                        <i class="fa fa-spin fa-spin"></i>
                                    </div>
                                    <div class="profile-photo-progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a class="profile-photo-link" href="<?php echo $profilePhotoUrl; ?>">
                                        <div class="profile-photo" style="background-image: url(<?php echo $profilePhotoUrl; ?>);" onclick="blueimp.Gallery($('.profile-photo-link')); return false"></div>
                                    </a>
                                    <?php

                                    if ($myPage) {

                                        ?>
                                        <div class="btn btn-secondary photo-upload-button">
                                            <input type="file" id="photo-upload" name="uploaded_file">
                                            <i class="iconfont icofont-ui-edit"></i>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="w-100 justify-content-end profile-main-info-container">
                                    <div class="fullname-line ">
                                        <h1 class="display-name"><?php echo $profileInfo['fullname']; ?></h1>
                                        <?php

                                        if ($profileInfo['verified']) {

                                            ?>
                                            <span class="user-badge user-verified-badge ml-1" rel="tooltip" title="<?php echo $LANG['label-account-verified']; ?>"><i class="iconfont icofont-check-alt"></i></span>
                                            <?php
                                        }

                                        if ($profileInfo['online']) {

                                            ?>
                                            <i class="online-status bg-green" rel="tooltip" title="Online"></i>
                                            <?php

                                        } else {

                                            ?>
                                            <i class="online-status bg-gray" rel="tooltip" title="<?php echo $profileInfo['lastAuthorizeTimeAgo']; ?>"></i>
                                            <?php
                                        }
                                        ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <?php

                        if (auth::getCurrentUserId() != 0) {

                            ?>

                            <div class="profile-info-block profile-main-actions text-right pt-0">

                                <?php

                                if ($myPage) {

                                    ?>
                                    <a class="btn button blue btn-primary mb-2 mb-sm-0" href="/account/settings/profile">
                                        <i class="ic icon-edit-2 mr-1"></i>
                                        <?php echo $LANG['action-edit-profile']; ?>
                                    </a>
                                    <?php

                                } else {

                                    ?>

                                    <div class="js_actions_block">

                                        <?php

                                        if (auth::getCurrentUserId() != 0) {

                                            ?>

                                            <a href="javascript:void(0)" class="button btn-profile-action-button mr-1 blue" onclick="Profile.getGiftsBox('<?php echo $request[0]; ?>', '<?php echo $LANG['dlg-select-gift']; ?>'); return false;">
                                                <i class="iconfont icofont-gift pl-1"></i>
                                            </a>

                                            <?php

                                            if ($profileInfo['allowMessages'] == 1 || ($profileInfo['allowMessages'] == 0 && $profileInfo['friend'] === true)) {

                                                ?>

                                                <a href="/account/chat/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" class="button btn-profile-action-button blue">
                                                    <i class="iconfont icofont-ui-message px-1"></i>
                                                </a>

                                                <?php
                                            }
                                        }
                                        ?>

                                    </div>

                                    <div class="js_follow_block">
                                        <?php

                                        if ($profileInfo['friend']) {

                                            ?>
                                            <a id="btn-friend-action" class="button red js_follow_btn btn-profile-action-button" href="javascript:void(0)" onclick="Friends.remove('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"><i class="iconfont icofont-minus px-1"></i><?php echo $LANG['action-remove-from-friends']; ?></a>
                                            <?php

                                        } else {

                                            if ($profileInfo['follow']) {

                                                ?>
                                                <a id="btn-friend-action" class="button yellow js_follow_btn btn-profile-action-button" onclick="Friends.cancelRequest('<?php echo $profileInfo['id']; ?>'); return false;"><i class="iconfont icofont-not-allowed px-1"></i><?php echo $LANG['action-cancel-friend-request']; ?></a>
                                                <?php

                                            } else {

                                                ?>
                                                <a id="btn-friend-action" class="button green js_follow_btn btn-profile-action-button" onclick="Friends.sendRequest('<?php echo $profileInfo['id']; ?>'); return false;" ><i class="iconfont icofont-plus px-1"></i><?php echo $LANG['action-add-to-friends']; ?></a>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </a>
                                    </div>



                                    <div class="dropdown" style="display: inline-block;">
                                        <button type="button" class="button blue btn-profile-menu dropdown-toggle mb-sm-0" data-toggle="dropdown">
                                            <i class="iconfont icofont-ui-user"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item report-button" data-toggle="modal" data-target="#new-report" onclick="Report.showDialog('<?php echo $profileInfo['id']; ?>', '<?php echo REPORT_TYPE_PROFILE; ?>'); return false;"><?php echo $LANG['action-report']; ?></a>
                                            <?php

                                            if (!$myPage && !$profileInfo['error'] && $profileInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                if ($profileInfo['blocked']) {

                                                    ?>
                                                    <a class="dropdown-item block-button js_block_btn" data-action="unblock" data-toggle="modal" data-target="#profile-unblock-dlg" onclick="Profile.getBlockBox('<?php echo $profileInfo['id']; ?>'); return false;"><?php echo $LANG['action-unblock']; ?></a>
                                                    <?php

                                                } else {

                                                    ?>
                                                    <a class="dropdown-item block-button js_block_btn" data-action="block" data-toggle="modal" data-target="#profile-block-dlg" onclick="Profile.getBlockBox('<?php echo $profileInfo['id']; ?>'); return false;"><?php echo $LANG['action-block']; ?></a>
                                                    <?php

                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>

                            <?php

                        } else {

                            ?>
                            <div class="profile-info-block profile-main-actions text-right pt-0">
                                <span class="promo-msg mr-3"><?php echo sprintf($LANG['msg-contact-promo'], "<strong>" . $profileInfo['fullname'] . "</strong>"); ?></span>
                                <a class="btn btn-primary mr-1" href="/signup"><?php echo $LANG['action-signup']; ?></a>
                                <a class="btn btn-secondary" href="/"><?php echo $LANG['action-login']; ?></a>
                            </div>
                            <?php
                        }
                        ?>

                    </div>

                </div>
                <!-- end photos card -->

            </div>
        </div>

        <div id="profile-column-content" class="row content-block col-sm-12 col-md-12 col-lg-12 order-md-1 order-lg-1">

            <div class="column-content-sidebar col-md-4">

                <?php

                    if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyInfo'] == 0) {

                        ?>
                        <div class="card" id="preview-info-block">
                            <div class="card-header border-0">
                                <h3 class="card-title"><i class="icofont icofont-info-circle mr-2"></i><?php echo $LANG['label-profile-info']; ?></h3>
                            </div>
                            <div class="card-body p-3">
                                <?php

                                if (strlen($profileInfo['location']) == 0 && strlen($profileInfo['fb_page']) == 0 && strlen($profileInfo['instagram_page']) == 0 && strlen($profileInfo['status']) == 0) {

                                    ?>
                                    <h5 class="m-2 text-center"><?php echo $LANG['label-empty-page']; ?></h5>
                                    <?php

                                } else {

                                    if (strlen($profileInfo['location']) != 0) {

                                        ?>
                                        <div class="addon-line d-flex align-content-center flex-column flex-sm-row">
                                            <div class="user-location mt-2 mt-sm-0 ml-0">
                                                <i class="iconfont icofont-location-pin mr-1"></i>
                                                <?php echo $profileInfo['location']; ?>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                    if (strlen($profileInfo['fb_page']) != 0) {

                                        ?>
                                        <div class="addon-line d-flex align-content-center flex-column flex-sm-row">
                                            <div class="user-link mt-2 mt-sm-0 ml-0">
                                                <i class="iconfont icofont-link mr-1"></i>
                                                <a target="_blank" rel="nofollow"
                                                   href="/go?to=<?php echo $profileInfo['fb_page']; ?>"><?php echo $profileInfo['fb_page']; ?></a>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                    if (strlen($profileInfo['instagram_page']) != 0) {

                                        ?>
                                        <div class="addon-line d-flex align-content-center flex-column flex-sm-row">
                                            <div class="user-link mt-2 mt-sm-0 ml-0">
                                                <i class="iconfont icofont-link mr-1"></i>
                                                <a target="_blank" rel="nofollow"
                                                   href="/go?to=<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                    if (strlen($profileInfo['status']) != 0) {

                                        ?>
                                        <div class="addon-line d-flex align-content-center flex-column flex-sm-row">
                                            <div class="user-bio mt-2 mt-sm-0 ml-0">
                                                <i class="iconfont icofont-quote-right mr-1"></i>
                                                <span><?php echo $profileInfo['status']; ?></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }

                                ?>

                            </div>
                        </div>
                        <?php
                    }
                ?>

                <?php

                    if ($profileInfo['id'] != auth::getCurrentUserId() && $profileInfo['allowShowMyInfo'] == 1) {

                        ?>
                        <div class="card " id="preview-posts-block">
                            <div class="card-header border-0">
                                <h3 class="card-title">
                                    <i class="icofont icofont-newspaper mr-2"></i>
                                    <span class="counter-button-title"><?php echo $LANG['page-posts']; ?> <span id="stat_posts_count" class="counter-button-indicator"><?php echo $profileInfo['postsCount']; ?></span></span>
                                </h3>
                            </div>
                        </div>
                        <?php
                    }
                ?>

                <?php

                    if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyFriends'] == 0) {

                        if ($profileInfo['friendsCount'] != 0) {

                            ?>
                                <div class="card" id="preview-people-block">
                                    <div class="card-header border-0">
                                        <h3 class="card-title"><i class="icofont icofont-users-alt-4 mr-2"></i>
                                            <span class="counter-button-title"><?php echo $LANG['page-friends']; ?>
                                                <span id="stat_friends_count" class="counter-button-indicator"><?php if ($profileInfo['friendsCount'] > 0) echo $profileInfo['friendsCount']; ?></span>
                                            </span>
                                        </h3>
                                        <span class="action-link">
                                            <?php

                                            if ($myPage) {

                                                ?>
                                                <a href="/account/friends"><?php echo $LANG['action-show-all']; ?></a>
                                                <?php

                                            } else {

                                                ?>
                                                <a href="/<?php echo $profileInfo['username']; ?>/friends"><?php echo $LANG['action-show-all']; ?></a>
                                                <?php
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div class="card-body p-2">
                                        <div class="grid-list row">

                                            <?php

                                            $friends = new friends($dbo, $profileInfo['id']);
                                            $result = $friends->get(0, 6);

                                            foreach ($result['items'] as $key => $value) {

                                                draw::previewPeopleItem($value, $LANG, $helper);
                                            }
                                            ?>

                                        </div>
                                    </div>

                                </div>
                            <?php
                        }
                    }
                ?>

                <?php

                    if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyGallery'] == 0) {

                        if ($profileInfo['galleryItemsCount'] != 0) {

                            ?>
                            <div class="card " id="preview-gallery-block">
                                <div class="card-header border-0">
                                    <h3 class="card-title"><i class="icofont icofont-image mr-2"></i>
                                        <span class="counter-button-title"><?php echo $LANG['label-photos']; ?>
                                            <span id="stat_photos_count" class="counter-button-indicator"><?php if ($profileInfo['galleryItemsCount'] > 0) echo $profileInfo['galleryItemsCount']; ?></span>
                                        </span>
                                    </h3>
                                    <span class="action-link">
                                            <?php

                                            if ($myPage) {

                                                ?>
                                                <a href="/account/gallery"><?php echo $LANG['action-show-all']; ?></a>
                                                <?php

                                            } else {

                                                ?>
                                                <a href="/<?php echo $profileInfo['username']; ?>/gallery"><?php echo $LANG['action-show-all']; ?></a>
                                                <?php
                                            }
                                            ?>
                                        </span>
                                </div>

                                <div class="card-body p-2">
                                    <div class="grid-list row">

                                        <?php

                                        $gallery = new gallery($dbo);
                                        $gallery->setRequestFrom($profileInfo['id']);
                                        $result = $gallery->get($profileInfo['id'], -1, 0, 6);

                                        foreach ($result['items'] as $key => $value) {

                                            draw::galleryItem($value, $profileInfo, $LANG, $helper, true);
                                        }
                                        ?>

                                    </div>
                                </div>

                            </div>
                            <?php
                        }
                    }
                ?>

                <?php

                    if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyGifts'] == 0) {

                        if ($profileInfo['giftsCount'] != 0) {

                            ?>
                            <div class="card " id="preview-gifts-block">
                                <div class="card-header border-0">
                                    <h3 class="card-title"><i class="icofont icofont-gift mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-gifts']; ?> <span id="stat_gifts_count" class="counter-button-indicator"><?php if ($profileInfo['giftsCount'] > 0) echo $profileInfo['giftsCount']; ?></span></span></h3>
                                    <span class="action-link">
                                        <a href="/<?php echo $profileInfo['username']; ?>/gifts"><?php echo $LANG['action-show-all']; ?></a>
                                    </span>
                                </div>

                                <div class="card-body p-2">
                                    <div class="grid-list row">

                                        <?php

                                        $gifts = new gift($dbo);
                                        $gifts->setRequestFrom($profileInfo['id']);
                                        $result = $gifts->get($profileInfo['id'], 0, 6);

                                        foreach ($result['items'] as $key => $value) {

                                            draw::previewGiftItem($value, $profileInfo);
                                        }
                                        ?>

                                    </div>
                                </div>

                            </div>
                            <?php

                        }
                    }
                ?>

            </div>

            <div class="column-content-items col-md-8">

                <?php

                if ($myPage) {

                    ?>

                    <?php
                        include_once("../html/common/postform.inc.php");
                    ?>

                    <?php
                }
                ?>

                <div class="content-list-page section posts-list-page" style="margin: 0; padding: 0">

                    <?php

                    $result = $wall->get($profileInfo['id'], 0, $accessMode);

                    $posts_loaded = count($result['posts']);

                    if ($posts_loaded != 0) {

                        ?>

                        <div class="items-list content-list">

                            <?php

                            foreach ($result['posts'] as $key => $value) {

                                draw::post($value, $LANG, $helper, false);
                            }

                            ?>

                        </div>

                        <?php

                    } else {

                        ?>

                        <?php

                        $text = $LANG['label-empty-wall'];

                        if ( $myPage ) {

                            $text = $LANG['label-empty-my-wall'];
                        }

                        ?>

                            <div class="card information-banner">
                                 <div class="card-header">
                                     <div class="card-body">
                                         <h5 class="m-0"><?php echo $text; ?></h5>
                                     </div>
                                 </div>
                            </div>

                        <?php
                    }
                    ?>

                    <?php

                    if ($posts_all > 20) {

                        ?>

                        <header class="top-banner loading-banner">

                            <div class="prompt">
                                <button onclick="Items.more('/<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                            </div>

                        </header>

                        <?php
                    }
                    ?>


                </div>

            </div>

        </div>

    </div>

    <?php

        if (!$myPage && auth::getCurrentUserId() != 0) {

            ?>

            <div class="modal modal-form fade profile-block-dlg" id="profile-block-dlg" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <form id="profile-block-form" action="/api/v2/method/blacklist.add" method="post">

                            <input type="hidden" name="accessToken" value="<?php echo auth::getAccessToken(); ?>">
                            <input type="hidden" name="accountId" value="<?php echo auth::getCurrentUserId(); ?>">

                            <input type="hidden" name="profileId" value="<?php echo $profileInfo['id']; ?>">
                            <input type="hidden" name="reason" value="">

                            <div class="modal-header">
                                <h5 class="modal-title placeholder-title"><?php echo $LANG['dlg-confirm-block-title']; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"></span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="error-summary alert alert-warning"><?php echo sprintf($LANG['msg-block-user-text'], "<strong>".$profileInfo['fullname']."</strong>", "<strong>".$profileInfo['fullname']."</strong>"); ?></div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-no']; ?></button>
                                <button type="button"  onclick="Profile.block('<?php echo $profileInfo['id']; ?>'); return false;" data-dismiss="modal" class="btn btn-primary"><?php echo $LANG['action-yes']; ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <?php
        }
    ?>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>
    <script type="text/javascript" src="/js/draggable_background.js"></script>
    <script type="text/javascript" src="/js/friends.js"></script>

    <script type="text/javascript">

        var inbox_all = <?php echo $posts_all; ?>;
        var inbox_loaded = <?php echo $posts_loaded; ?>;

        var auth_token = "<?php echo auth::getAuthenticityToken(); ?>";

        <?php

            if ($myPage) {

                ?>
                    var myPage = true;
                 <?php

                if (strlen($profileInfo['normalCoverUrl']) != 0) {

                    ?>

                        var CoverExists = true;

                    <?php

                } else {

                    ?>

                        var CoverExists = false;

                    <?php
                }

                if (strlen($profileInfo['bigPhotoUrl']) != 0) {

                    ?>
                    var PhotoExists = true;
                    <?php

                } else {

                    ?>
                    var PhotoExists = false;
                    <?php
                }
            }
        ?>

        var $infobox = $('#info-box');

        $("#cover-upload").fileupload({
            formData: {accountId: <?php echo auth::getCurrentUserId(); ?>, accessToken: "<?php echo auth::getAccessToken(); ?>", imgType: 1},
            name: 'image',
            url: "/api/" + options.api_version + "/method/profile.uploadImg",
            dropZone:  '',
            dataType: 'json',
            singleFileUploads: true,
            multiple: false,
            maxNumberOfFiles: 1,
            maxFileSize: constants.MAX_FILE_SIZE,
            acceptFileTypes: "", // or regex: /(jpeg)|(jpg)|(png)$/i
            "files":null,
            minFileSize: null,
            messages: {
                "maxNumberOfFiles": "Maximum number of files exceeded",
                "acceptFileTypes": "File type not allowed",
                "maxFileSize": "File is too big",
                "minFileSize": "File is too small"},
            process: true,
            start: function (e, data) {

                console.log("start");

                $('div.profile-cover-progress').css("display", "block");
                $('div.profile-cover-loader').addClass('hidden');
                $('div.cover-upload-button').addClass('hidden');
                $('div.profile-cover-img').addClass('hidden');
            },
            processfail: function(e, data) {

                console.log("processfail");

                if (data.files.error) {

                    $infobox.find('#info-box-message').text(data.files[0].error);
                    $infobox.modal('show');
                }
            },
            progressall: function (e, data) {

                console.log("progressall");

                var progress = parseInt(data.loaded / data.total * 100, 10);

                $('div.profile-cover-progress').find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
            },
            done: function (e, data) {

                console.log("done");

                var result = jQuery.parseJSON(data.jqXHR.responseText);

                if (result.hasOwnProperty('error')) {

                    if (result.error === false) {

                        if (result.hasOwnProperty('normalCoverUrl')) {

                            $("div.profile-cover-img").css("background-image", "url(" + result.normalCoverUrl + ")");
                        }

                    } else {

                        $infobox.find('#info-box-message').text(result.error_description);
                        $infobox.modal('show');
                    }
                }
            },
            fail: function (e, data) {

                console.log("always");
            },
            always: function (e, data) {

                console.log("always");

                $('div.profile-cover-progress').css("display", "none");
                $('div.profile-cover-loader').removeClass('hidden');
                $('div.cover-upload-button').removeClass('hidden');
                $('div.profile-cover-img').removeClass('hidden');
            }

        });

        $("#photo-upload").fileupload({
            formData: {accountId: <?php echo auth::getCurrentUserId(); ?>, accessToken: "<?php echo auth::getAccessToken(); ?>", imgType: 0},
            name: 'image',
            url: "/api/" + options.api_version + "/method/profile.uploadImg",
            dropZone:  '',
            dataType: 'json',
            singleFileUploads: true,
            multiple: false,
            maxNumberOfFiles: 1,
            maxFileSize: constants.MAX_FILE_SIZE,
            acceptFileTypes: "", // or regex: /(jpeg)|(jpg)|(png)$/i
            "files":null,
            minFileSize: null,
            messages: {
                "maxNumberOfFiles":"Maximum number of files exceeded",
                "acceptFileTypes":"File type not allowed",
                "maxFileSize": "File is too big",
                "minFileSize": "File is too small"},
            process: true,
            start: function (e, data) {

                console.log("start");

                $('div.profile-photo-progress').css("display", "block");
                $('div.profile-photo-loader').addClass('hidden');
                $('div.photo-upload-button').addClass('hidden');
                $('a.profile-photo-link').addClass('hidden');

                $("#photo-upload").trigger('start');
            },
            processfail: function(e, data) {

                console.log("processfail");

                if (data.files.error) {

                    $infobox.find('#info-box-message').text(data.files[0].error);
                    $infobox.modal('show');
                }
            },
            progressall: function (e, data) {

                console.log("progressall");

                var progress = parseInt(data.loaded / data.total * 100, 10);

                $('div.profile-photo-progress').find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
            },
            done: function (e, data) {

                console.log("done");

                var result = jQuery.parseJSON(data.jqXHR.responseText);

                if (result.hasOwnProperty('error')) {

                    if (result.error === false) {

                        if (result.hasOwnProperty('lowPhotoUrl')) {

                            $("div.profile-photo").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("span.avatar").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("a.avatar").css("background-image", "url(" + result.lowPhotoUrl + ")");
                            $("a.profile-photo-link").first().attr("href", result.originPhotoUrl);
                        }

                    } else {

                        $infobox.find('#info-box-message').text(result.error_description);
                        $infobox.modal('show');
                    }
                }

                $("#photo-upload").trigger('done');
            },
            fail: function (e, data) {

                console.log(data.errorThrown);
            },
            always: function (e, data) {

                console.log("always");

                $('div.profile-photo-progress').css("display", "none");
                $('div.profile-photo-loader').removeClass('hidden');
                $('div.photo-upload-button').removeClass('hidden');
                $('a.profile-photo-link').removeClass('hidden');

                $("#photo-upload").trigger('always');
            }
        });

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

        window.Profile || ( window.Profile = {} );

        Profile.getGiftsBox = function(username, title) {

            var url = "/" + username + "/select_gifts/?action=get-box";
            $.colorbox({width:"604px", href: url, title: title, top: "50px",});
        };

    </script>


</body
</html>