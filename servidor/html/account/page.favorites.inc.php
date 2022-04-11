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

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $items_all = $stream->getFavoritesCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->getFavorites($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Items.more('/account/favorites', '<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "favorites";

    $css_files = array("main.css");
    $page_title = $LANG['page-favorites']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="page-favorites">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <?php
                include_once("../html/common/sidemenu.inc.php");
            ?>

            <div class="row main-page-column">

                <div class="col-md-8">

                    <div class="main-content">

                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title"><?php echo $LANG['page-favorites']; ?></h3>
                                <h5 class="card-description"><?php echo $LANG['page-favorites-description']; ?></h5>
                            </div>
                        </div>

                        <div class=" content-list-page posts-list-page posts-list-page-bordered">

                            <?php

                            $result = $stream->getFavorites(0);

                            $items_loaded = count($result['items']);

                            if ($items_loaded != 0) {

                                ?>

                                <div class="items-list content-list">

                                    <?php

                                    foreach ($result['items'] as $key => $value) {

                                        draw::post($value, $LANG, $helper);
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
                                        <button onclick="Items.more('/account/favorites', '<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                    </div>

                                </header>

                                <?php
                            }
                            ?>


                        </div>

                    </div>

                </div>

                <div class="col-md-4 right-sidebar-column">

                    <?php

                        include_once("../html/common/sidebar.inc.php");
                    ?>

                </div>

            </div>
        </div>

    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

    <script type="text/javascript">

        var inbox_all = <?php echo $items_all; ?>;
        var inbox_loaded = <?php echo $items_loaded; ?>;

    </script>


</body
</html>