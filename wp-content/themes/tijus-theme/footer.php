        <!-- Footer Start  -->
        <div class="section footer-section">

            <!-- Footer Widget Section Start -->
            <div class="footer-widget-section">

                <img class="shape-1 animation-down" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-21.png" alt="Shape">

                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 order-md-1 order-lg-1">

                            <!-- Footer Widget Start -->
                            <?php
                            $f_phone    = get_theme_mod( 'tijus_phone', '(970) 262-1413' );
                            $f_email    = get_theme_mod( 'tijus_email', 'address@gmail.com' );
                            $f_phone_r  = preg_replace( '/[^0-9+]/', '', $f_phone );
                            $f_addr1    = get_theme_mod( 'tijus_footer_address_title', 'Caribbean Ct' );
                            $f_addr2    = get_theme_mod( 'tijus_footer_address_city',  'Haymarket, Virginia (VA).' );
                            ?>
                            <div class="footer-widget">
                                <div class="widget-logo">
                                    <?php
                                    $fl_w = get_theme_mod( 'tijus_footer_logo_width', '' );
                                    $fl_h = get_theme_mod( 'tijus_footer_logo_height', '' );
                                    $fl_style = '';
                                    if ( $fl_w ) $fl_style .= 'width:' . intval( $fl_w ) . 'px;';
                                    if ( $fl_h ) $fl_style .= 'height:' . intval( $fl_h ) . 'px;';
                                    ?>
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo tijus_get_logo_url(); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"<?php echo $fl_style ? ' style="' . esc_attr( $fl_style ) . '"' : ''; ?>></a>
                                </div>

                                <div class="widget-address">
                                    <h4 class="footer-widget-title"><?php echo esc_html( $f_addr1 ); ?></h4>
                                    <p><?php echo esc_html( $f_addr2 ); ?></p>
                                </div>

                                <ul class="widget-info">
                                    <li>
                                        <p> <i class="flaticon-email"></i> <a href="mailto:<?php echo esc_attr( $f_email ); ?>"><?php echo esc_html( $f_email ); ?></a> </p>
                                    </li>
                                    <li>
                                        <p> <i class="flaticon-phone-call"></i> <a href="tel:<?php echo esc_attr( $f_phone_r ); ?>"><?php echo esc_html( $f_phone ); ?></a> </p>
                                    </li>
                                </ul>

                                <ul class="widget-social">
                                    <?php foreach ( tijus_get_social_links() as $_sl ) : ?>
                                    <li><a href="<?php echo esc_url( $_sl['url'] ); ?>"><i class="<?php echo esc_attr( $_sl['icon'] ); ?>"></i></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <!-- Footer Widget End -->

                        </div>
                        <div class="col-lg-6 order-md-3 order-lg-2">

                            <!-- Footer Widget Link Start -->
                            <div class="footer-widget-link">

                                <!-- Footer Widget Start -->
                                <div class="footer-widget">
                                    <h4 class="footer-widget-title">Category</h4>
                                    <?php
                                    if ( has_nav_menu( 'footer-category' ) ) {
                                        wp_nav_menu( array(
                                            'theme_location' => 'footer-category',
                                            'menu_class'     => 'widget-link',
                                            'container'      => false,
                                            'depth'          => 1,
                                            'fallback_cb'    => false,
                                        ) );
                                    } else {
                                        ?>
                                        <ul class="widget-link">
                                            <li><a href="#">Creative Writing</a></li>
                                            <li><a href="#">Film &amp; Video</a></li>
                                            <li><a href="#">Graphic Design</a></li>
                                            <li><a href="#">UI/UX Design</a></li>
                                            <li><a href="#">Business Analytics</a></li>
                                            <li><a href="#">Marketing</a></li>
                                        </ul>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!-- Footer Widget End -->

                                <!-- Footer Widget Start -->
                                <div class="footer-widget">
                                    <h4 class="footer-widget-title">Quick Links</h4>
                                    <?php
                                    if ( has_nav_menu( 'footer-quick-links' ) ) {
                                        wp_nav_menu( array(
                                            'theme_location' => 'footer-quick-links',
                                            'menu_class'     => 'widget-link',
                                            'container'      => false,
                                            'depth'          => 1,
                                            'fallback_cb'    => false,
                                        ) );
                                    } else {
                                        ?>
                                        <ul class="widget-link">
                                            <li><a href="#">Privacy Policy</a></li>
                                            <li><a href="#">Discussion</a></li>
                                            <li><a href="#">Terms &amp; Conditions</a></li>
                                            <li><a href="#">Customer Support</a></li>
                                            <li><a href="#">Course FAQ's</a></li>
                                        </ul>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!-- Footer Widget End -->

                            </div>
                            <!-- Footer Widget Link End -->

                        </div>
                        <div class="col-lg-3 col-md-6 order-md-2 order-lg-3">

                            <!-- Footer Widget Start -->
                            <div class="footer-widget">
                                <h4 class="footer-widget-title">Subscribe</h4>

                                <div class="widget-subscribe">
                                    <p>Lorem Ipsum has been them an industry printer took a galley make book.</p>

                                    <div class="widget-form">
                                        <form action="#">
                                            <input type="text" placeholder="Email here">
                                            <button class="btn btn-primary btn-hover-dark">Subscribe Now</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Widget End -->

                        </div>
                    </div>
                </div>

                <img class="shape-2 animation-left" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-22.png" alt="Shape">

            </div>
            <!-- Footer Widget Section End -->

            <!-- Footer Copyright Start -->
            <div class="footer-copyright">
                <div class="container">

                    <!-- Footer Copyright Start -->
                    <div class="copyright-wrapper">
                        <div class="copyright-link">
                            <a href="#">Terms of Service</a>
                            <a href="#">Privacy Policy</a>
                            <a href="#">Sitemap</a>
                            <a href="#">Security</a>
                        </div>
                        <div class="copyright-text">
                            <p>&copy; <?php echo wp_kses_post( get_theme_mod( 'tijus_copyright_text', '2021 <span>Edule</span> Made with &#10084; by <a href="#">Codecarnival</a>' ) ); ?></p>
                        </div>
                    </div>
                    <!-- Footer Copyright End -->

                </div>
            </div>
            <!-- Footer Copyright End -->

        </div>
        <!-- Footer End -->

        <!--Back To Start-->
        <a href="#" class="back-to-top">
            <i class="icofont-simple-up"></i>
        </a>
        <!--Back To End-->

    </div>






<?php if ( is_singular( 'course' ) ) : ?>
<!-- Enrollment Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <h5 class="modal-title w-100 text-center" id="enrollmentModalLabel">Enroll Now</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <style>
                    #enrollmentForm input.form-control, 
                    #enrollmentForm select.form-select {
                        border: 1px solid #000 !important;
                    }
                </style>
                <form id="enrollmentForm" method="post">
                    <input type="hidden" name="action" value="tijus_enroll_user">
                    <input type="hidden" name="enroll_nonce" value="<?php echo esc_attr( wp_create_nonce( 'tijus_enroll_action' ) ); ?>">
                    <input type="hidden" name="course_id" value="<?php echo get_the_ID(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required style="height: 45px; border-radius: 5px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Last Name</label>
                            <input type="text" name="last_name" class="form-control" style="height: 45px; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-weight:600;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="enroll_email" class="form-control" required style="height: 45px; border-radius: 5px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required style="height: 45px; border-radius: 5px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">WhatsApp/Phone No.</label>
                            <input type="text" name="whatsapp" class="form-control" style="height: 45px; border-radius: 5px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Course Mode <span class="text-danger">*</span></label>
                            <select name="course_mode" class="form-select" required style="height: 45px; border-radius: 5px;">
                                <option value="">Select Mode</option>
                                <option value="Online">Online</option>
                                <option value="Offline">Offline</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Course <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="<?php echo esc_attr( get_the_title() ); ?>" readonly style="height: 45px; border-radius: 5px; background: #f0f0f0;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">Country</label>
                            <input type="text" name="country" class="form-control" style="height: 45px; border-radius: 5px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;">City</label>
                            <input type="text" name="city" class="form-control" style="height: 45px; border-radius: 5px;">
                        </div>
                    </div>

                    <div id="enrollmentMessage" class="alert d-none mt-3" style="font-size:14px; border-radius:5px;"></div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-hover-dark w-100" id="enrollSubmitBtn" style="height: 50px; font-weight:600; font-size:16px;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var enrollForm = document.getElementById('enrollmentForm');
    if (enrollForm) {
        var emailInput = document.getElementById('enroll_email');
        var submitBtn = document.getElementById('enrollSubmitBtn');
        var msgBox = document.getElementById('enrollmentMessage');
        
        var isEmailExisting = false;
        var checkTimeout = null;
        
        emailInput.addEventListener('input', function() {
            clearTimeout(checkTimeout);
            var emailVal = this.value.trim();
            if (emailVal.length > 5 && emailVal.includes('@')) {
                checkTimeout = setTimeout(function() {
                    var formData = new FormData();
                    formData.append('action', 'tijus_check_email_exists');
                    formData.append('email', emailVal);
                    
                    fetch('<?php echo esc_url( admin_url('admin-ajax.php') ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && data.data.exists) {
                            isEmailExisting = true;
                            submitBtn.innerHTML = 'Login to Enroll';
                            msgBox.classList.remove('d-none', 'alert-danger');
                            msgBox.classList.add('alert-success');
                            msgBox.innerHTML = 'Account found! Please login to immediately enroll.';
                        } else {
                            isEmailExisting = false;
                            submitBtn.innerHTML = 'Submit';
                            msgBox.classList.add('d-none');
                        }
                    });
                }, 500);
            } else {
                isEmailExisting = false;
                submitBtn.innerHTML = 'Submit';
            }
        });

        enrollForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isEmailExisting) {
                var emailVal = emailInput.value.trim();
                var courseId = document.querySelector('input[name="course_id"]').value;
                var baseLoginUrl = '<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>';
                
                // Add query args securely considering existing parameters like `?redirect_to=`
                var cleanLoginUrl = baseLoginUrl.replace(/&amp;/g, '&');
                var separator = cleanLoginUrl.indexOf('?') !== -1 ? '&' : '?';
                var finalUrl = cleanLoginUrl + separator + 'auto_enroll_course=' + courseId + '&log=' + encodeURIComponent(emailVal);
                
                window.location.href = finalUrl;
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';
            msgBox.classList.add('d-none');
            msgBox.classList.remove('alert-danger', 'alert-success');
            
            var formData = new FormData(enrollForm);
            
            fetch('<?php echo esc_url( admin_url('admin-ajax.php') ); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                msgBox.classList.remove('d-none');
                if (data.success) {
                    msgBox.classList.add('alert-success');
                    msgBox.innerHTML = data.data;
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    msgBox.classList.add('alert-danger');
                    msgBox.innerHTML = data.data;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit';
                }
            })
            .catch(error => {
                msgBox.classList.remove('d-none', 'alert-success');
                msgBox.classList.add('alert-danger');
                msgBox.innerHTML = 'An unexpected error occurred.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit';
            });
        });
    }
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var favorites = JSON.parse(localStorage.getItem('tijus_favorites')) || [];

    // Initialize states on load
    function initFavorites() {
        var loveBtns = document.querySelectorAll('.tijus-love-btn');
        loveBtns.forEach(function(btn) {
            var id = btn.getAttribute('data-target-id');
            if (favorites.includes(id)) {
                btn.innerHTML = '<i class="icofont-heart"></i>';
                btn.style.color = '#ff4500';
            }
        });
    }
    initFavorites();
    
    // Toast UI builder
    function showToast(message) {
        var toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.bottom = '30px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.backgroundColor = '#333';
        toast.style.color = '#fff';
        toast.style.padding = '12px 24px';
        toast.style.borderRadius = '30px';
        toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        toast.style.zIndex = '9999';
        toast.style.fontSize = '14px';
        toast.style.transition = 'opacity 0.3s ease';
        toast.innerHTML = message;
        
        document.body.appendChild(toast);
        
        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }

    // Event Delegation (fixes AJAX loaded cards)
    document.body.addEventListener('click', function(e) {
        // Handle dropdown favorite click
        var dropdownLoveBtn = e.target.closest('.tijus-dropdown-love-btn');
        if (dropdownLoveBtn) {
            e.preventDefault();
            var loveBtn = dropdownLoveBtn.closest('.single-courses').querySelector('.tijus-love-btn');
            if (loveBtn) {
                loveBtn.click();
            }
            return;
        }

        // Handle collection click
        var collectionBtn = e.target.closest('.tijus-collection-btn');
        if (collectionBtn) {
            e.preventDefault();
            var targetId = collectionBtn.getAttribute('data-target-id');
            var collections = JSON.parse(localStorage.getItem('tijus_collections')) || [];
            if (!collections.includes(targetId)) {
                collections.push(targetId);
                localStorage.setItem('tijus_collections', JSON.stringify(collections));
                showToast('Added to Collection');
            } else {
                showToast('Already in Collection');
            }
            return;
        }

        // Handle specific remove from favorites inside dashboard
        var removeFavBtn = e.target.closest('.tijus-remove-favorite-btn');
        if (removeFavBtn) {
            e.preventDefault();
            var targetId = removeFavBtn.getAttribute('data-target-id');
            var index = favorites.indexOf(targetId);
            if (index > -1) {
                favorites.splice(index, 1);
                localStorage.setItem('tijus_favorites', JSON.stringify(favorites));
                showToast('Removed from Favorites');
                // Refresh dashboard tab
                var favTab = document.querySelector('.tijus-tab-trigger[data-target="favorites"]');
                if(favTab) favTab.click();
            }
            return;
        }
        
        // Handle specific remove from collections inside dashboard
        var removeColBtn = e.target.closest('.tijus-remove-collection-btn');
        if (removeColBtn) {
            e.preventDefault();
            var targetId = removeColBtn.getAttribute('data-target-id');
            var collections = JSON.parse(localStorage.getItem('tijus_collections')) || [];
            var index = collections.indexOf(targetId);
            if (index > -1) {
                collections.splice(index, 1);
                localStorage.setItem('tijus_collections', JSON.stringify(collections));
                showToast('Removed from Collection');
                // Refresh dashboard tab
                var colTab = document.querySelector('.tijus-tab-trigger[data-target="collections"]');
                if(colTab) colTab.click();
            }
            return;
        }

        // Handle share click
        var shareBtn = e.target.closest('.tijus-share-btn');
        if (shareBtn) {
            e.preventDefault();
            var url = shareBtn.getAttribute('data-url');
            var title = shareBtn.getAttribute('data-title');
            if (navigator.share) {
                navigator.share({ title: title, url: url }).catch(function(){});
            } else {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Link copied to clipboard!');
                }).catch(function() {
                    showToast('Failed to copy link.');
                });
            }
            return;
        }

        var btn = e.target.closest('.tijus-love-btn');
        if (!btn) return;
        
        e.preventDefault();
        var targetId = btn.getAttribute('data-target-id');
        var index = favorites.indexOf(targetId);
        
        if (index > -1) {
            // Remove from favorites
            favorites.splice(index, 1);
            btn.innerHTML = '<i class="icofont-heart-alt"></i>';
            btn.style.color = '#999';
            showToast('Removed from Favorites');
        } else {
            // Add to favorites
            favorites.push(targetId);
            btn.innerHTML = '<i class="icofont-heart"></i>';
            btn.style.color = '#ff4500';
            showToast('Added to Favorites ❤️');
        }
        
        localStorage.setItem('tijus_favorites', JSON.stringify(favorites));
    });
    
    // Re-initialize after AJAX calls if jQuery ajaxComplete is available (WordPress core)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ajaxComplete(function() {
            initFavorites();
        });
    }
});
</script>

<?php wp_footer(); ?>
</body>

</html>