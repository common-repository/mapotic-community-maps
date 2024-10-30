<?php
/**
 * Mapotic - Community Maps
 *
 * @package     mapotic
 * @author      Mapotic.com
 * @copyright   2019 Mapotic.com
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Mapotic - Community Maps
 * Description: Anyone can create a free map and customise it for their own mapping purposes. Mapotic was created to provide users with a flexible solution that would integrate social interactions into digital maps.
 * Version:     1.1.0
 * Author:      Mapotic.com
 * Author URI:  https://mapotic.com
 * Text Domain: mapotic
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

const MAPOTIC_SETTINGS_GROUP  = 'mapotic_main_section';
const MAPOTIC_SETTINGS_PREFIX = 'mapotic_';

add_shortcode( 'mapotic-map', 'mapotic_layout_mapotic_map' );
function mapotic_layout_mapotic_map() {
	$mapUrl = get_option( MAPOTIC_SETTINGS_PREFIX . 'map_url', 'https://www.mapotic.com/slovakia' );
	$height = get_option( MAPOTIC_SETTINGS_PREFIX . 'height', 800 );
	$width  = get_option( MAPOTIC_SETTINGS_PREFIX . 'width', 600 );
	$zoom   = get_option( MAPOTIC_SETTINGS_PREFIX . 'zoom', 7 );

	return '
    <div style="-webkit-overflow-scrolling: touch; overflow-y: scroll;">
        <iframe style="overflow: hidden; width: ' . $width . 'px; height: ' . $height . 'px;" allow="geolocation *; camera *;" frameborder="0" src="https://' . $mapUrl . '/embed?zoom=' . $zoom . '">
        </iframe>
    </div>
    ';
}

add_action( 'admin_menu', 'mapotic_setup_menu' );
function mapotic_setup_menu() {
	add_menu_page( 'Mapotic', 'Mapotic', 'manage_options', 'mapotic', 'mapotic_options_page', 'dashicons-mapotic' );
}

function mapotic_options_page() {
	echo '
    <div class="wrap">
        <h1>' . __( 'Maps for communities - Mapotic', 'mapotic' ) . '</h1>
        <h2>' . __( 'Creating new community map', 'mapotic' ) . '</h2>
        <p>' . __( '1. Register on Mapotic.com - Maps for communities', 'mapotic' ) . '</p>
        <a href="https://www.mapotic.com/(dialog:signup)" target="_blank">
            <input type="button" class="button action" value="' . __( 'Register to Mapotic', 'mapotic' ) . '">
        </a>
        <p>' . __( '2. Create map for your community', 'mapotic' ) . '</p>
        <a href="https://www.mapotic.com/create" target="_blank">
            <input type="button" class="button action" value="' . __( 'Create new map', 'mapotic' ) . '">
        </a>

        <p>' . __( 'You can continue with following short-code setup whenever you feel your map is ready.', 'mapotic' ) . '</p>


        <form method="post" action="options.php">
	';
	settings_fields( MAPOTIC_SETTINGS_GROUP );
	do_settings_sections( "mapotic-options" );
	submit_button();
	echo '
        </form>

        <p><b>' . __( 'Shortcode of map:', 'mapotic' ) . '</b></p>
        <p>' . __( 'After finishing setup above your shortcode will appear here:', 'mapotic' ) . '</p>
        <div class="card" style="background: #008ec2; color: #fff">[mapotic-map]</div>
        <p><i>' . __( 'Copy this shortcode and paste it to your post, page or text widget.', 'mapotic' ) . '</i></p>
    </div>
	';
}

add_action( 'admin_init', 'mapotic_display_options' );
function mapotic_display_options() {
	add_settings_section( MAPOTIC_SETTINGS_GROUP, __( 'Setting short-code of map', 'mapotic' ), '', 'mapotic-options' );

	add_settings_field(
		MAPOTIC_SETTINGS_PREFIX . 'map_url',
		__( 'Your map URL:', 'mapotic' ),
		'mapotic_display_mapotic_url_input_element',
		'mapotic-options',
		MAPOTIC_SETTINGS_GROUP,
		array(
			MAPOTIC_SETTINGS_PREFIX . 'map_url',
			'450'
		)
	);
	add_settings_field(
		MAPOTIC_SETTINGS_PREFIX . 'height',
		__( 'Height (px):', 'mapotic' ),
		'mapotic_display_number_input_element',
		'mapotic-options',
		MAPOTIC_SETTINGS_GROUP,
		array(
			MAPOTIC_SETTINGS_PREFIX . 'height',
			'70'
		)
	);
	add_settings_field(
		MAPOTIC_SETTINGS_PREFIX . 'width',
		__( 'Width (px):', 'mapotic' ),
		'mapotic_display_number_input_element',
		'mapotic-options',
		MAPOTIC_SETTINGS_GROUP,
		array(
			MAPOTIC_SETTINGS_PREFIX . 'width',
			'70'
		)
	);
	add_settings_field(
		MAPOTIC_SETTINGS_PREFIX . 'zoom',
		__( 'Zoom level:', 'mapotic' ),
		'mapotic_display_zoom_select_element',
		'mapotic-options',
		MAPOTIC_SETTINGS_GROUP,
		array( MAPOTIC_SETTINGS_PREFIX . 'zoom' )
	);

	register_setting( MAPOTIC_SETTINGS_GROUP, MAPOTIC_SETTINGS_PREFIX . 'map_url', 'mapotic_sanitize_url' );
	register_setting( MAPOTIC_SETTINGS_GROUP, MAPOTIC_SETTINGS_PREFIX . 'height', 'sanitize_text_field' );
	register_setting( MAPOTIC_SETTINGS_GROUP, MAPOTIC_SETTINGS_PREFIX . 'width', 'sanitize_text_field' );
	register_setting( MAPOTIC_SETTINGS_GROUP, MAPOTIC_SETTINGS_PREFIX . 'zoom', 'sanitize_text_field' );
}

function mapotic_sanitize_url( $option ) {
	return str_replace( array( 'http://', 'https://' ), '', $option );
}

function mapotic_display_mapotic_url_input_element( $params ) {
	printf(
		'<input type="text" id="%s" name="%1$s" value="%s" style="width:%dpx" pattern="%s" title="%s"/>',
		$params[0],
		get_option( $params[0] ),
		$params[1],
		'(https://|http://|)www\\.mapotic\\.com/.*',
		'Correct format https://www.mapotic.com/[your_map_id]'
	);
}

function mapotic_display_number_input_element( $params ) {
	printf(
		'<input type="number" id="%s" name="%1$s" value="%s" style="width:%dpx" min="1"  max="9999"/>',
		$params[0],
		get_option( $params[0] ),
		$params[1]
	);
}


function mapotic_display_zoom_select_element( $params ) {
	$option_value = get_option( $params[0] );
	printf( '<select id="%s" name="%1$s">', $params[0] );
	for ( $i = 1; $i <= 14; $i ++ ) {
		printf( '<option value="%d"%s>%1$d</option>', $i, $i == $option_value ? 'selected' : '' );
	}
	echo '</select>';
}

add_action( 'admin_enqueue_scripts', 'mapotic_enqueue_styles' );
function mapotic_enqueue_styles() {
	wp_register_style( 'mapotic_dashicons', plugin_dir_url( __FILE__ ) . 'mapotic.css');
	wp_enqueue_style( 'mapotic_dashicons' );
}
