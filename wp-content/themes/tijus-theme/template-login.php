<?php
/* Template Name: Login Page */

get_header();
$theme_uri = get_template_directory_uri();
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
                <li class="active">Login</li>
            </ul>
            <h2 class="title">Login <span>Form</span></h2>
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

<!-- Register & Login Start -->
<div class="section section-padding">
    <div class="container">

        <!-- Register & Login Wrapper Start -->
        <div class="register-login-wrapper">
            <div class="row align-items-center">
                <div class="col-lg-6">

                    <!-- Register & Login Images Start -->
                    <div class="register-login-images">
                        <div class="shape-1">
                            <img src="<?php echo $theme_uri; ?>/assets/images/shape/shape-26.png" alt="Shape">
                        </div>
                        <div class="images">
                            <img src="<?php echo $theme_uri; ?>/assets/images/register-login.png" alt="Register Login">
                        </div>
                    </div>
                    <!-- Register & Login Images End -->

                </div>
                <div class="col-lg-6">

                    <!-- Register & Login Form Start -->
                    <div class="register-login-form">
                        <h3 class="title">Login <span>Now</span></h3>

                        <div class="form-wrapper">
                            <?php if ( is_user_logged_in() ) : ?>
                                <p style="margin-bottom: 20px; font-weight: 500; color: #309255;">You are already logged in to your account.</p>
                                <a href="<?php echo home_url('/dashboard/'); ?>" class="btn btn-primary btn-hover-dark w-100 mb-3">Go to Dashboard</a>
                                <a href="<?php echo wp_logout_url( home_url() ); ?>" class="btn btn-secondary btn-outline w-100">Log Out</a>
                            <?php else : ?>
                            <form id="tijus-login-form">
                                <?php wp_nonce_field('tijus_login_nonce_action', 'tijus_login_nonce'); ?>
                                
                                <div class="single-form">
                                    <input type="email" name="log_email" placeholder="Email Address" required>
                                </div>
                                <div class="single-form">
                                    <input type="password" name="log_password" placeholder="Password" required>
                                </div>
                                
                                <p id="tijus-login-message" style="display: none; margin-top: 15px; font-size: 14px;"></p>

                                <div class="single-form">
                                    <button type="submit" class="btn btn-primary btn-hover-dark w-100" id="tijus-login-btn">Login</button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Register & Login Form End -->

                </div>
            </div>
        </div>
        <!-- Register & Login Wrapper End -->

    </div>
</div>
<!-- Register & Login End -->

<script>
jQuery(document).ready(function($) {
    $('#tijus-login-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#tijus-login-btn');
        var $msg = $('#tijus-login-message');
        
        $btn.text('Logging in...').prop('disabled', true);
        $msg.hide().removeClass('text-danger text-success');
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: $form.serialize() + '&action=tijus_ajax_login',
            success: function(response) {
                if (response.success) {
                    $msg.addClass('text-success').text(response.data).show();
                    window.location.href = '<?php echo home_url(); ?>';
                } else {
                    $msg.addClass('text-danger').text(response.data).show();
                    $btn.text('Login').prop('disabled', false);
                }
            },
            error: function() {
                $msg.addClass('text-danger').text('A server error occurred. Please try again.').show();
                $btn.text('Login').prop('disabled', false);
            }
        });
    });
});
</script>

<?php get_footer(); ?>
