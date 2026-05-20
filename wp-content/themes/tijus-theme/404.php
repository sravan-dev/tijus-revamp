<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
$theme_uri = get_template_directory_uri();
global $tijus_error_title, $tijus_error_message;
$title = !empty($tijus_error_title) ? $tijus_error_title : 'Page Not Found';
$main_title = !empty($tijus_error_message) ? $tijus_error_message : 'We are very sorry for error. We <span> can’t find this</span> page.';
$desc = !empty($tijus_error_message) ? '' : 'It has survived not only five centuries but also the leap into electronic typesetting.';
?>

<!-- Page Banner Start -->
<div class="section page-banner">

    <img class="shape-1 animation-round" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-8.png" alt="Shape">

    <img class="shape-2" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo home_url(); ?>">Home</a></li>
                <li class="active"><?php echo $title === 'Page Not Found' ? '404 Error' : esc_html($title); ?></li>
            </ul>
            <h2 class="title"><?php echo esc_html($title); ?></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <!-- Shape Icon Box Start -->
    <div class="shape-icon-box">

        <img class="icon-shape-1 animation-left" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-5.png" alt="Shape">

        <div class="box-content">
            <div class="box-wrapper">
                <i class="flaticon-badge"></i>
            </div>
        </div>

        <img class="icon-shape-2" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-6.png" alt="Shape">

    </div>
    <!-- Shape Icon Box End -->

    <img class="shape-3" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-24.png" alt="Shape">

    <img class="shape-author" src="<?php echo $theme_uri; ?>/assets/images/author/author-11.jpg" alt="Shape">

</div>
<!-- Page Banner End -->

<!-- Error Start -->
<div class="section section-padding mt-n10">
    <div class="container">

        <!-- Error Wrapper Start -->
        <div class="error-wrapper">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Error Images Start -->
                    <div class="error-images">
                        <img src="<?php echo $theme_uri; ?>/assets/images/error.png" alt="Error">
                    </div>
                    <!-- Error Images End -->
                </div>
                <div class="col-lg-6">
                    <!-- Error Content Start -->
                    <div class="error-content">
                        <h5 class="sub-title"><?php echo $title === 'Page Not Found' ? 'This Page is Not Found.' : 'Error'; ?></h5>
                        <h2 class="main-title"><?php echo wp_kses_post($main_title); ?></h2>
                        <?php if ($desc) : ?><p><?php echo esc_html($desc); ?></p><?php endif; ?>
                        <a href="<?php echo home_url(); ?>" class="btn btn-primary btn-hover-dark">Back To Home</a>
                    </div>
                    <!-- Error Content End -->
                </div>
            </div>
        </div>
        <!-- Error Wrapper End -->

    </div>
</div>
<!-- Error End -->

<!-- Download App Start -->
<div class="section section-padding download-section">

    <div class="app-shape-1"></div>
    <div class="app-shape-2"></div>
    <div class="app-shape-3"></div>
    <div class="app-shape-4"></div>

    <div class="container">

        <!-- Download App Wrapper Start -->
        <div class="download-app-wrapper mt-n6">

            <!-- Section Title Start -->
            <div class="section-title section-title-white">
                <h5 class="sub-title">Ready to start?</h5>
                <h2 class="main-title">Download our mobile app. for easy to start your course.</h2>
            </div>
            <!-- Section Title End -->

            <img class="shape-1 animation-right" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-14.png" alt="Shape">

            <!-- Download App Button End -->
            <div class="download-app-btn">
                <ul class="app-btn">
                    <li><a href="#"><img src="<?php echo $theme_uri; ?>/assets/images/google-play.png" alt="Google Play"></a></li>
                    <li><a href="#"><img src="<?php echo $theme_uri; ?>/assets/images/app-store.png" alt="App Store"></a></li>
                </ul>
            </div>
            <!-- Download App Button End -->

        </div>
        <!-- Download App Wrapper End -->

    </div>
</div>
<!-- Download App End -->

<?php
get_footer();
