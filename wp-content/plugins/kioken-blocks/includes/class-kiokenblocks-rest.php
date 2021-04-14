<?php
/**
 * Rest API functions
 *
 * @package @@plugin_name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Kioken_Rest
 */
class Kioken_Rest extends WP_REST_Controller {
    /**
     * Namespace.
     *
     * @var string
     */
    protected $namespace = 'kioken/v';

    /**
     * Version.
     *
     * @var string
     */
    protected $version   = '1';

    /**
     * KiokenBlocks_Rest constructor.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register rest routes.
     */
    public function register_routes() {
        $namespace = $this->namespace . $this->version;


        // Get attachment image <img> tag.
        register_rest_route(
            $namespace, '/get_attachment_image/(?P<id>[\d]+)', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_attachment_image' ),
                'permission_callback' => array( $this, 'get_attachment_image_permission' ),
            )
        );

        register_rest_route(
    			$namespace,
    			'/fetch_templates',
    			array(
    				array(
    					'methods'	=> \WP_REST_Server::READABLE,
    					'callback'	=> array( $this, 'fetch_templates' ),
                        'permission_callback' => '__return_true',
    				),
    			)
    		);

        register_rest_route(
    			$namespace,
    			'/import_template',
    			array(
    				array(
    					'methods'	=> \WP_REST_Server::READABLE,
    					'callback'	=> array( $this, 'import_template' ),
                        'permission_callback' => '__return_true',
    					'args'		=> array(
    						'url'	=> array(
    							'type'        => 'string',
    							'required'    => true,
    							'description' => __( 'URL of the JSON file.', 'textdomain' ),
    						),
    					),
    				),
    			)
    		);


    }

    /**
	 * Function to fetch templates.
	 *
	 * @return array|bool|\WP_Error
	 */
	public function fetch_templates( \WP_REST_Request $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

    $fetch = wp_remote_get( 'https://kk-elements.s3.eu-central-1.amazonaws.com/templates.json' );
    $json = wp_remote_retrieve_body( $fetch );

    $templates_list = json_decode($json, true);


		$templates = apply_filters( 'kioken_gutenberg_templates', $templates_list );

		return rest_ensure_response( $templates );
	}

	/**
	 * Function to fetch template JSON.
	 *
	 * @return array|bool|\WP_Error
	 */
	public function import_template( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		$url = $request->get_param( 'url' );

    $response = wp_remote_get( $url );
    $json = wp_remote_retrieve_body( $response );
		$obj = json_decode( $json );
		return rest_ensure_response( $obj );
	}

    /**
     * Get attachment image <img> tag permissions.
     *
     * @param WP_REST_Request $request  request object.
     *
     * @return bool
     */
    public function get_attachment_image_permission( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );

        if ( ! $id ) {
            return $this->error( 'no_id_found', __( 'Provide image ID.', '@@text_domain' ) );
        }
        return true;
    }

    /**
     * Get read google maps api key permissions.
     *
     * @return bool
     */
    public function update_google_maps_api_key_permission() {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return $this->error( 'user_dont_have_permission', __( 'User don\'t have permissions to change options.', '@@text_domain' ) );
        }
        return true;
    }

    /**
     * Get edit options permissions.
     *
     * @return bool
     */
    public function update_disabled_blocks_permission() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $this->error( 'user_dont_have_permission', __( 'User don\'t have permissions to change options.', '@@text_domain' ) );
        }
        return true;
    }

    /**
     * Get edit options permissions.
     *
     * @return bool
     */
    public function update_settings_permission() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $this->error( 'user_dont_have_permission', __( 'User don\'t have permissions to change options.', '@@text_domain' ) );
        }
        return true;
    }


    /**
     * Get attachment image <img> tag.
     *
     * @param WP_REST_Request $request  request object.
     *
     * @return mixed
     */
    public function get_attachment_image( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $size = $request->get_param( 'size' );
        $icon = $request->get_param( 'icon' );
        $attr = $request->get_param( 'attr' );

        $image = wp_get_attachment_image( $id, $size, $icon, $attr );

        if ( $image ) {
            return $this->success( $image );
        } else {
            return $this->error( 'no_image_found', __( 'Image not found.', '@@text_domain' ) );
        }
    }

    /**
     * Get Twitter API url result
     *
     * @param array $data - api request data.
     *
     * @return bool|mixed
     */
    public function request_api_twitter( $data ) {
        $data = array_merge( array(
            'url' => 'https://api.twitter.com/1.1/statuses/user_timeline.json',
            'consumer_key' => '',
            'consumer_secret' => '',
            'access_token' => '',
            'access_token_secret' => '',
            'screen_name' => '',
            'exclude_replies' => '',
            'include_rts' => '',
            'count' => '',
            'include_entities' => '',
        ), $data );

        $oauth = array(
            'oauth_consumer_key' => $data['consumer_key'],
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $data['access_token'],
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        );

        $base_info_url = $data['url'];

        if ( $data['screen_name'] ) {
            $data['url'] .= strpos( $data['url'], '?' ) !== false ? '&' : '?';
            $data['url'] .= 'screen_name=' . $data['screen_name'];
            $oauth['screen_name'] = $data['screen_name'];
        }
        if ( $data['exclude_replies'] ) {
            $data['url'] .= strpos( $data['url'], '?' ) !== false ? '&' : '?';
            $data['url'] .= 'exclude_replies=' . $data['exclude_replies'];
            $oauth['exclude_replies'] = $data['exclude_replies'];
        }
        if ( $data['include_rts'] ) {
            $data['url'] .= strpos( $data['url'], '?' ) !== false ? '&' : '?';
            $data['url'] .= 'include_rts=' . $data['include_rts'];
            $oauth['include_rts'] = $data['include_rts'];
        }
        if ( $data['count'] ) {
            $data['url'] .= strpos( $data['url'], '?' ) !== false ? '&' : '?';
            $data['url'] .= 'count=' . $data['count'];
            $oauth['count'] = $data['count'];
        }
        if ( $data['include_entities'] ) {
            $data['url'] .= strpos( $data['url'], '?' ) !== false ? '&' : '?';
            $data['url'] .= 'include_entities=' . $data['include_entities'];
            $oauth['include_entities'] = $data['include_entities'];
        }

        $base_info = $this->build_base_string( $base_info_url, 'GET', $oauth );
        $composite_key = rawurlencode( $data['consumer_secret'] ) . '&' . rawurlencode( $data['access_token_secret'] );
        $oauth_signature = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );
        $oauth['oauth_signature'] = $oauth_signature;

        // Make Requests.
        $header = array( $this->build_authorization_header( $oauth ), 'Expect:' );
        $options_buf = wp_remote_get(
            $data['url'], array(
                'headers' => implode( "\n", $header ),
                'sslverify' => false,
            )
        );

        if ( ! is_wp_error( $options_buf ) && isset( $options_buf['body'] ) ) {
            return json_decode( $options_buf['body'], true );
        } else {
            return false;
        }
    }

    /**
     * Build base string
     *
     * @param string $base_uri - url.
     * @param string $method - method.
     * @param array  $params - params.
     *
     * @return string
     */
    private function build_base_string( $base_uri, $method, $params ) {
        $r = array();
        ksort( $params );
        foreach ( $params as $key => $value ) {
            $r[] = "$key=" . rawurlencode( $value );
        }
        return $method . '&' . rawurlencode( $base_uri ) . '&' . rawurlencode( implode( '&', $r ) );
    }

    /**
     * Build authorization header
     *
     * @param array $oauth - auth data.
     *
     * @return string
     */
    private function build_authorization_header( $oauth ) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach ( $oauth as $key => $value ) {
            $values[] = "$key=\"" . rawurlencode( $value ) . '"';
        }
        $r .= implode( ', ', $values );
        return $r;
    }



    /**
     * Success rest.
     *
     * @param mixed $response response data.
     * @return mixed
     */
    public function success( $response ) {
        return new WP_REST_Response(
            array(
                'success' => true,
                'response' => $response,
            ), 200
        );
    }

    /**
     * Error rest.
     *
     * @param mixed $code     error code.
     * @param mixed $response response data.
     * @return mixed
     */
    public function error( $code, $response ) {
        return new WP_REST_Response(
            array(
                'error' => true,
                'success' => false,
                'error_code' => $code,
                'response' => $response,
            ), 401
        );
    }
}
new Kioken_Rest();

/**
 * Create API Order By Fields
 *
 * @since 1.1.0
 */
function kiokenblocks_register_rest_orderby_fields() {
	$post_type = Kioken_Init::kioken_get_post_types();

	foreach ( $post_type as $key => $type ) {
		add_filter( "rest_{$type['value']}_collection_params", 'kiokenblocks_add_orderby', 10, 1 );
	}
}

add_action( 'init', 'kiokenblocks_register_rest_orderby_fields' );

/**
 * Adds Order By values to Rest API
 *
 * @param object $params Parameters.
 * @since 1.1.0
 */
function kiokenblocks_add_orderby( $params ) {

	$params['orderby']['enum'][] = 'rand';
	$params['orderby']['enum'][] = 'comment_count';
	$params['orderby']['enum'][] = 'menu_order';

	return $params;
}
