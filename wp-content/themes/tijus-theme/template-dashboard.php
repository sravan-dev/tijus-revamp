<?php
/* Template Name: Student Dashboard */

// Kick out non-logged-in users securely
if ( ! is_user_logged_in() ) {
    wp_redirect( home_url('/sign-in/') );
    exit;
}

get_header();
$theme_uri = get_template_directory_uri();
$current_user = wp_get_current_user();

// Determine which tab is active
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'profile';
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
                <li class="active">Student Dashboard</li>
            </ul>
            <h2 class="title">My <span>Account</span></h2>
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

<!-- Dashboard Container Start -->
<div class="section section-padding">
    <div class="container">
        
        <div class="row">
            
            <!-- Sidebar: col-lg-3 -->
            <div class="col-lg-3 col-md-4">
                <div class="tijus-dashboard-sidebar" style="background: #fff; padding: 30px 20px; border-radius: 10px; box-shadow: 0px 5px 20px 0px rgba(0, 0, 0, 0.05); margin-bottom: 30px; position: relative; z-index: 1;">
                    <div style="text-align:center; margin-bottom: 25px;">
                        <img src="<?php echo esc_url( get_avatar_url( $current_user->ID, ['size' => 100] ) ); ?>" style="border-radius: 50%; border: 3px solid #309255; width: 90px; height: 90px; object-fit: cover;" alt="Avatar">
                        <h4 style="margin-top: 15px; font-weight: 700; font-size: 18px;"><?php echo esc_html( $current_user->display_name ); ?></h4>
                        <span style="display:block; color: #52565b; font-size: 14px; margin-bottom: 20px;">Student</span>
                    </div>
                
                    <ul class="nav flex-column tijus-dashboard-menu" style="gap: 5px; list-style: none; padding-left: 0; margin-bottom: 0;">
                        <li class="nav-item">
                            <a class="nav-link tijus-tab-trigger active" data-target="profile" href="javascript:void(0);" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #fff; background: #309255;"><i class="icofont-ui-user" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> My Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tijus-tab-trigger" data-target="courses" href="javascript:void(0);" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #212832; background: transparent;"><i class="icofont-book" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> Applied Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tijus-tab-trigger" data-target="collections" href="javascript:void(0);" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #212832; background: transparent;"><i class="icofont-save" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> My Collections</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tijus-tab-trigger" data-target="favorites" href="javascript:void(0);" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #212832; background: transparent;"><i class="icofont-heart" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> My Favourites</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tijus-tab-trigger" data-target="settings" href="javascript:void(0);" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #212832; background: transparent;"><i class="icofont-settings-alt" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> Settings</a>
                        </li>
                        <li class="nav-item" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                            <a class="nav-link" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" style="display: block; padding: 12px 20px; border-radius: 6px; font-weight: 500; color: #dc3545;"><i class="icofont-logout" style="margin-right:8px; font-size: 18px; vertical-align: middle;"></i> Sign Out</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content Area: col-lg-9 -->
            <div class="col-lg-9 col-md-8">
                <div class="dashboard-content" style="background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0px 5px 20px 0px rgba(0, 0, 0, 0.05); min-height: 480px;">
                    
                    <div id="tab-profile" class="tijus-tab-content">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                            <h3 style="font-weight: 700; color: #212832; margin-bottom: 0;">Personal Information</h3>
                            <a href="javascript:void(0);" id="tijus-edit-profile-btn" style="font-size: 14px; color: #309255; font-weight: 500;"><i class="icofont-ui-edit"></i> Edit Profile</a>
                        </div>
                        
                        <div id="tijus-profile-view">
                            <table class="table" style="border-top: none;">
                                <tbody>
                                    <tr>
                                        <th style="width: 250px; border-top: none; padding: 15px 0; color: #52565b;">Registration Date</th>
                                        <td style="border-top: none; padding: 15px 0; font-weight:500; color: #212832;"><?php echo date('F j, Y', strtotime($current_user->user_registered)); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 1px solid #eee; padding: 15px 0; color: #52565b;">Full Name</th>
                                        <td style="border-top: 1px solid #eee; padding: 15px 0; font-weight:500; color: #212832;"><?php echo esc_html( $current_user->display_name ); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 1px solid #eee; padding: 15px 0; color: #52565b;">Email Address</th>
                                        <td style="border-top: 1px solid #eee; padding: 15px 0; font-weight:500; color: #212832;"><?php echo esc_html( $current_user->user_email ); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 1px solid #eee; padding: 15px 0; color: #52565b;">Platform Role</th>
                                        <td style="border-top: 1px solid #eee; padding: 15px 0; font-weight:500;"><span style="background: rgba(48, 146, 85, 0.1); color: #309255; padding: 5px 15px; border-radius: 20px; font-size: 13px;">Student</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="tijus-profile-edit" style="display: none; background: #f8f9fa; padding: 25px; border-radius: 8px;">
                            <form id="tijus-profile-form">
                                <div class="form-group mb-3">
                                    <label style="font-weight: 500; margin-bottom: 8px; color: #212832;">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" style="border: 1px solid #ddd; height: 45px; border-radius: 5px; padding: 0 15px;" value="<?php echo esc_attr( $current_user->display_name ); ?>" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label style="font-weight: 500; margin-bottom: 8px; color: #212832;">Email Address <small style="color:#999;">(Cannot be changed here)</small></label>
                                    <input type="email" class="form-control" style="border: 1px solid #ddd; height: 45px; border-radius: 5px; padding: 0 15px; background: #eee;" value="<?php echo esc_attr( $current_user->user_email ); ?>" disabled>
                                </div>
                                <div id="tijus-profile-msg" class="alert d-none mt-3" style="font-size:14px; padding: 10px;"></div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary" id="tijus-profile-submit" style="background:#309255; border:none;">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" id="tijus-profile-cancel" style="margin-left: 10px; background:transparent; color:#52565b; border: 1px solid #ddd;">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div id="tab-courses" class="tijus-tab-content" style="display: none;">
                        <h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">Applied Courses</h3>
                        <div style="background: rgba(13, 202, 240, 0.1); border-left: 4px solid #0dcaf0; padding: 20px; border-radius: 4px; color: #055160;">
                            You have not enrolled in any courses yet.
                        </div>
                        <a href="<?php echo home_url('/courses/'); ?>" class="btn btn-primary btn-hover-dark mt-4">Browse Courses</a>
                    </div>

                    <div id="tab-favorites" class="tijus-tab-content" style="display: none;">
                        <h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Favourites</h3>
                        <div style="background: rgba(255, 188, 0, 0.1); border-left: 4px solid #ffbc00; padding: 20px; border-radius: 4px; color: #664d03;">
                            No favourite items stored yet. Browse courses to add to your wishlist!
                        </div>
                    </div>

                    <div id="tab-collections" class="tijus-tab-content" style="display: none;">
                        <h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Collections</h3>
                        <div style="background: rgba(13, 110, 253, 0.1); border-left: 4px solid #0d6efd; padding: 20px; border-radius: 4px; color: #084298;">
                            You haven't created any collections yet. Curate your learning journey!
                        </div>
                    </div>

                    <div id="tab-settings" class="tijus-tab-content" style="display: none;">
                        <h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">Account Settings</h3>
                        <p style="color: #52565b; margin-bottom: 25px;">Update your account password using the form below.</p>
                        
                        <form action="#" method="POST" id="tijus-settings-form">
                            <div class="row">
                                <div class="col-md-12 mb-4 form-group">
                                    <label style="font-weight: 500; margin-bottom: 8px; color: #212832;">New Password</label>
                                    <input type="password" class="form-control" style="border: 1px solid #eee; height: 50px; border-radius: 5px; padding: 0 20px;" placeholder="Leave blank to keep unchanged">
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-hover-dark">Update Record</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            
        </div>
        
    </div>
</div>
<!-- Dashboard Container End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggers = document.querySelectorAll('.tijus-tab-trigger');
    const contents = document.querySelectorAll('.tijus-tab-content');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');

            // Reset all styling for triggers
            triggers.forEach(btn => {
                btn.style.background = 'transparent';
                btn.style.color = '#212832';
                btn.classList.remove('active');
            });

            // Activate clicked trigger
            this.style.background = '#309255';
            this.style.color = '#fff';
            this.classList.add('active');

            // Hide all content panels
            contents.forEach(content => {
                content.style.display = 'none';
            });

            // Show target panel immediately
            document.getElementById('tab-' + targetId).style.display = 'block';

            // Special dynamic load for favorites when clicked
            if ( targetId === 'favorites' ) {
                const favTab = document.getElementById('tab-favorites');
                const favorites = JSON.parse(localStorage.getItem('tijus_favorites')) || [];
                
                if (favorites.length > 0) {
                    favTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Favourites</h3><div style="text-align:center; padding: 40px;"><i class="icofont-spinner icofont-spin" style="font-size:40px; color:#309255;"></i></div>';
                    
                    const formData = new FormData();
                    formData.append('action', 'tijus_get_favorite_courses');
                    formData.append('course_ids', JSON.stringify(favorites));

                    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            favTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Favourites</h3><div class="row">' + res.data + '</div>';
                            if (typeof initFavorites !== 'undefined') {
                                initFavorites();
                            }
                        } else {
                            favTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Favourites</h3><div style="background: rgba(255, 188, 0, 0.1); border-left: 4px solid #ffbc00; padding: 20px; border-radius: 4px; color: #664d03;">' + res.data + '</div>';
                        }
                    })
                    .catch(e => {
                        console.error(e);
                    });
                } else {
                    favTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Favourites</h3><div style="background: rgba(255, 188, 0, 0.1); border-left: 4px solid #ffbc00; padding: 20px; border-radius: 4px; color: #664d03;">No favourite items stored yet. Browse courses to add to your wishlist!</div>';
                }
            }

            // Special dynamic load for collections when clicked
            if ( targetId === 'collections' ) {
                const colTab = document.getElementById('tab-collections');
                const collections = JSON.parse(localStorage.getItem('tijus_collections')) || [];
                
                if (collections.length > 0) {
                    colTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Collections</h3><div style="text-align:center; padding: 40px;"><i class="icofont-spinner icofont-spin" style="font-size:40px; color:#309255;"></i></div>';
                    
                    const formData = new FormData();
                    formData.append('action', 'tijus_get_collection_courses');
                    formData.append('course_ids', JSON.stringify(collections));

                    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            colTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Collections</h3><div class="row">' + res.data + '</div>';
                            if (typeof initFavorites !== 'undefined') {
                                initFavorites(); // Optional: so hearts inside collections tab also light up dynamically
                            }
                        } else {
                            colTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Collections</h3><div style="background: rgba(13, 110, 253, 0.1); border-left: 4px solid #0d6efd; padding: 20px; border-radius: 4px; color: #084298;">' + res.data + '</div>';
                        }
                    })
                    .catch(e => {
                        console.error(e);
                    });
                } else {
                    colTab.innerHTML = '<h3 style="margin-bottom: 25px; font-weight: 700; color: #212832;">My Collections</h3><div style="background: rgba(13, 110, 253, 0.1); border-left: 4px solid #0d6efd; padding: 20px; border-radius: 4px; color: #084298;">You haven\'t created any collections yet. Curate your learning journey!</div>';
                }
            }
            // Update URL hash without causing a page jump
            if (history.pushState) {
                history.pushState(null, null, '#' + targetId);
            } else {
                window.location.hash = targetId;
            }
        });
    });

    // On page load, instantly open the tab according to the hash if it exists
    let hash = window.location.hash.substring(1);
    if (hash) {
        let trigger = document.querySelector('.tijus-tab-trigger[data-target="' + hash + '"]');
        if (trigger) trigger.click();
    }

    // Edit Profile Logic
    const editBtn = document.getElementById('tijus-edit-profile-btn');
    const cancelBtn = document.getElementById('tijus-profile-cancel');
    const viewDiv = document.getElementById('tijus-profile-view');
    const editDiv = document.getElementById('tijus-profile-edit');
    const profileForm = document.getElementById('tijus-profile-form');
    
    if(editBtn && cancelBtn && viewDiv && editDiv && profileForm) {
        editBtn.addEventListener('click', function() {
            viewDiv.style.display = 'none';
            editDiv.style.display = 'block';
        });
        
        cancelBtn.addEventListener('click', function() {
            editDiv.style.display = 'none';
            viewDiv.style.display = 'block';
        });
        
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('tijus-profile-submit');
            const msgBox = document.getElementById('tijus-profile-msg');
            const fullName = this.querySelector('input[name="full_name"]').value;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Saving...';
            msgBox.classList.add('d-none');
            msgBox.classList.remove('alert-danger', 'alert-success');
            
            const formData = new FormData();
            formData.append('action', 'tijus_update_profile');
            formData.append('full_name', fullName);
            
            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                msgBox.classList.remove('d-none');
                if (data.success) {
                    msgBox.classList.add('alert-success');
                    msgBox.innerHTML = data.data;
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    msgBox.classList.add('alert-danger');
                    msgBox.innerHTML = data.data || 'Failed to update.';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Changes';
                }
            })
            .catch(err => {
                msgBox.classList.remove('d-none');
                msgBox.classList.add('alert-danger');
                msgBox.innerHTML = 'Network error.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Changes';
            });
        });
    }
});
</script>

<?php get_footer(); ?>
