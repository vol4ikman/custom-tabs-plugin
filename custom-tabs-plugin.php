<?php
/**
 * Plugin Name: Custom Tabs Plugin
 * Description: Responsive, accessible custom tabs section with shortcode support.
 * Version: 1.0.0
 * Author: Custom
 * Text Domain: custom-tabs-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Custom_Tabs_Plugin {
	const OPTION_KEY    = 'custom_tabs_plugin_options';
	const ACF_GROUP_KEY = 'group_custom_tabs_settings';

	private $admin_menu_hook = '';
	private $version         = '';

	public function __construct() {
		$this->version = 'v-' . time();

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'acf/init', array( $this, 'register_acf_field_group' ) );
		add_shortcode( 'custom_tabs_plugin', array( $this, 'render_shortcode' ) );
	}

	public function register_assets() {
		wp_register_style(
			'custom-tabs-plugin-fonts',
			'https://use.typekit.net/wuz0gtr.css',
			array(),
			$this->version
		);

		wp_register_style(
			'custom-tabs-plugin-slick',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
			array(),
			'1.8.1'
		);

		wp_register_style(
			'custom-tabs-plugin-styles',
			plugin_dir_url( __FILE__ ) . 'assets/css/custom-tabs-plugin.min.css',
			array( 'custom-tabs-plugin-fonts', 'custom-tabs-plugin-slick' ),
			$this->version
		);

		wp_register_script(
			'custom-tabs-plugin-slick',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
			array( 'jquery' ),
			'1.8.1',
			true
		);

		wp_register_script(
			'custom-tabs-plugin-script',
			plugin_dir_url( __FILE__ ) . 'assets/js/custom-tabs-plugin.js',
			array( 'jquery', 'custom-tabs-plugin-slick' ),
			$this->version,
			true
		);
	}

	private function get_default_tabs() {
		return array(
			array(
				'title'               => __( 'Faster launches with less rework', 'custom-tabs-plugin' ),
				'quote'               => __( 'The new workflow gave our team structure and speed. We shipped in weeks, not months.', 'custom-tabs-plugin' ),
				'user_avatar'         => 'https://via.placeholder.com/72',
				'user_name'           => __( 'Alex Carter', 'custom-tabs-plugin' ),
				'user_role'           => __( 'Head of Product', 'custom-tabs-plugin' ),
				'user_company'        => 'https://via.placeholder.com/140x40?text=Northpeak+Labs',
				'description'         => __( 'Align strategy, design, and engineering around clear milestones so every sprint moves the product forward.', 'custom-tabs-plugin' ),
				'percent'             => '72%',
				'percent_description' => __( 'Reduction in time-to-release after adopting the process.', 'custom-tabs-plugin' ),
				'tab_image'           => 'https://via.placeholder.com/1200x700',
				'tab_image_mobile'    => 'https://via.placeholder.com/720x900',
				'cta_text'            => __( 'Read Case Study', 'custom-tabs-plugin' ),
				'cta_url'             => '#',
			),
			array(
				'title'               => __( 'Consistent UX across every release', 'custom-tabs-plugin' ),
				'quote'               => __( 'From onboarding to checkout, our interface finally feels cohesive and intentional.', 'custom-tabs-plugin' ),
				'user_avatar'         => 'https://via.placeholder.com/72',
				'user_name'           => __( 'Priya Singh', 'custom-tabs-plugin' ),
				'user_role'           => __( 'Design Lead', 'custom-tabs-plugin' ),
				'user_company'        => 'https://via.placeholder.com/140x40?text=Brightlane',
				'description'         => __( 'Build a reusable design language that shortens decision cycles and improves visual quality at scale.', 'custom-tabs-plugin' ),
				'percent'             => '58%',
				'percent_description' => __( 'Faster design-to-dev handoff with fewer QA revisions.', 'custom-tabs-plugin' ),
				'tab_image'           => 'https://via.placeholder.com/1200x700',
				'tab_image_mobile'    => 'https://via.placeholder.com/720x900',
				'cta_text'            => __( 'View Design Outcome', 'custom-tabs-plugin' ),
				'cta_url'             => '#',
			),
			array(
				'title'               => __( 'Reliable code, predictable delivery', 'custom-tabs-plugin' ),
				'quote'               => __( 'We moved from firefighting to shipping on schedule with confidence.', 'custom-tabs-plugin' ),
				'user_avatar'         => 'https://via.placeholder.com/72',
				'user_name'           => __( 'Daniel Moore', 'custom-tabs-plugin' ),
				'user_role'           => __( 'Engineering Manager', 'custom-tabs-plugin' ),
				'user_company'        => 'https://via.placeholder.com/140x40?text=Vertex+Systems',
				'description'         => __( 'Adopt standards, automation, and performance guardrails that keep releases stable as your product grows.', 'custom-tabs-plugin' ),
				'percent'             => '44%',
				'percent_description' => __( 'Decrease in post-release defects after pipeline improvements.', 'custom-tabs-plugin' ),
				'tab_image'           => 'https://via.placeholder.com/1200x700',
				'tab_image_mobile'    => 'https://via.placeholder.com/720x900',
				'cta_text'            => __( 'See Delivery Metrics', 'custom-tabs-plugin' ),
				'cta_url'             => '#',
			),
			array(
				'title'               => __( 'Data that drives smarter decisions', 'custom-tabs-plugin' ),
				'quote'               => __( 'We finally know which experiments move the needle and which to stop early.', 'custom-tabs-plugin' ),
				'user_avatar'         => 'https://via.placeholder.com/72',
				'user_name'           => __( 'Maya Lopez', 'custom-tabs-plugin' ),
				'user_role'           => __( 'Growth Analyst', 'custom-tabs-plugin' ),
				'user_company'        => 'https://via.placeholder.com/140x40?text=Summit+Commerce',
				'description'         => __( 'Turn user behavior and experiment outcomes into a practical roadmap for product and marketing teams.', 'custom-tabs-plugin' ),
				'percent'             => '36%',
				'percent_description' => __( 'Increase in conversion rate after insight-led optimization.', 'custom-tabs-plugin' ),
				'tab_image'           => 'https://via.placeholder.com/1200x700',
				'tab_image_mobile'    => 'https://via.placeholder.com/720x900',
				'cta_text'            => __( 'Explore Insights', 'custom-tabs-plugin' ),
				'cta_url'             => '#',
			),
		);
	}

	private function get_default_settings() {
		return array(
			'gallery' => array(),
			'tabs'    => $this->get_default_tabs(),
		);
	}

	private function get_settings() {
		$defaults = $this->get_default_settings();

		if ( function_exists( 'get_field' ) ) {
			$acf_settings = $this->get_acf_settings( $defaults );

			if ( ! empty( $acf_settings ) ) {
				return $acf_settings;
			}
		}

		$saved_settings = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $saved_settings ) ) {
			return $defaults;
		}

		$settings = array(
			'gallery' => isset( $saved_settings['gallery'] ) ? $this->normalize_gallery_urls( $saved_settings['gallery'] ) : $defaults['gallery'],
			'tabs'    => array(),
		);

		foreach ( $defaults['tabs'] as $index => $default_tab ) {
			$saved_tab = array();

			if ( isset( $saved_settings['tabs'][ $index ] ) && is_array( $saved_settings['tabs'][ $index ] ) ) {
				$saved_tab = $saved_settings['tabs'][ $index ];
			}

			$settings['tabs'][] = array(
				'title'               => ! empty( $saved_tab['title'] ) ? (string) $saved_tab['title'] : $default_tab['title'],
				'quote'               => ! empty( $saved_tab['quote'] ) ? (string) $saved_tab['quote'] : $default_tab['quote'],
				'user_avatar'         => ! empty( $saved_tab['user_avatar'] ) ? (string) $saved_tab['user_avatar'] : $default_tab['user_avatar'],
				'user_name'           => ! empty( $saved_tab['user_name'] ) ? (string) $saved_tab['user_name'] : $default_tab['user_name'],
				'user_role'           => ! empty( $saved_tab['user_role'] ) ? (string) $saved_tab['user_role'] : $default_tab['user_role'],
				'user_company'        => ! empty( $saved_tab['user_company'] ) ? esc_url_raw( $saved_tab['user_company'] ) : $default_tab['user_company'],
				'description'         => ! empty( $saved_tab['description'] ) ? (string) $saved_tab['description'] : $default_tab['description'],
				'percent'             => ! empty( $saved_tab['percent'] ) ? (string) $saved_tab['percent'] : $default_tab['percent'],
				'percent_description' => ! empty( $saved_tab['percent_description'] ) ? (string) $saved_tab['percent_description'] : $default_tab['percent_description'],
				'tab_image'           => ! empty( $saved_tab['tab_image'] ) ? esc_url_raw( $saved_tab['tab_image'] ) : $default_tab['tab_image'],
				'tab_image_mobile'    => ! empty( $saved_tab['tab_image_mobile'] ) ? esc_url_raw( $saved_tab['tab_image_mobile'] ) : $default_tab['tab_image_mobile'],
				'cta_text'            => ! empty( $saved_tab['cta_text'] ) ? (string) $saved_tab['cta_text'] : $default_tab['cta_text'],
				'cta_url'             => ! empty( $saved_tab['cta_url'] ) ? (string) $saved_tab['cta_url'] : $default_tab['cta_url'],
			);
		}

		return $settings;
	}

	private function normalize_image_url( $image_value ) {
		if ( is_string( $image_value ) ) {
			return esc_url_raw( $image_value );
		}

		if ( is_numeric( $image_value ) ) {
			$image_src = wp_get_attachment_image_url( (int) $image_value, 'full' );

			return $image_src ? esc_url_raw( $image_src ) : '';
		}

		if ( is_array( $image_value ) && ! empty( $image_value['url'] ) ) {
			return esc_url_raw( $image_value['url'] );
		}

		return '';
	}

	private function normalize_gallery_urls( $gallery_value ) {
		$urls = array();

		if ( ! is_array( $gallery_value ) ) {
			return $urls;
		}

		foreach ( $gallery_value as $image_value ) {
			$image_url = $this->normalize_image_url( $image_value );

			if ( '' !== $image_url ) {
				$urls[] = $image_url;
			}

			if ( count( $urls ) >= 5 ) {
				break;
			}
		}

		return $urls;
	}

	private function get_acf_settings( $defaults ) {
		$acf_tabs = get_field( 'custom_tabs_tabs', 'option' );
		$gallery  = $this->normalize_gallery_urls( get_field( 'custom_tabs_gallery', 'option' ) );

		$settings = array(
			'gallery' => ! empty( $gallery ) ? $gallery : $defaults['gallery'],
			'tabs'    => array(),
		);

		if ( is_array( $acf_tabs ) && ! empty( $acf_tabs ) ) {
			foreach ( $acf_tabs as $index => $tab ) {
				$default_tab = isset( $defaults['tabs'][ $index ] ) ? $defaults['tabs'][ $index ] : $defaults['tabs'][0];
				$avatar_url  = isset( $tab['user_avatar'] ) ? $this->normalize_image_url( $tab['user_avatar'] ) : '';
				$company_url = isset( $tab['user_company'] ) ? $this->normalize_image_url( $tab['user_company'] ) : '';
				$tab_image   = isset( $tab['tab_image'] ) ? $this->normalize_image_url( $tab['tab_image'] ) : '';
				$tab_mobile  = isset( $tab['tab_image_mobile'] ) ? $this->normalize_image_url( $tab['tab_image_mobile'] ) : '';

				if ( '' === $avatar_url ) {
					$avatar_url = $default_tab['user_avatar'];
				}

				if ( '' === $company_url ) {
					$company_url = $default_tab['user_company'];
				}

				if ( '' === $tab_image ) {
					$tab_image = $default_tab['tab_image'];
				}

				if ( '' === $tab_mobile ) {
					$tab_mobile = $default_tab['tab_image_mobile'];
				}

				$settings['tabs'][] = array(
					'title'               => ! empty( $tab['title'] ) ? sanitize_text_field( $tab['title'] ) : $default_tab['title'],
					'quote'               => ! empty( $tab['quote'] ) ? wp_kses_post( $tab['quote'] ) : $default_tab['quote'],
					'user_avatar'         => $avatar_url,
					'user_name'           => ! empty( $tab['user_name'] ) ? sanitize_text_field( $tab['user_name'] ) : $default_tab['user_name'],
					'user_role'           => ! empty( $tab['user_role'] ) ? sanitize_text_field( $tab['user_role'] ) : $default_tab['user_role'],
					'user_company'        => $company_url,
					'description'         => ! empty( $tab['description'] ) ? sanitize_text_field( $tab['description'] ) : $default_tab['description'],
					'percent'             => ! empty( $tab['percent'] ) ? sanitize_text_field( $tab['percent'] ) : $default_tab['percent'],
					'percent_description' => ! empty( $tab['percent_description'] ) ? sanitize_text_field( $tab['percent_description'] ) : $default_tab['percent_description'],
					'tab_image'           => $tab_image,
					'tab_image_mobile'    => $tab_mobile,
					'cta_text'            => ! empty( $tab['cta_text'] ) ? sanitize_text_field( $tab['cta_text'] ) : $default_tab['cta_text'],
					'cta_url'             => ! empty( $tab['cta_url'] ) ? esc_url_raw( $tab['cta_url'] ) : $default_tab['cta_url'],
				);
			}
		}

		if ( empty( $settings['tabs'] ) ) {
			$settings['tabs'] = $defaults['tabs'];
		}

		return $settings;
	}

	public function register_admin_menu() {
		$this->admin_menu_hook = add_menu_page(
			__( 'Custom Tabs', 'custom-tabs-plugin' ),
			__( 'Custom Tabs', 'custom-tabs-plugin' ),
			'manage_options',
			'custom-tabs-plugin',
			array( $this, 'render_admin_page' ),
			'dashicons-index-card',
			58
		);

		if ( ! empty( $this->admin_menu_hook ) ) {
			add_action( 'load-' . $this->admin_menu_hook, array( $this, 'prepare_admin_page' ) );
		}
	}

	public function prepare_admin_page() {
		if ( function_exists( 'acf_form_head' ) ) {
			acf_form_head();
		}
	}

	public function register_acf_field_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$defaults = $this->get_default_settings();

		acf_add_local_field_group(
			array(
				'key'      => self::ACF_GROUP_KEY,
				'title'    => __( 'Custom Tabs settings', 'custom-tabs-plugin' ),
				'fields'   => array(
					array(
						'key'          => 'field_custom_tabs_tabs',
						'label'        => __( 'Tabs', 'custom-tabs-plugin' ),
						'name'         => 'custom_tabs_tabs',
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Add Tab', 'custom-tabs-plugin' ),
						'sub_fields'   => array(
							array(
								'key'   => 'field_custom_tabs_tab_title',
								'label' => __( 'Title', 'custom-tabs-plugin' ),
								'name'  => 'title',
								'type'  => 'text',
							),
							array(
								'key'          => 'field_custom_tabs_tab_quote',
								'label'        => __( 'Quote', 'custom-tabs-plugin' ),
								'name'         => 'quote',
								'type'         => 'wysiwyg',
								'tabs'         => 'visual',
								'toolbar'      => 'basic',
								'media_upload' => 0,
							),
							array(
								'key'           => 'field_custom_tabs_tab_user_avatar',
								'label'         => __( 'User Avatar', 'custom-tabs-plugin' ),
								'name'          => 'user_avatar',
								'type'          => 'image',
								'return_format' => 'url',
								'preview_size'  => 'thumbnail',
								'library'       => 'all',
							),
							array(
								'key'   => 'field_custom_tabs_tab_user_name',
								'label' => __( 'User Name', 'custom-tabs-plugin' ),
								'name'  => 'user_name',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_custom_tabs_tab_user_role',
								'label' => __( 'User Role', 'custom-tabs-plugin' ),
								'name'  => 'user_role',
								'type'  => 'text',
							),
							array(
								'key'           => 'field_custom_tabs_tab_user_company',
								'label'         => __( 'User Company', 'custom-tabs-plugin' ),
								'name'          => 'user_company',
								'type'          => 'image',
								'return_format' => 'url',
								'preview_size'  => 'thumbnail',
								'library'       => 'all',
							),
							array(
								'key'   => 'field_custom_tabs_tab_description',
								'label' => __( 'Description', 'custom-tabs-plugin' ),
								'name'  => 'description',
								'type'  => 'textarea',
								'rows'  => 3,
							),
							array(
								'key'   => 'field_custom_tabs_tab_percent',
								'label' => __( 'Percent', 'custom-tabs-plugin' ),
								'name'  => 'percent',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_custom_tabs_tab_percent_description',
								'label' => __( 'Percent Description', 'custom-tabs-plugin' ),
								'name'  => 'percent_description',
								'type'  => 'textarea',
								'rows'  => 2,
							),
							array(
								'key'           => 'field_custom_tabs_tab_image',
								'label'         => __( 'Tab Image', 'custom-tabs-plugin' ),
								'name'          => 'tab_image',
								'type'          => 'image',
								'return_format' => 'url',
								'preview_size'  => 'medium',
								'library'       => 'all',
							),
							array(
								'key'           => 'field_custom_tabs_tab_image_mobile',
								'label'         => __( 'Tab Image Mobile', 'custom-tabs-plugin' ),
								'name'          => 'tab_image_mobile',
								'type'          => 'image',
								'return_format' => 'url',
								'preview_size'  => 'medium',
								'library'       => 'all',
							),
							array(
								'key'   => 'field_custom_tabs_tab_cta_text',
								'label' => __( 'CTA Text', 'custom-tabs-plugin' ),
								'name'  => 'cta_text',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_custom_tabs_tab_cta_url',
								'label' => __( 'CTA URL', 'custom-tabs-plugin' ),
								'name'  => 'cta_url',
								'type'  => 'url',
							),
						),
					),
					array(
						'key'           => 'field_custom_tabs_gallery',
						'label'         => __( 'Gallery', 'custom-tabs-plugin' ),
						'name'          => 'custom_tabs_gallery',
						'type'          => 'gallery',
						'return_format' => 'url',
						'preview_size'  => 'thumbnail',
						'library'       => 'all',
						'min'           => 0,
						'max'           => 5,
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'custom-tabs-plugin',
						),
					),
				),
			)
		);
	}

	public function register_settings() {
		register_setting(
			'custom_tabs_plugin_settings',
			self::OPTION_KEY,
			array( $this, 'sanitize_settings' )
		);
	}

	public function sanitize_settings( $input ) {
		$defaults = $this->get_default_settings();
		$output   = array(
			'gallery' => isset( $input['gallery'] ) ? $this->normalize_gallery_urls( $input['gallery'] ) : $defaults['gallery'],
			'tabs'    => array(),
		);

		foreach ( $defaults['tabs'] as $index => $default_tab ) {
			$tab_input = array();

			if ( isset( $input['tabs'][ $index ] ) && is_array( $input['tabs'][ $index ] ) ) {
				$tab_input = $input['tabs'][ $index ];
			}

			$cta_url = '#';
			if ( isset( $tab_input['cta_url'] ) ) {
				$cta_url = esc_url_raw( $tab_input['cta_url'] );
			}

			if ( '' === $cta_url ) {
				$cta_url = '#';
			}

			$output['tabs'][] = array(
				'title'               => isset( $tab_input['title'] ) ? sanitize_text_field( $tab_input['title'] ) : $default_tab['title'],
				'quote'               => isset( $tab_input['quote'] ) ? wp_kses_post( $tab_input['quote'] ) : $default_tab['quote'],
				'user_avatar'         => isset( $tab_input['user_avatar'] ) ? $this->normalize_image_url( $tab_input['user_avatar'] ) : $default_tab['user_avatar'],
				'user_name'           => isset( $tab_input['user_name'] ) ? sanitize_text_field( $tab_input['user_name'] ) : $default_tab['user_name'],
				'user_role'           => isset( $tab_input['user_role'] ) ? sanitize_text_field( $tab_input['user_role'] ) : $default_tab['user_role'],
				'user_company'        => isset( $tab_input['user_company'] ) ? $this->normalize_image_url( $tab_input['user_company'] ) : $default_tab['user_company'],
				'description'         => isset( $tab_input['description'] ) ? sanitize_text_field( $tab_input['description'] ) : $default_tab['description'],
				'percent'             => isset( $tab_input['percent'] ) ? sanitize_text_field( $tab_input['percent'] ) : $default_tab['percent'],
				'percent_description' => isset( $tab_input['percent_description'] ) ? sanitize_text_field( $tab_input['percent_description'] ) : $default_tab['percent_description'],
				'tab_image'           => isset( $tab_input['tab_image'] ) ? $this->normalize_image_url( $tab_input['tab_image'] ) : $default_tab['tab_image'],
				'tab_image_mobile'    => isset( $tab_input['tab_image_mobile'] ) ? $this->normalize_image_url( $tab_input['tab_image_mobile'] ) : $default_tab['tab_image_mobile'],
				'cta_text'            => isset( $tab_input['cta_text'] ) ? sanitize_text_field( $tab_input['cta_text'] ) : $default_tab['cta_text'],
				'cta_url'             => $cta_url,
			);
		}

		return $output;
	}

	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Custom Tabs', 'custom-tabs-plugin' ); ?></h1>

			<?php if ( ! function_exists( 'acf_form' ) ) : ?>
				<div class="notice notice-warning inline">
					<p><?php esc_html_e( 'Install and activate Advanced Custom Fields (ACF) to edit Custom Tabs settings from this page.', 'custom-tabs-plugin' ); ?></p>
				</div>
			<?php else : ?>
				<?php
				acf_form(
					array(
						'post_id'         => 'options',
						'field_groups'    => array( self::ACF_GROUP_KEY ),
						'submit_value'    => __( 'Save Settings', 'custom-tabs-plugin' ),
						'updated_message' => __( 'Custom Tabs settings saved.', 'custom-tabs-plugin' ),
					)
				);
				?>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_shortcode( $atts = array() ) {
		wp_enqueue_style( 'custom-tabs-plugin-styles' );
		wp_enqueue_script( 'custom-tabs-plugin-script' );

		$settings = $this->get_settings();
		$tabs     = apply_filters( 'custom_tabs_plugin_items', $settings['tabs'] );

		if ( ! is_array( $tabs ) || empty( $tabs ) ) {
			return '';
		}

		$instance_id = 'ctp-' . wp_generate_password( 8, false, false );

		ob_start();
		?>
		<section class="ctp" id="<?php echo esc_attr( $instance_id ); ?>" data-ctp-tabs>

			<div class="ctp__inner">

				<div class="ctp__tablist" role="tablist" aria-label="<?php esc_attr_e( 'Service tabs', 'custom-tabs-plugin' ); ?>">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<?php
						$tab_label = isset( $tab['label'] ) ? $tab['label'] : '';

						if ( '' === $tab_label && isset( $tab['title'] ) ) {
							$tab_label = $tab['title'];
						}
						?>
						<button
							type="button"
							class="ctp__tab<?php echo $index === 0 ? ' is-active' : ''; ?>"
							id="<?php echo esc_attr( $instance_id . '-tab-' . $index ); ?>"
							role="tab"
							aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $instance_id . '-panel-' . $index ); ?>"
							tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
						>
							<h3><?php echo esc_html( $tab_label ); ?></h3>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="ctp__panels">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<?php
						$tab_title               = isset( $tab['title'] ) ? (string) $tab['title'] : '';
						$tab_quote               = isset( $tab['quote'] ) ? (string) $tab['quote'] : '';
						$tab_user_avatar         = isset( $tab['user_avatar'] ) ? (string) $tab['user_avatar'] : '';
						$tab_user_name           = isset( $tab['user_name'] ) ? (string) $tab['user_name'] : '';
						$tab_user_role           = isset( $tab['user_role'] ) ? (string) $tab['user_role'] : '';
						$tab_user_company        = isset( $tab['user_company'] ) ? (string) $tab['user_company'] : '';
						$tab_description         = isset( $tab['description'] ) ? (string) $tab['description'] : '';
						$tab_percent             = isset( $tab['percent'] ) ? (string) $tab['percent'] : '';
						$tab_percent_description = isset( $tab['percent_description'] ) ? (string) $tab['percent_description'] : '';
						$tab_image               = isset( $tab['tab_image'] ) ? (string) $tab['tab_image'] : '';
						$tab_image_mobile        = isset( $tab['tab_image_mobile'] ) ? (string) $tab['tab_image_mobile'] : '';
						$tab_cta_url             = isset( $tab['cta_url'] ) ? (string) $tab['cta_url'] : '#';
						$tab_cta_text            = isset( $tab['cta_text'] ) ? (string) $tab['cta_text'] : '';

						?>
						<article
							class="ctp__panel<?php echo $index === 0 ? ' is-active' : ''; ?>"
							id="<?php echo esc_attr( $instance_id . '-panel-' . $index ); ?>"
							role="tabpanel"
							aria-labelledby="<?php echo esc_attr( $instance_id . '-tab-' . $index ); ?>"
							<?php echo $index === 0 ? '' : 'hidden'; ?>
						>
							<div class="left-side" style="<?php echo isset( $tab_image ) ? 'background-image: url(' . esc_url( $tab_image ) . ');' : ''; ?>">
								<div class="left-content">
									<svg xmlns="http://www.w3.org/2000/svg" width="34" height="28" viewBox="0 0 34 28" fill="none">
										<path d="M0 18C0 23.88 3.48 27.12 7.44 27.12C11.04 27.12 13.8 24.24 13.8 20.76C13.8 17.16 11.4 14.64 8.04 14.64C7.44 14.64 6.6 14.76 6.48 14.76C6.84 10.92 10.32 6.12 14.16 3.6L9.72 0C4.2 3.96 0 10.68 0 18ZM19.2 18C19.2 23.88 22.68 27.12 26.64 27.12C30.24 27.12 33.12 24.24 33.12 20.76C33.12 17.16 30.6 14.64 27.24 14.64C26.64 14.64 25.8 14.76 25.68 14.76C26.16 10.92 29.52 6.12 33.36 3.6L28.92 0C23.4 3.96 19.2 10.68 19.2 18Z" fill="black"/>
									</svg>
									<div class="quote">
										<?php echo wp_kses_post( $tab_quote ); ?>
									</div>

									<div class="user-metadata">
										<div class="avatar">
											<img src="<?php echo esc_url( $tab_user_avatar ); ?>" alt="<?php echo esc_attr( $tab_user_name ); ?>">
										</div>
										<div class="user-info">
											<div class="name"><?php echo esc_html( $tab_user_name ); ?></div>
											<div class="role"><?php echo esc_html( $tab_user_role ); ?></div>
										</div>
									</div>
									<?php if ( ! empty( $tab_user_company ) ) : ?>
										<div class="company">
											<img src="<?php echo esc_url( $tab_user_company ); ?>" alt="<?php echo esc_attr( $tab_user_name . ' company' ); ?>">
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="right-side">

								<?php if ( '' !== $tab_percent || '' !== $tab_percent_description ) : ?>
									<div class="percent-block">
										<?php if ( '' !== $tab_percent ) : ?>
											<div class="percent"><?php echo esc_html( $tab_percent ); ?></div>
										<?php endif; ?>
										<?php if ( '' !== $tab_percent_description ) : ?>
											<div class="percent-description"><?php echo esc_html( $tab_percent_description ); ?></div>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( '' !== $tab_cta_text ) : ?>
									<a class="ctp__cta" href="<?php echo esc_url( $tab_cta_url ); ?>">
										<span class="arrow">
											<svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" viewBox="0 0 46 46" fill="none">
												<path d="M16.6275 10.6274C15.5229 10.6274 14.6275 11.5229 14.6275 12.6274C14.6275 13.732 15.5229 14.6274 16.6275 14.6274L27.7991 14.6274L11.2133 31.2132C10.4322 31.9943 10.4322 33.2606 11.2133 34.0417C11.9943 34.8227 13.2607 34.8227 14.0417 34.0417L30.6275 17.4559L30.6275 28.6274C30.6275 29.732 31.5229 30.6274 32.6275 30.6274C33.7321 30.6274 34.6275 29.732 34.6275 28.6274V10.6274L16.6275 10.6274Z" fill="white"/>
											</svg>
										</span>
										<span class="text"><?php echo esc_html( $tab_cta_text ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( isset( $settings['gallery'] ) && is_array( $settings['gallery'] ) && ! empty( $settings['gallery'] ) ) : ?>
					<div class="ctp__gallery">
						<?php foreach ( $settings['gallery'] as $image_url ) : ?>
							<div class="ctp__gallery-item">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="">
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}
}

new Custom_Tabs_Plugin();
