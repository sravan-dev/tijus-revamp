<?php
/* Template Name: Register Page */

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
                <li class="active">Register</li>
            </ul>
            <h2 class="title">Registration <span>Form</span></h2>
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
                        <h3 class="title">Registration <span>Now</span></h3>

                        <div class="form-wrapper">
                            <?php if ( is_user_logged_in() ) : ?>
                                <p style="margin-bottom: 20px; font-weight: 500; color: #309255;">You are already logged in to your account.</p>
                                <a href="<?php echo home_url('/dashboard/'); ?>" class="btn btn-primary btn-hover-dark w-100 mb-3">Go to Dashboard</a>
                                <a href="<?php echo wp_logout_url( home_url() ); ?>" class="btn btn-secondary btn-outline w-100">Log Out</a>
                            <?php else : ?>
                            <form id="tijus-register-form">
                                <?php wp_nonce_field('tijus_register_nonce_action', 'tijus_register_nonce'); ?>

                                <div class="single-form">
                                    <input type="text" name="reg_name" placeholder="Full Name" required>
                                </div>
                                <div class="single-form">
                                    <input type="email" name="reg_email" placeholder="Email Address" required>
                                </div>
                                <div class="single-form">
                                    <input type="password" name="reg_password" id="reg_password" placeholder="Password" required>
                                </div>
                                <div class="single-form">
                                    <input type="password" name="reg_password_confirm" id="reg_password_confirm" placeholder="Confirm Password" required>
                                </div>

                                <?php
                                $num1 = rand(1, 9);
                                $num2 = rand(1, 9);
                                if ( ! session_id() ) { @session_start(); }
                                $_SESSION['tijus_captcha_ans'] = $num1 + $num2;
                                ?>
                                <div class="single-form">
                                    <div class="row align-items-center">
                                        <div class="col-sm-5">
                                            <label style="font-weight: 600; color: #212832; margin-bottom: 0;">What is <?php echo $num1; ?> + <?php echo $num2; ?>? *</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="number" name="reg_captcha" placeholder="Answer" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <p id="tijus-register-message" style="display: none; margin-top: 15px; font-size: 14px;"></p>

                                <div class="single-form">
                                    <button type="submit" class="btn btn-primary btn-hover-dark w-100" id="tijus-register-btn">Create an account</button>
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
    $('#tijus-register-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#tijus-register-btn');
        var $msg = $('#tijus-register-message');
        
        var pwd = $('#reg_password').val();
        var cpwd = $('#reg_password_confirm').val();
        
        if (pwd !== cpwd) {
            $msg.addClass('text-danger').text('Passwords do not match!').show();
            return;
        }

        $btn.text('Creating account...').prop('disabled', true);
        $msg.hide().removeClass('text-danger text-success');
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: $form.serialize() + '&action=tijus_ajax_register',
            success: function(response) {
                if (response.success) {
                    $msg.addClass('text-success').text(response.data).show();
                    setTimeout(function() {
                        window.location.href = '<?php echo home_url(); ?>';
                    }, 500);
                } else {
                    $msg.addClass('text-danger').text(response.data).show();
                    $btn.text('Create an account').prop('disabled', false);
                }
            },
            error: function() {
                $msg.addClass('text-danger').text('A server error occurred. Please try again.').show();
                $btn.text('Create an account').prop('disabled', false);
            }
        });
    });
});
</script>

<?php get_footer(); ?>
