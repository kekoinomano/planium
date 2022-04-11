<?php

/*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk, https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */


    if (auth::isSession()) {

        ?>

        <div class="top-header">
			<div class="container">

				<div class="d-flex">

					<a class="logo" href="/">
						<img class="header-brand-img" src="/public/img/logo.png" alt="<?php echo APP_NAME; ?>" title="<?php echo APP_TITLE; ?>">
					</a>

                    <?php

                        if (isset($page_id) && $page_id !== "search" && $page_id !== "search_groups" && $page_id !== "hashtags") {

                            ?>
                            <form class="navbar-form navbar-left d-none d-md-block col-4 col-lg-4" action="/search/name">

                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo $LANG['nav-search']; ?>" name="query">
                                    <button type="submit" class="btn btn-secondary">
                                        <span class="icofont icofont-search"></span>
                                    </button>
                                </div>

                            </form>
                            <?php
                        }
                    ?>


							<div class="d-flex align-items-center order-lg-2 ml-auto">

                                <a class="nav-link py-2 icon" href="/account/friends">
                                    <i class="icofont icofont-users"></i>
                                    <span class="nav-unread hidden friends-badge"></span>
                                </a>

                                <a class="nav-link py-2 icon" href="/account/messages">
                                    <i class="icofont icofont-ui-message"></i>
                                    <span class="nav-unread hidden messages-badge"></span>
                                </a>

                                <a class="nav-link py-2 icon" href="/account/notifications">
                                    <i class="icofont icofont-notification"></i>
                                    <span class="nav-unread hidden notifications-badge"></span>
                                </a>

								<div class="dropdown">
									<a href="/<?php echo auth::getCurrentUserUsername(); ?>" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                                        <span class="avatar" style="background-image: url(<?php echo auth::getCurrentUserPhotoUrl(); ?>); background-position: center; background-size: cover;"></span>
										<span class="ml-2 d-none d-lg-block profile-menu-nav-link">
											<span class="text-default"><?php echo auth::getCurrentUserFullname(); ?></span>
											<small class="text-muted d-block">@<?php echo auth::getCurrentUserUsername(); ?></small>
										</span>
									</a>
									<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
										<a class="dropdown-item" href="/<?php echo auth::getCurrentUserLogin(); ?>"><i class="dropdown-icon icofont icofont-ui-user"></i><?php echo $LANG['nav-profile']; ?></a>
                                        <a class="dropdown-item d-block d-md-none" href="/<?php echo auth::getCurrentUserLogin(); ?>/gallery">
                                            <i class="dropdown-icon icofont icofont-ui-image"></i><?php echo $LANG['page-gallery']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/account/wall">
                                            <i class="dropdown-icon icofont icofont-newspaper"></i><?php echo $LANG['page-wall']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/account/stream">
                                            <i class="dropdown-icon icofont icofont-world"></i><?php echo $LANG['page-stream']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/account/groups">
                                            <i class="dropdown-icon icofont icofont-group"></i><?php echo $LANG['page-communities']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/account/favorites">
                                            <i class="dropdown-icon icofont icofont-heart"></i><?php echo $LANG['page-favorites']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/account/popular">
                                            <i class="dropdown-icon icofont icofont-fire-burn"></i><?php echo $LANG['page-popular']; ?>
                                        </a>

                                        <a class="dropdown-item d-block d-md-none" href="/search/market">
                                            <i class="dropdown-icon icofont icofont-bag"></i><?php echo $LANG['page-market']; ?>
                                        </a>

                                        <a class="dropdown-item d-block d-md-none" href="/account/products">
                                            <i class="dropdown-icon icofont icofont-basket"></i><?php echo $LANG['page-products']; ?>
                                        </a>

                                        <a class="dropdown-item d-block d-md-none" href="/account/guests">
											<span class="float-right">
												<span class="badge badge-primary guests-badge guests-primary-badge"></span>
											</span>
                                            <i class="dropdown-icon icofont icofont-eye"></i><?php echo $LANG['nav-guests']; ?>
                                        </a>

                                        <a class="dropdown-item d-block d-md-none" href="/account/messages">
											<span class="float-right">
												<span class="badge badge-primary messages-badge messages-primary-badge"></span>
											</span>
                                            <i class="dropdown-icon icofont icofont-ui-message"></i><?php echo $LANG['nav-messages']; ?>
                                        </a>

										<a class="dropdown-item d-block d-md-none" href="/account/notifications">
											<span class="float-right">
												<span class="badge badge-primary notifications-badge notifications-primary-badge"></span>
											</span>
											<i class="dropdown-icon icofont icofont-notification"></i><?php echo $LANG['nav-notifications']; ?>
                                        </a>
                                        <a class="dropdown-item d-block d-md-none" href="/search/name"><i class="dropdown-icon icofont icofont-ui-search"></i><?php echo $LANG['nav-search']; ?></a>
										<a class="dropdown-item" href="/account/settings/profile"><i class="dropdown-icon icofont icofont-gear-alt"></i><?php echo $LANG['nav-settings']; ?></a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="/support"><i class="dropdown-icon icofont icofont-support"></i><?php echo $LANG['topbar-support']; ?></a>
										<a class="dropdown-item" href="/logout?access_token=<?php echo auth::getAccessToken(); ?>&amp;continue=/"><i class="dropdown-icon icofont icofont-logout"></i><?php echo $LANG['topbar-logout']; ?></a>
									</div>
								</div>

							</div>

                </div>
			</div>
		</div>

        <?php

    } else {

        ?>

        <div class="top-header">
            <div class="container">
                <div class="d-flex">

                    <a class="logo" href="/">
                        <img class="header-brand-img" src="/public/img/logo.png" alt="<?php echo APP_NAME; ?>>" title="<?php echo APP_TITLE; ?>">
                    </a>


                    <div class="d-flex align-items-center order-lg-2 ml-auto">

                        <?php

                        if (isset($page_id) && $page_id === "main") {

                            ?>

                            <div class="nav-item">
                                <a href="/signup" class="topbar-button" title="">
                                    <span class="new-item d-sm-inline-block"><?php echo $LANG['topbar-signup']; ?></span>
                                </a>
                            </div>

                            <?php

                        } else if (isset($page_id) && $page_id === "signup") {

                            ?>

                            <div class="nav-item">
                                <a href="/" class="topbar-button" title="">
                                    <span class="new-item d-sm-inline-block"><?php echo $LANG['topbar-signin']; ?></span>
                                </a>
                            </div>

                            <?php

                        } else {

                            ?>

                            <div class="nav-item">
                                <a href="/" class="topbar-button" title="">
                                    <span class="new-item d-sm-inline-block"><?php echo $LANG['topbar-signin']; ?></span>
                                </a>
                            </div>

                            <div class="nav-item">
                                <a href="/signup" class="topbar-button" title="">
                                    <span class="new-item d-sm-inline-block"><?php echo $LANG['topbar-signup']; ?></span>
                                </a>
                            </div>

                            <?php
                        }
                        ?>

                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    if (!isset($_COOKIE['privacy'])) {

        if (isset($page_id) && $page_id != 'main') {

            ?>
            <div class="header-message gone">
                <div class="wrap">
                    <p class="message"><?php echo $LANG['label-cookie-message']; ?> <a href="/terms"><?php echo $LANG['page-terms']; ?></a></p>
                </div>

                <button class="close-message-button close-privacy-message">×</button>
            </div>
            <?php
        }
    }
?>