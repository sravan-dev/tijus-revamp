<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">

    <!-- CSS
	============================================ -->

    <!-- Google Fonts CSS -->  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icon Font CSS -->
    <!-- <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/icofont.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/flaticon.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/font-awesome.min.css"> -->

    <!-- Plugins CSS -->
    <!-- <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/animate.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/nice-select.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/apexcharts.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/plugins/jqvmap.min.css"> -->

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/vendor/plugins.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <div class="main-wrapper">

        <!-- Header Section Start -->
        <div class="header-section">

            <!-- Header Top Start -->
            <div class="header-top d-none d-lg-block">
                <div class="container">

                    <!-- Header Top Wrapper Start -->
                    <div class="header-top-wrapper">

                        <div class="header-top-left">
                            <?php
                            $ann_text = get_theme_mod( 'tijus_announcement_text', "Enroll now to get 50% off on all courses" );
                            $ann_link = get_theme_mod( 'tijus_announcement_link', '#' );
                            ?>
                            <p><?php echo wp_kses_post( $ann_text ); ?> <a href="<?php echo esc_url( $ann_link ); ?>"></a></p>
                        </div>
                        <!-- Header Top Left End -->

                        <!-- Header Top Medal Start -->
                        <div class="header-top-medal">
                            <?php
                            $phone = get_theme_mod( 'tijus_phone', '(970) 262-1413' );
                            $email = get_theme_mod( 'tijus_email', 'address@gmail.com' );
                            $phone_raw = preg_replace( '/[^0-9+]/', '', $phone );
                            ?>
                            <div class="top-info">
                                <p><i class="flaticon-phone-call"></i> <a href="tel:<?php echo esc_attr( $phone_raw ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                                <p><i class="flaticon-email"></i> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
                            </div>
                        </div>
                        <!-- Header Top Medal End -->

                        <!-- Header Top Right Start -->
                        <?php
                        $fb  = get_theme_mod( 'tijus_facebook_url',  '#' );
                        $tw  = get_theme_mod( 'tijus_twitter_url',   '#' );
                        $sk  = get_theme_mod( 'tijus_skype_url',     '#' );
                        $ig  = get_theme_mod( 'tijus_instagram_url', '#' );
                        ?>
                        <div class="header-top-right">
                            <ul class="social">
                                <?php foreach ( tijus_get_social_links() as $_sl ) : ?>
                                <li><a href="<?php echo esc_url( $_sl['url'] ); ?>"><i class="<?php echo esc_attr( $_sl['icon'] ); ?>"></i></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <!-- Header Top Right End -->

                    </div>
                    <!-- Header Top Wrapper End -->

                </div>
            </div>
            <!-- Header Top End -->

            <!-- Header Main Start -->
            <div class="header-main">
                <div class="container">

                    <!-- Header Main Start -->
                    <div class="header-main-wrapper">

                        <!-- Header Logo Start -->
                        <div class="header-logo">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo tijus_get_logo_url(); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"<?php
                                $logo_w = get_theme_mod( 'tijus_logo_width', '' );
                                $logo_h = get_theme_mod( 'tijus_logo_height', '' );
                                if ( $logo_w ) echo ' width="' . absint( $logo_w ) . '"';
                                if ( $logo_h ) echo ' height="' . absint( $logo_h ) . '"';
                            ?>></a>
                        </div>
                        <!-- Header Logo End -->

                        <!-- Header Menu Start -->
                        <div class="header-menu d-none d-lg-block">
                            <?php
                            if ( has_nav_menu( 'tijus-menu' ) ) {
                                wp_nav_menu( array(
                                    'theme_location' => 'tijus-menu',
                                    'menu_class'     => 'nav-menu',
                                    'container'      => false,
                                ) );
                            } else {
                                echo '<ul class="nav-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( admin_url('nav-menus.php') ) . '">Set Menu</a></li></ul>';
                            }
                            ?>

                        </div>
                        <!-- Header Menu End -->

                        <!-- Header Sing In & Up Start -->
                        <div class="header-sign-in-up d-none d-lg-block">
                            <?php
                            if ( has_nav_menu( 'header-right' ) ) {
                                wp_nav_menu( array(
                                    'theme_location' => 'header-right',
                                    'container'      => false,
                                    'menu_class'     => '',
                                ) );
                            } else {
                                echo '<ul><li><a class="sign-in" href="' . esc_url( wp_login_url() ) . '">Sign In</a></li><li><a class="sign-up" href="' . esc_url( wp_registration_url() ) . '">Sign Up</a></li></ul>';
                            }
                            ?>
                        </div>
                        <!-- Header Sing In & Up End -->

                        <!-- Header Mobile Toggle Start -->
                        <div class="header-toggle d-lg-none">
                            <a class="menu-toggle" href="javascript:void(0)">
                                <span></span>
                                <span></span>
                                <span></span>
                            </a>
                        </div>
                        <!-- Header Mobile Toggle End -->

                    </div>
                    <!-- Header Main End -->

                </div>
            </div>
            <!-- Header Main End -->

        </div>
        <!-- Header Section End -->

        <!-- Mobile Menu Start -->
        <div class="mobile-menu">

            <!-- Menu Close Start -->
            <a class="menu-close" href="javascript:void(0)">
                <i class="icofont-close-line"></i>
            </a>
            <!-- Menu Close End -->

            <!-- Mobile Top Medal Start -->
            <div class="mobile-top">
                <p><i class="flaticon-phone-call"></i> <a href="tel:<?php echo esc_attr( $phone_raw ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                <p><i class="flaticon-email"></i> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
            </div>
            <!-- Mobile Top Medal End -->

            <!-- Mobile Sing In & Up Start -->
            <div class="mobile-sign-in-up">
                <?php
                if ( has_nav_menu( 'header-right' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'header-right',
                        'container'      => false,
                        'menu_class'     => '',
                    ) );
                } else {
                    echo '<ul><li><a class="sign-in" href="' . esc_url( wp_login_url() ) . '">Sign In</a></li><li><a class="sign-up" href="' . esc_url( wp_registration_url() ) . '">Sign Up</a></li></ul>';
                }
                ?>
            </div>
            <!-- Mobile Sing In & Up End -->

            <!-- Mobile Menu Start -->
            <div class="mobile-menu-items">
                <?php
                if ( has_nav_menu( 'tijus-menu' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'tijus-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                    ) );
                } else {
                    echo '<ul class="nav-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( admin_url('nav-menus.php') ) . '">Assign Menu here</a></li></ul>';
                }
                ?>
            </div>
            <!-- Mobile Menu End -->

            <!-- Mobile Menu End -->
            <div class="mobile-social">
                <ul class="social">
                    <?php foreach ( tijus_get_social_links() as $_sl ) : ?>
                    <li><a href="<?php echo esc_url( $_sl['url'] ); ?>"><i class="<?php echo esc_attr( $_sl['icon'] ); ?>"></i></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Mobile Menu End -->

        </div>
        <!-- Mobile Menu End -->

        <!-- Overlay Start -->
        <div class="overlay"></div>
        <!-- Overlay End -->

