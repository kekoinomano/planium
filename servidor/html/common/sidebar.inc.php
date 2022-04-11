<aside class="sidebar-column" style="">

    <?php

        if (auth::getCurrentUserId() != 0) {

            ?>

            <div class="card " id="preview-people-block">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="icofont icofont-ui-user mr-2"></i><span class="counter-button-title"><?php echo $LANG['tab-search-users']; ?></span></h3>
                    <span class="action-link">
                        <a href="/search/name"><?php echo $LANG['action-show-all']; ?></a>
                    </span>
                </div>

                <div class="card-body p-2">
                    <div class="grid-list row">

                        <?php

                        $search = new search($dbo);

                        $result = $search->preload(0, -1, -1, 1, 9);

                        foreach ($result['items'] as $key => $value) {

                            draw::previewPeopleItem($value, $LANG, $helper);
                        }

                        unset($search);
                        ?>

                    </div>
                </div>
            </div>

            <?php

                if (isset($page_id) && $page_id != "search_groups" && $page_id != "my_groups" && $page_id != "managed_groups") {

                    ?>
                        <div class="card " id="preview-groups-block">
                            <div class="card-header border-0">
                                <h3 class="card-title"><i class="icofont icofont-group mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-groups']; ?></span></h3>
                                <span class="action-link">
                            <a href="/search/groups"><?php echo $LANG['action-show-all']; ?></a>
                        </span>
                            </div>

                            <div class="card-body p-2">
                                <div class="grid-list row">

                                    <?php

                                    $search = new search($dbo);

                                    $result = $search->communitiesPreload(0, 6);

                                    foreach ($result['items'] as $key => $value) {

                                        draw::communityItemPreview($value, $LANG, $helper);
                                    }

                                    unset($search);
                                    ?>

                                </div>
                            </div>
                        </div>
                    <?php
                }
            ?>

            <?php

        } else {

            ?>

            <!--    You ad banner here-->

            <div class="sidebar-block">

                <!--        Link and image to you ad-->
                <!--        Image size recommended 500x237-->

                <a href="https://codecanyon.net/user/qascript/portfolio?ref=qascript" target="_blank">
                    <img alt="Jobs-logos" src="/img/test_img.png">
                </a>

            </div>

            <div class="item-list sidebar-block transparent">

                <header class="item-list-header">
                    <h3>You Links Here</h3>
                </header>

                <ul>
                    <li class="item-li">
                        <a href="https://codecanyon.net/user/qascript/portfolio?ref=qascript" class="custom-item-link" target="_blank">
                            <div class="superline">
                                <span class="organization">View My Portfolio on Codecanyon</span>
                            </div>
                            <h4>Codecanyon</h4>
                        </a>
                    </li>

                    <li class="item-li">
                        <a href="https://codecanyon.net/item/my-social-network-app-and-website/13965025?ref=qascript" class="custom-item-link" target="_blank">
                            <div class="superline">
                                <span class="organization">By this App on Codecanyon</span>
                            </div>
                            <h4>Codecanyon</h4>
                        </a>
                    </li>

                    <li class="item-li">
                        <a href="https://www.facebook.com/qascript" class="custom-item-link" target="_blank">
                            <div class="superline">
                                <span class="organization">My Facebook Page</span>
                            </div>
                            <h4>Facebook</h4>
                        </a>
                    </li>

            </div>

            <?php
        }
    ?>

</aside>