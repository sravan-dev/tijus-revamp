<?php
/**
 * Template Name: Contact Page
 *
 * @package tijus-theme
 */

get_header();
?>

<!-- Page Banner Start -->
<div class="section page-banner">

    <img class="shape-1 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-8.png" alt="Shape">

    <img class="shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li class="active"><?php the_title(); ?></li>
            </ul>
            <h2 class="title"><?php the_title(); ?></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <!-- Shape Icon Box Start -->
    <div class="shape-icon-box">

        <img class="icon-shape-1 animation-left" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-5.png" alt="Shape">

        <div class="box-content">
            <div class="box-wrapper">
                <i class="flaticon-badge"></i>
            </div>
        </div>

        <img class="icon-shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-6.png" alt="Shape">

    </div>
    <!-- Shape Icon Box End -->

    <img class="shape-3" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-24.png" alt="Shape">

    <img class="shape-author" src="<?php echo get_template_directory_uri(); ?>/assets/images/author/author-11.jpg" alt="Shape">

</div>
<!-- Page Banner End -->

<!-- Contact Map Start -->
<div class="section section-padding-02">
    <div class="container">

        <!-- Contact Map Wrapper Start -->
        <div class="contact-map-wrapper">
            <iframe id="gmap_canvas" src="https://maps.google.com/maps?q=Mission%20District%2C%20San%20Francisco%2C%20CA%2C%20USA&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
        <!-- Contact Map Wrapper End -->

    </div>
</div>
<!-- Contact Map End -->

<!-- Contact Start -->
<div class="section section-padding">
    <div class="container">

        <!-- Contact Wrapper Start -->
        <div class="contact-wrapper">
            <div class="row align-items-center">
                <div class="col-lg-6">

                    <!-- Contact Info Start -->
                    <div class="contact-info">

                        <img class="shape animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-12.png" alt="Shape">

                        <!-- Single Contact Info Start -->
                        <div class="single-contact-info">
                            <div class="info-icon">
                                <i class="flaticon-phone-call"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="title">Phone No.</h6>
                                <p><a href="tel:9539259910">+91 95392 59910</a></p>
                            </div>
                        </div>
                        <!-- Single Contact Info End -->
                        <!-- Single Contact Info Start -->
                        <div class="single-contact-info">
                            <div class="info-icon">
                                <i class="flaticon-email"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="title">Email Address.</h6>
                                <p><a href="mailto:info@tijusacademy.com">info@tijusacademy.com</a></p>
                            </div>
                        </div>
                        <!-- Single Contact Info End -->
                        <!-- Single Contact Info Start -->
                        <div class="single-contact-info">
                            <div class="info-icon">
                                <i class="flaticon-pin"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="title">Office Address.</h6>
                                <p>Tijus Academy</p>
                            </div>
                        </div>
                        <!-- Single Contact Info End -->
                    </div>
                    <!-- Contact Info End -->

                </div>
                <div class="col-lg-6">

                    <!-- Contact Form Start -->
                    <div class="contact-form">
                        <h3 class="title">Get in Touch <span>With Us</span></h3>

                        <div class="form-wrapper">
                            <form id="contact-form" action="#" method="POST">
                                <?php wp_nonce_field( 'tijus_contact_form', 'contact_nonce' ); ?>
                                <!-- Single Form Start -->
                                <div class="single-form">
                                    <input type="text" name="name" placeholder="Name" required>
                                </div>
                                <!-- Single Form End -->
                                <!-- Single Form Start -->
                                <div class="single-form">
                                    <input type="email" name="email" placeholder="Email" required>
                                </div>
                                <!-- Single Form End -->
                                <!-- Single Form Start -->
                                <div class="single-form">
                                    <input type="text" name="subject" placeholder="Subject" required>
                                </div>
                                <!-- Single Form End -->
                                <!-- Single Form Start -->
                                <div class="single-form">
                                    <textarea name="message" placeholder="Message" required></textarea>
                                </div>
                                <!-- Single Form End -->
                                
                                <!-- Captcha Start -->
                                <?php 
                                $captcha_code = strtoupper(substr(md5(mt_rand()), 0, 5)); // 5 character random string
                                $hash = md5($captcha_code . 'tijus_captcha_salt');

                                // Generate image via GD inline
                                ob_start();
                                $im = imagecreatetruecolor(120, 40);
                                $bg_color = imagecolorallocate($im, 245, 245, 245);
                                $text_color = imagecolorallocate($im, 60, 60, 60);
                                $line_color = imagecolorallocate($im, 210, 210, 210);

                                imagefilledrectangle($im, 0, 0, 120, 40, $bg_color);

                                // Add some noise lines to prevent OCR parsing
                                for($i=0; $i<6; $i++) {
                                    imageline($im, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
                                }

                                // Draw the text
                                imagestring($im, 5, 38, 12, $captcha_code, $text_color);

                                imagepng($im);
                                imagedestroy($im);
                                $image_data = ob_get_clean();
                                $base64_image = 'data:image/png;base64,' . base64_encode($image_data);
                                ?>
                                <div class="single-form" style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
                                    <img src="<?php echo $base64_image; ?>" alt="Captcha" style="border: 1px solid #ddd; border-radius: 5px; height: 42px;">
                                    <input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>">
                                    <input type="text" name="captcha_answer" placeholder="Enter Captcha Code" required style="flex: 1; max-width: 170px; padding: 10px 15px; height: 42px; border: 1px solid #ddd; border-radius: 5px;">
                                </div>
                                <!-- Captcha End -->

                                <p class="form-message" style="margin-top: 15px; display: none;"></p>
                                <!-- Single Form Start -->
                                <div class="single-form" style="margin-top: 20px;">
                                    <button class="btn btn-primary btn-hover-dark w-100" type="submit">
                                        <span class="btn-text">Send Message</span>
                                        <i class="flaticon-right"></i>
                                    </button>
                                </div>
                                <!-- Single Form End -->
                            </form>
                        </div>
                    </div>
                    <!-- Contact Form End -->

                </div>
            </div>
        </div>
        <!-- Contact Wrapper End -->

    </div>
</div>
<!-- Contact End -->

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

            <img class="shape-1 animation-right" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-14.png" alt="Shape">

            <!-- Download App Button Start -->
            <div class="download-app-btn">
                <ul class="app-btn">
                    <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/google-play.png" alt="Google Play"></a></li>
                    <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/app-store.png" alt="App Store"></a></li>
                </ul>
            </div>
            <!-- Download App Button End -->

        </div>
        <!-- Download App Wrapper End -->

    </div>
</div>
<!-- Download App End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = contactForm.querySelector('button[type="submit"]');
        const btnText = btn.querySelector('.btn-text');
        const formMessage = contactForm.querySelector('.form-message');
        const formData = new FormData(contactForm);
        formData.append('action', 'tijus_submit_contact');

        formMessage.style.display = 'none';
        
        btn.disabled = true;
        const originalText = btnText.innerText;
        btnText.innerText = 'Sending...';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            btn.disabled = false;
            btnText.innerText = originalText;
            formMessage.innerText = res.data;
            formMessage.style.display = 'block';

            if (res.success) {
                formMessage.style.color = '#309255'; /* Edule Green */
                contactForm.reset();
            } else {
                formMessage.style.color = '#dc3545';
            }
        })
        .catch(error => {
            btn.disabled = false;
            btnText.innerText = originalText;
            formMessage.innerText = 'System error occurred. Please try again.';
            formMessage.style.color = '#dc3545';
            formMessage.style.display = 'block';
        });
    });
});
</script>

<?php
get_footer();
