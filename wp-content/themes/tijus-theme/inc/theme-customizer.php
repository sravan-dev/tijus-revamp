<?php
/**
 * Tijus Theme Customizer Settings
 *
 * Adds a "Tijus Theme Options" panel in Appearance → Customize for:
 *  - Header top bar (announcement, phone, email)
 *  - Logo
 *  - Social media links
 *  - Footer info (address, copyright)
 *
 * @package tijus-theme
 */

add_action( 'customize_register', 'tijus_customizer_register' );

function tijus_customizer_register( $wp_customize ) {

	// ── Main Panel ────────────────────────────────────────────────────────────
	$wp_customize->add_panel( 'tijus_theme_options', [
		'title'    => __( 'Tijus Theme Options', 'tijus-theme' ),
		'priority' => 130,
	] );

	// ── Section: Logo ─────────────────────────────────────────────────────────
	$wp_customize->add_section( 'tijus_logo_section', [
		'title'    => __( 'Logo', 'tijus-theme' ),
		'panel'    => 'tijus_theme_options',
		'priority' => 10,
	] );

	$wp_customize->add_setting( 'tijus_logo', [
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tijus_logo', [
		'label'       => __( 'Header & Footer Logo', 'tijus-theme' ),
		'description' => __( 'Upload your logo image. Replaces the default logo on both header and footer.', 'tijus-theme' ),
		'section'     => 'tijus_logo_section',
	] ) );

	// ── Section: Header Top Bar ───────────────────────────────────────────────
	$wp_customize->add_section( 'tijus_header_top', [
		'title'    => __( 'Header Top Bar', 'tijus-theme' ),
		'panel'    => 'tijus_theme_options',
		'priority' => 20,
	] );

	// Announcement text
	$wp_customize->add_setting( 'tijus_announcement_text', [
		'default'           => "Enroll now to get 50% off on all courses",
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_announcement_text', [
		'label'   => __( 'Announcement Text', 'tijus-theme' ),
		'section' => 'tijus_header_top',
		'type'    => 'text',
	] );

	// Announcement link
	$wp_customize->add_setting( 'tijus_announcement_link', [
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_announcement_link', [
		'label'   => __( 'Announcement Link URL', 'tijus-theme' ),
		'section' => 'tijus_header_top',
		'type'    => 'url',
	] );

	// Phone
	$wp_customize->add_setting( 'tijus_phone', [
		'default'           => '(970) 262-1413',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_phone', [
		'label'   => __( 'Phone Number', 'tijus-theme' ),
		'section' => 'tijus_header_top',
		'type'    => 'text',
	] );

	// Email
	$wp_customize->add_setting( 'tijus_email', [
		'default'           => 'address@gmail.com',
		'sanitize_callback' => 'sanitize_email',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_email', [
		'label'   => __( 'Email Address', 'tijus-theme' ),
		'section' => 'tijus_header_top',
		'type'    => 'email',
	] );

	// ── Section: Social Media Links ───────────────────────────────────────────
	$wp_customize->add_section( 'tijus_social_links', [
		'title'    => __( 'Social Media Links', 'tijus-theme' ),
		'panel'    => 'tijus_theme_options',
		'priority' => 30,
	] );

	$social_platforms = [
		'facebook'  => 'Facebook URL',
		'twitter'   => 'Twitter URL',
		'skype'     => 'Skype URL',
		'instagram' => 'Instagram URL',
	];

	foreach ( $social_platforms as $key => $label ) {
		$wp_customize->add_setting( 'tijus_' . $key . '_url', [
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		] );
		$wp_customize->add_control( 'tijus_' . $key . '_url', [
			'label'   => __( $label, 'tijus-theme' ),
			'section' => 'tijus_social_links',
			'type'    => 'url',
		] );
	}

	// ── Section: Footer ───────────────────────────────────────────────────────
	$wp_customize->add_section( 'tijus_footer', [
		'title'    => __( 'Footer', 'tijus-theme' ),
		'panel'    => 'tijus_theme_options',
		'priority' => 40,
	] );

	// Footer address line 1
	$wp_customize->add_setting( 'tijus_footer_address_title', [
		'default'           => 'Caribbean Ct',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_footer_address_title', [
		'label'   => __( 'Address Line 1 (Street)', 'tijus-theme' ),
		'section' => 'tijus_footer',
		'type'    => 'text',
	] );

	// Footer address line 2
	$wp_customize->add_setting( 'tijus_footer_address_city', [
		'default'           => 'Haymarket, Virginia (VA).',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_footer_address_city', [
		'label'   => __( 'Address Line 2 (City, State)', 'tijus-theme' ),
		'section' => 'tijus_footer',
		'type'    => 'text',
	] );

	// Copyright text
	$wp_customize->add_setting( 'tijus_copyright_text', [
		'default'           => '2021 <span>Edule</span> Made with &#10084; by <a href="#">Codecarnival</a>',
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'refresh',
	] );
	$wp_customize->add_control( 'tijus_copyright_text', [
		'label'       => __( 'Copyright Text', 'tijus-theme' ),
		'description' => __( 'HTML is allowed. Example: 2025 <span>My Brand</span> All rights reserved.', 'tijus-theme' ),
		'section'     => 'tijus_footer',
		'type'        => 'textarea',
	] );
}
