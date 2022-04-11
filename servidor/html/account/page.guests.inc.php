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

    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastGuestsView();
    unset($account);

    $guests = new guests($dbo, auth::getCurrentUserId());
    $guests->setRequestFrom(auth::getCurrentUserId());

    $items_all = $guests->count();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $act = isset($_POST['act']) ? $_POST['act'] : '';
        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        if ($act === 'clear' && $access_token === auth::getAccessToken()) {

            $guests->clear();

            exit;
        }

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $guests->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::guestItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();


            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "guests";

    $css_files = array("main.css", "tipsy.css", "my.css");
    $page_title = $LANG['page-guests']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="guests-page">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <?php
                include_once("../html/common/sidemenu.inc.php");
            ?>

            <div class="row main-page-column">

                <div class="col-md-12">

                    <div class="main-content">

                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <?php echo $LANG['page-guests']; ?>

                                    <?php

                                    if ($items_all > 0) {

                                        ?>
                                        <div class="page-title-content-extra">
                                            <a class="extra-button button blue" href="javascript:void(0)" onclick="Guests.clear('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo$LANG['action-clear-all']; ?></a>
                                        </div>
                                        <?php
                                    }

                                    ?>

                                </h3>
                                <h5 class="card-description"><?php echo $LANG['label-guests-sub-title']; ?></h5>
                            </div>
                        </div>

                        <div class="content-list-page ">

                            <?php

                            $result = $guests->get(0);

                            $items_loaded = count($result['items']);

                            if ($items_loaded != 0) {

                                ?>

                                        <div class="content-list grid-list row">

                                                <?php

                                                    foreach ($result['items'] as $key => $value) {

                                                        draw::guestItem($value, $LANG, $helper);
                                                    }
                                                ?>

                                        </div>

                                <?php

                            } else {

                                ?>

                                <div class="card information-banner">
                                    <div class="card-header">
                                        <div class="card-body">
                                            <h5 class="m-0"><?php echo $LANG['label-empty-list']; ?></h5>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            }
                            ?>

                            <?php

                                if ($items_all > 20) {

                                    ?>

                                    <header class="top-banner loading-banner">

                                        <div class="prompt">
                                            <button onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                        </div>

                                    </header>

                                    <?php
                                }
                            ?>


                        </div>

                    </div>

                </div> <!--   end m12         -->

            </div>
        </div>

    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

    <script type="text/javascript">

        var items_all = <?php echo $items_all; ?>;
        var items_loaded = <?php echo $items_loaded; ?>;

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>