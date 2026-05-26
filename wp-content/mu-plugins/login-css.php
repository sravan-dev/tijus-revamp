<?php
/*
Plugin Name: Tijus Login UI Customizer
Description: Customizes the WordPress login page to match the Agent Login design.
Author: Gemini CLI
Version: 1.1
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'login_enqueue_scripts', function() {
    ?>
    <style type="text/css">
        /* CSS to override default WordPress login styles and implement the new design */
        body.login {
            background-color: #FCF8F1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 100vh !important;
            font-family: 'Montserrat', sans-serif !important;
            position: relative !important;
            overflow: auto !important;
            padding: 40px 0 !important;
            box-sizing: border-box !important;
        }

        body.login::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(circle at 10% 20%, rgba(255, 201, 136, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(255, 201, 136, 0.1) 0%, transparent 40%);
            z-index: -1;
        }

        #login {
            width: 100% !important;
            max-width: 540px !important;
            padding: 0 !important;
            margin: auto !important;
            background: #FFFFFF !important;
            border-radius: 40px !important;
            box-shadow: 0px 20px 60px rgba(0, 0, 0, 0.05) !important;
            padding: 60px 50px !important;
            box-sizing: border-box !important;
            position: relative !important;
        }

        .login h1 { display: none !important; }

        .login form {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            margin-top: 0 !important;
            box-shadow: none !important;
        }

        .login-header-custom {
            text-align: center !important;
            margin-bottom: 40px !important;
        }

        .login-header-custom h2 {
            font-size: 36px !important;
            font-weight: 800 !important;
            color: #000000 !important;
            margin-bottom: 12px !important;
            letter-spacing: -0.5px !important;
        }

        .login-header-custom p {
            font-size: 16px !important;
            color: #666666 !important;
            line-height: 1.5 !important;
            margin: 0 !important;
            max-width: 300px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .login label { display: none !important; }

        .login input[type="text"],
        .login input[type="password"],
        .login input[type="email"] {
            background: #F0F5FF !important;
            border: 1px solid transparent !important;
            border-radius: 16px !important;
            padding: 20px 24px !important;
            font-size: 16px !important;
            color: #000000 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            margin-bottom: 20px !important;
            box-shadow: none !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .login input[type="text"]:focus,
        .login input[type="password"]:focus,
        .login input[type="email"]:focus {
            background: #FFFFFF !important;
            border-color: #FFC988 !important;
            box-shadow: 0 0 0 4px rgba(255, 201, 136, 0.15) !important;
            outline: none !important;
        }

        #user_login {
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='12' cy='12' r='9' stroke='%23999' stroke-width='2'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 24px center !important;
        }

        .wp-pwd { position: relative !important; width: 100% !important; }

        .wp-hide-pw {
            position: absolute !important;
            right: 24px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: none !important;
            border: none !important;
            color: #000 !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            height: auto !important;
            z-index: 5 !important;
            font-size: 14px !important;
        }

        .wp-hide-pw .dashicons { display: none !important; }
        .wp-hide-pw[data-toggle="0"]::before { content: 'Show' !important; }
        .wp-hide-pw[data-toggle="1"]::before { content: 'Hide' !important; }

        .trouble-link {
            display: block !important;
            text-align: left !important;
            margin-bottom: 30px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #000 !important;
            text-decoration: none !important;
            transition: opacity 0.2s !important;
        }
        .trouble-link:hover { opacity: 0.7 !important; }

        .login .submit { float: none !important; padding: 0 !important; margin-bottom: 40px !important; }

        .login .button-primary {
            background-color: #FFC988 !important;
            border: none !important;
            border-radius: 16px !important;
            color: #000 !important;
            font-size: 18px !important;
            font-weight: 800 !important;
            height: 64px !important;
            width: 100% !important;
            margin: 0 !important;
            box-shadow: 0 4px 15px rgba(255, 201, 136, 0.3) !important;
            text-shadow: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .login .button-primary:hover {
            background-color: #f7b36a !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(255, 201, 136, 0.4) !important;
        }

        .social-login-separator { 
            text-align: center !important; 
            margin: 0 0 30px 0 !important; 
            position: relative !important; 
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .social-login-separator::before { 
            content: '' !important; 
            position: absolute !important; 
            top: 50% !important; 
            left: 0 !important; 
            right: 0 !important; 
            height: 1px !important; 
            background: #EEEEEE !important; 
            z-index: 1 !important; 
        }
        .social-login-separator span { 
            background: #FFF !important; 
            padding: 0 15px !important; 
            position: relative !important; 
            z-index: 2 !important; 
            color: #999 !important; 
            font-size: 14px !important; 
            font-weight: 500 !important;
        }

        .social-buttons { display: flex !important; gap: 12px !important; justify-content: space-between !important; margin-bottom: 35px !important; }
        .social-btn { 
            flex: 1 !important; 
            display: flex !important; 
            align-items: center !important; 
            justify-content: center !important; 
            padding: 14px !important; 
            border: 1px solid #EEEEEE !important; 
            border-radius: 14px !important; 
            background: #FFF !important; 
            cursor: pointer !important; 
            text-decoration: none !important; 
            color: #000 !important; 
            font-weight: 700 !important; 
            font-size: 14px !important; 
            gap: 10px !important; 
            transition: all 0.2s ease !important;
        }
        .social-btn:hover { background: #F9F9F9 !important; border-color: #DDD !important; }
        
        .login-footer-link { text-align: center !important; font-size: 15px !important; color: #666 !important; margin-bottom: 10px !important; }
        .login-footer-link a { color: #000 !important; font-weight: 800 !important; text-decoration: none !important; }

        #nav, #backtoblog { display: none !important; }

        .login-copyright {
            margin-top: 50px !important;
            text-align: center !important;
            color: #000 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            letter-spacing: 0.2px !important;
        }
        .login-copyright span { margin: 0 12px !important; color: #CCC !important; font-weight: 300 !important; }
        
        .forgetmenot { display: none !important; }
        #login_error, .message { 
            border: none !important; 
            background: #FFF1F1 !important; 
            color: #D32F2F !important; 
            border-radius: 16px !important; 
            padding: 18px !important; 
            margin-bottom: 25px !important; 
            font-weight: 500 !important;
            box-shadow: 0 2px 10px rgba(211, 47, 47, 0.05) !important;
        }
        .message { background: #F1FFF4 !important; color: #388E3C !important; }
    </style>
    <?php
} );

// Inject Custom Header Content
add_filter( 'login_message', function( $message ) {
    return '
    <div class="login-header-custom">
        <img src="/wp-content/uploads/2026/04/logo.webp" alt="Logo" style="max-width: 200px; margin-bottom: 20px;">
        <p>Hey, Enter your details to get sign in to your account</p>
    </div>' . $message;
} );

// Inject Trouble Link
add_action( 'login_form', function() {
    echo '<a href="' . esc_url( wp_lostpassword_url() ) . '" class="trouble-link">Having trouble in sign in?</a>';
} );

// Inject Social Buttons and Footer (will be moved inside #login via JS)
add_action( 'login_footer', function() {
    ?>
    <div id="custom-login-footer-content" style="display:none;">
        <div class="social-login-separator">
            <span>Or Sign in with</span>
        </div>
        <div class="social-buttons" style="justify-content: center !important;">
            <a href="#" class="social-btn" style="flex: none !important; min-width: 200px !important;">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg> Google
            </a>
        </div>
        <div class="login-copyright">
            Copyright @tijusacademy 2022 <span>|</span> Privacy Policy
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var loginDiv = document.getElementById('login');
        var footerContent = document.getElementById('custom-login-footer-content');
        if (loginDiv && footerContent) {
            footerContent.style.display = 'block';
            loginDiv.appendChild(footerContent);
        }

        // Add placeholders
        var userLogin = document.getElementById('user_login');
        if (userLogin) userLogin.placeholder = 'Enter Email / Phone No';
        
        var userPass = document.getElementById('user_pass');
        if (userPass) userPass.placeholder = 'Passcode';
        
        // Reposition trouble link
        var troubleLink = document.querySelector('.trouble-link');
        var submitP = document.querySelector('p.submit');
        if (troubleLink && submitP) {
            submitP.parentNode.insertBefore(troubleLink, submitP);
        }
        
        // Update Submit button text to match screenshot "Log In"
        var submitBtn = document.getElementById('wp-submit');
        if (submitBtn) submitBtn.value = 'Log In';
    });
    </script>
    <?php
} );

// Set Login Logo URL to Home
add_filter( 'login_headerurl', function() { return home_url(); } );
