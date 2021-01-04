<?php
/**
 * Plugin Name:     DWR Blocks
 * Description:     Custom blocks for https://dwr.io.
 * Version:         0.1.0
 * Author:          Daniel W. Robert
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     dwr-blocks
 *
 * @package         dwr-blocks
 */

namespace DWR_Blocks;

class DWR_Blocks {
	/**
	 * Singleton instance.
	 *
	 * @since 2021-01-01
	 *
	 * @var self Instance.
	 */
	private static $instance = null;

	/**
	 * Has been initialized yet?
	 *
	 * @since 2021-01-01
	 *
	 * @var bool Initialized?
	 */
	private $did_init;

	/**
	 * Private constructor.
	 *
	 * @since 2021-01-01
	 */
	private function __construct() {
		$this->did_init = false;
	}

	/**
	 * Create or return instance of this class.
	 *
	 * @since 2021-01-01
	 */
	public static function get_instance() : self {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 2021-01-01
	 */
	public function init() : void {
		if ( ! function_exists( 'register_block_type' ) ) {
			return; // The block editor is not supported.
		} elseif ( $this->did_init ) {
			return; // Already initialized.
		}

		// Flag as initialized.
		$this->did_init = true;

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories', array( $this, 'set_blocks_category' ), 10, 2 );
	}

	/**
	 * Load all translations for our plugin from the /languages/ folder.
	 *
	 * @since 2021-01-01
	 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
	*/
	public function load_textdomain() : void {
		load_plugin_textdomain( 'dwr-blocks', false, basename( __DIR__ ) . '/languages' );
	}

	/**
	 * Registers all block assets so that they can be enqueued through Gutenberg in
	 * the corresponding context.
	 *
	 * Passes translations to JavaScript.
	 *
	 * @since 2021-01-01
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-registration/
	 */
	public function register_blocks() : void {
		$dir = __DIR__;

		// Fail if block editor is not supported
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Automatically load dependencies and version.
		$script_asset_path = "$dir/build/index.asset.php";
		if ( ! file_exists( $script_asset_path ) ) {
			throw new Error(
				'You need to run `npm start` or `npm run build` first.'
			);
		}
		$script_asset = require( $script_asset_path );

		// Register the block editor script.
		$index_js     = 'build/index.js';
		wp_register_script(
			'dwr-blocks-block-editor',
			plugins_url( $index_js, __FILE__ ),
			$script_asset['dependencies'],
			$script_asset['version']
		);
		wp_set_script_translations( 'dwr-blocks-block-editor', 'dwr-blocks' );

		// Register the block editor stylesheet.
		$editor_css = 'build/index.css';
		wp_register_style(
			'dwr-blocks-block-editor',
			plugins_url( $editor_css, __FILE__ ),
			array(),
			filemtime( "$dir/$editor_css" )
		);

		// Register the main front-end stylesheet.
		$style_css = 'build/style-index.css';
		wp_register_style(
			'dwr-blocks-block',
			plugins_url( $style_css, __FILE__ ),
			array(),
			filemtime( "$dir/$style_css" )
		);

		/**
		 * Adds internationalization support.
		 *
		 * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
		 * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ).
		 *
		 * @since 2021-01-01
		 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/internationalization/
		 * @link https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
		 */
		wp_set_script_translations(
			'dwr-blocks-editor-script',
			'dwr-blocks',
			"$dir/languages"
		);

		// Array of blocks created in this plugin
		$blocks = [
			'dwr-blocks/table-of-contents',
		];

		// Loop through $blocks and register each block with the same script and styles.
		foreach( $blocks as $block ) {
			$options = [
				'editor_script' => 'dwr-blocks-block-editor',
				'editor_style'  => 'dwr-blocks-block-editor',
				'style'         => 'dwr-blocks-block',
			];
			register_block_type(
				$block,
				$options
			);
		}
	}

	/**
	 * Sets custom "DWR Blocks" category to house our blocks in admin editor.
	 *
	 * @param array    $categories Categories array
	 * @param \WP_Post $post Post object
	 *
	 * @since 2021-01-01
	 * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/filters/block-filters/#managing-block-categories
	 */
	public function set_blocks_category( $categories, $post ) : array {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'dwr-blocks',
					'title' => esc_html__( 'DWR Blocks', 'dwr-blocks' ),
				),
			)
		);
	}
}
DWR_Blocks::get_instance()->init();
