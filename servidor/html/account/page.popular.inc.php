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

    $popular = new popular($dbo);
    $popular->setRequestFrom(auth::getCurrentUserId());

    $page_id = "popular";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-popular']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="page-popular">


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
                                <h3 class="card-title"><?php echo $LANG['page-popular']; ?></h3>
                                <h5 class="card-description"><?php echo $LANG['page-popular-description']; ?></h5>
                            </div>
                        </div>

                        <div class="content-list-page posts-list-page posts-list-page-bordered">

                            <?php

                            $result = $popular->get(0, 0);

                            $inbox_loaded = count($result['items']);

                            if ($inbox_loaded != 0) {

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

                        </div>

                    </div>

                </div>

                <div class="col-md-4 right-sidebar-column">

                    <?php

                    include_once("../html/common/sidebar.inc.php");
                    ?>

                </div> <!--   end m4         -->

            </div>

        </div>
    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

    <script type="text/javascript">

        var inbox_loaded = <?php echo $inbox_loaded; ?>;

    </script>


</body
</html>
