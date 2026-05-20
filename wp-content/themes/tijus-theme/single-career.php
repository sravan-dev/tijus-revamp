<?php
/**
 * The template for displaying all single career posts
 */

get_header();
$theme_uri = get_template_directory_uri();
?>

<!-- Page Banner Start -->
<div class="section page-banner">
    <img class="shape-1 animation-round" src="<?php echo esc_url($theme_uri); ?>/assets/images/shape/shape-8.png" alt="Shape">
    <img class="shape-2" src="<?php echo esc_url($theme_uri); ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li><a href="<?php echo esc_url( home_url( '/career/' ) ); ?>">Careers</a></li>
                <li class="active"><?php the_title(); ?></li>
            </ul>
            <h2 class="title"><?php the_title(); ?></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <img class="shape-3" src="<?php echo esc_url($theme_uri); ?>/assets/images/shape/shape-24.png" alt="Shape">
</div>
<!-- Page Banner End -->

<div class="section section-padding mt-n10">
    <div class="container">
        <div class="row gx-10">
            <div class="col-lg-8">
                <?php while ( have_posts() ) : the_post(); ?>
                
                <div class="blog-details-wrapper" style="background:#fff; padding:40px; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,0.05); margin-bottom: 40px;">
                    <h2 class="title" style="margin-bottom: 20px;"><?php the_title(); ?></h2>
                    
                    <div class="blog-details-description mt-4">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'full', array( 'style' => 'margin-bottom:30px; border-radius:10px; width:100%; height:auto;' ) ); ?>
                        <?php else : ?>
                            <img src="<?php echo esc_url($theme_uri . '/assets/images/blog/blog-01.jpg'); ?>" style="margin-bottom:30px; border-radius:10px; width:100%; height:auto;" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                        
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <?php endwhile; ?>
            </div>

            <div class="col-lg-4">
                <!-- Application Form -->
                <div class="contact-form" style="background:#f9f9f9; padding:30px; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,0.05); position: sticky; top: 120px;">
                    <div class="section-title shape-01 mb-4">
                        <h2 class="main-title" style="font-size: 28px;">Apply for this <span>Position</span></h2>
                    </div>

                    <form id="tijus-application-form" class="application-form" enctype="multipart/form-data">
                        <?php wp_nonce_field('tijus_career_application', 'app_nonce'); ?>
                        <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label style="font-weight: 500; margin-bottom: 8px; color: #212832; display:block;">Full Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required style="border: 1px solid #ddd; border-radius: 5px; padding: 12px; width: 100%;">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label style="font-weight: 500; margin-bottom: 8px; color: #212832; display:block;">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required style="border: 1px solid #ddd; border-radius: 5px; padding: 12px; width: 100%;">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label style="font-weight: 500; margin-bottom: 8px; color: #212832; display:block;">Phone Number *</label>
                                <input type="text" name="phone" class="form-control" placeholder="+1234567890" required style="border: 1px solid #ddd; border-radius: 5px; padding: 12px; width: 100%;">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label style="font-weight: 500; margin-bottom: 8px; color: #212832; display:block;">Upload Resume (PDF, DOC) *</label>
                                <input type="file" name="resume" class="form-control" required accept=".pdf,.doc,.docx" style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; background: #fff; width: 100%;">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label style="font-weight: 500; margin-bottom: 8px; color: #212832; display:block;">Cover Letter / Message</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Tell us why you are a great fit..." style="border: 1px solid #ddd; border-radius: 5px; padding: 12px; width: 100%;"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div id="application-msg" style="display:none; padding:15px; border-radius:5px; margin-bottom:15px;"></div>
                                <button type="submit" class="btn btn-primary btn-hover-dark" style="width: 100%;">Submit Application</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appForm = document.getElementById('tijus-application-form');
    if(appForm) {
        appForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const msg = document.getElementById('application-msg');
            
            btn.disabled = true;
            btn.innerHTML = 'Submitting...';
            msg.style.display = 'none';
            msg.className = '';

            const formData = new FormData(this);
            formData.append('action', 'tijus_submit_application');

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                msg.style.display = 'block';
                if (data.success) {
                    msg.style.background = 'rgba(48, 146, 85, 0.1)';
                    msg.style.color = '#309255';
                    msg.style.border = '1px solid #309255';
                    msg.innerHTML = data.data;
                    appForm.reset();
                } else {
                    msg.style.background = 'rgba(220, 53, 69, 0.1)';
                    msg.style.color = '#dc3545';
                    msg.style.border = '1px solid #dc3545';
                    msg.innerHTML = data.data;
                }
                btn.disabled = false;
                btn.innerHTML = 'Submit Application';
            })
            .catch(err => {
                msg.style.display = 'block';
                msg.style.background = 'rgba(220, 53, 69, 0.1)';
                msg.style.color = '#dc3545';
                msg.style.border = '1px solid #dc3545';
                msg.innerHTML = 'Network error occurred. Please try again.';
                btn.disabled = false;
                btn.innerHTML = 'Submit Application';
            });
        });
    }
});
</script>

<?php get_footer(); ?>
