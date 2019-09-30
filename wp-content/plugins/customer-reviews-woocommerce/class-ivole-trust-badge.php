<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('Ivole_Trust_Badge')) :

    class Ivole_Trust_Badge
    {

        /**
         * @var array holds the current shorcode attributes
         */
        public $shortcode_atts;

        public function __construct( $shortcode_atts )
        {
            $defaults = array(
                'type' => 'sl',
                'border' => 'yes'
            );
            if ( isset( $shortcode_atts['type'] ) ) {
                $type = str_replace( ' ', '', $shortcode_atts['type'] );
                $type = strtolower( $type );
                $allowed_types = array( 'sl', 'slp', 'sd', 'sdp', 'wl', 'wlp', 'wd', 'wdp' );
                if( in_array( $type, $allowed_types ) ) {
                  $shortcode_atts['type'] = $type;
                } else {
                  $shortcode_atts['type'] = null;
                }
            }
            if ( isset( $shortcode_atts['border'] ) ) {
                $border = str_replace( ' ', '', $shortcode_atts['border'] );
                $border = strtolower( $border );
                $allowed_borders = array( 'yes', 'no' );
                if( in_array( $border, $allowed_borders ) ) {
                  $shortcode_atts['border'] = $border;
                } else {
                  $shortcode_atts['border'] = 'yes';
                }
            }
            $this->shortcode_atts = shortcode_atts($defaults, $shortcode_atts);
            // load styles and js
            $this->ivole_style();
        }

        public function show_trust_badge()
        {
            $class_img = 'ivole-trustbadgefi-' . $this->shortcode_atts['type'] . ' ivole-trustbadgefi-b' . $this->shortcode_atts['border'];
            $return = '<div id="ivole_trustbadgef_' . $this->shortcode_atts['type'] . '" class="ivole-trustbadgef-' . $this->shortcode_atts['type'] . '">';
            $return .= '<a href="https://www.cusrev.com/reviews/' . get_option( 'ivole_reviews_verified_page', Ivole_Email::get_blogdomain() ) . '" rel="nofollow" target="_blank"><img id="ivole_trustbadgefi_' . $this->shortcode_atts['type'] . '" class="' . $class_img . '" src="' . add_query_arg( 't', time(), 'https://www.cusrev.com/badges/' . Ivole_Email::get_blogurl() . '-' . $this->shortcode_atts['type'] . '.png' ) . '"></a>';
            $return .= '</div>';
            return $return;
        }

        public function ivole_style()
        {
            wp_register_style('ivole-frontend-css', plugins_url('/css/frontend.css', __FILE__), array(), null, 'all');
            wp_enqueue_style('ivole-frontend-css');
        }

        /**
         * Registers the trustbadge block
         *
         * @since 3.53
         */
        public static function register_block() {
            // Only register the block if the WP is at least 5.0, or gutenberg is installed.
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'ivole/cusrev-trustbadge', array(
                    'attributes' => array(
                        'badge_size' => array(
                            'type' => 'string',
                            'enum' => array( 'small', 'wide' ),
                            'default' => 'small'
                        ),
                        'badge_style' => array(
                            'type' => 'string',
                            'enum' => array( 'light', 'dark' ),
                            'default' => 'light'
                        ),
                        'store_rating' => array(
                            'type' => 'boolean',
                            'default' => false
                        ),
                        'badge_border' => array(
                            'type' => 'boolean',
                            'default' => true
                        )
                    ),
                    'render_callback' => array( self::class, 'render_block' )
                ) );
            }
        }

        /**
         * Render the trust_badges block
         *
         * @since 3.53
         *
         * @param array $block_attributes An array of block attributes
         *
         * @return string
         */
        public static function render_block( $block_attributes ) {
            // If trust badges are not enabled, display nothing.
            if ( get_option( 'ivole_reviews_verified', 'no' ) === 'no' ) {
                return '';
            }

            $badge_type = $block_attributes['badge_size'] === 'small' ? 's' : 'w';
            $badge_type .= $block_attributes['badge_style'] === 'light' ? 'l' : 'd';
            $badge_type .= $block_attributes['store_rating'] ? 'p' : '';

            $badge_border = $block_attributes['badge_border'] ? 'yes': 'no';

            $verified_reviews_page = get_option( 'ivole_reviews_verified_page', Ivole_Email::get_blogdomain() );
            $badge_img_src = add_query_arg( 't', time(), 'https://www.cusrev.com/badges/' . Ivole_Email::get_blogurl() . '-' . $badge_type . '.png' );

            $class_img = 'ivole-trustbadgefi-' . $badge_type . ' ivole-trustbadgefi-b' . $badge_border;
            $return = '<div id="ivole_trustbadgef_' . $badge_type . '" class="ivole-trustbadgef-' . $badge_type . '">';
            $return .= '<a href="https://www.cusrev.com/reviews/' . $verified_reviews_page . '" rel="nofollow" target="_blank"><img id="ivole_trustbadgefi_' . $badge_type . '" class="' . $class_img . '" src="' . $badge_img_src . '"></a>';
            $return .= '</div>';

            return $return;
        }
        
    }

endif;
