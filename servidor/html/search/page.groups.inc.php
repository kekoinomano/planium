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

    $query = '';

    $search = new search($dbo);
    $search->setRequestFrom(auth::getCurrentUserId());

    $items_all = 0;
    $items_loaded = 0;

    if (isset($_GET['query'])) {

        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $query = helper::clearText($query);
        $query = helper::escapeText($query);
    }

    if (!empty($_POST)) {

        $userId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;
        $query = isset($_POST['query']) ? $_POST['query'] : '';

        $userId = helper::clearInt($userId);
        $loaded = helper::clearInt($loaded);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        $result = $search->communitiesQuery($query, $userId);

        $items_loaded = count($result['items']);
        $items_all = $result['itemsCount'];


        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::communityItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();


            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Search.communitiesMore('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "search_groups";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-search-communities']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="page-search-groups">


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

                            <div class="standard-page page-title-content">
                                <div class="page-title-content-inner">
                                    <?php echo $LANG['page-search-communities']; ?>
                                </div>
                                <div class="page-title-content-bottom-inner">
                                    <?php echo $LANG['tab-search-communities-description']; ?>
                                </div>
                            </div>

                            <div class="standard-page tabs-content bordered">
                                <div class="tab-container">
                                    <nav class="tabs">
                                        <a href="/search/name"><span class="tab"><?php echo $LANG['tab-search-users']; ?></span></a>
                                        <a href="/search/groups"><span class="tab active"><?php echo $LANG['tab-search-communities']; ?></span></a>
                                        <a href="/search/hashtag"><span class="tab"><?php echo $LANG['tab-search-hashtags']; ?></span></a>
                                        <a href="/search/market"><span class="tab"><?php echo $LANG['page-market']; ?></span></a>
                                    </nav>
                                </div>
                            </div>

                            <div class="standard-page">
                                <form class="search-container" method="get" action="/search/groups">
                                    <div class="search-editbox-line">
                                        <input class="search-field" name="query" id="query" placeholder="<?php echo $LANG['search-editbox-placeholder']; ?>" autocomplete="off" type="text" autocorrect="off" autocapitalize="off" style="outline: none;" value="<?php echo $query; ?>">
                                        <button type="submit" class="btn btn-main blue"><?php echo $LANG['search-filters-action-search']; ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="content-list-page">

                            <?php

                            if (strlen($query) > 0) {

                                $result = $search->communitiesQuery($query, 0);

                            } else {

                                $result = $search->communitiesPreload(0);
                            }

                            $items_all = $result['itemsCount'];
                            $items_loaded = count($result['items']);

                            if (strlen($query) > 0) {

                                ?>

                                <div class="card">

                                    <header class="top-banner">

                                        <div class="info">
                                            <h1><?php echo $LANG['label-search-result']; ?> (<?php echo $items_all; ?>)</h1>
                                        </div>

                                    </header>
                                </div>

                                <?php
                            }

                            if ($items_loaded != 0) {

                                ?>

                                <div class="grid-list row" style="margin-right: -10px; margin-left: -10px;">

                                    <?php

                                        foreach ($result['items'] as $key => $value) {

                                            draw::communityItem($value, $LANG, $helper);
                                        }

                                    ?>
                                </div>

                                <?php
                            }
                            ?>

                            <?php

                            if ($items_all > 20) {

                                ?>

                                <header class="top-banner loading-banner">

                                    <div class="prompt">
                                        <button onclick="Search.communitiesMore('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                    </div>

                                </header>

                                <?php
                            }
                            ?>

                        </div>

                    </div>

                </div>
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
        var query = "<?php echo $query; ?>";

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>
