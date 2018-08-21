<?php
/**
 * Plugin Name: WC REST JSON to S3
 * Plugin URI: https://studio.bio/
 * Description: Write WooCommerce REST API endpoint response data to JSON then upload to s3. File is updated on post (product) save.
 * Author: Joshua Michaels for studio.bio
 * Author URI: https://studio.bio
 * Version: 1.0.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package WCRJS3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Hook into save_post_product so every time we save or update, a new file is written
    // change to add_action('save_post' ...) for regular posts or pages
    add_action( 'save_post_product', 'rest_api_products_to_json', 10, 4 );

    // Write WooCommerce products to json file
    function rest_api_products_to_json() {

        // make GET request to the WC REST API products endpoint
        // see here: https://wpscholar.com/blog/internal-wp-rest-api-calls/
        // change second argument to your desired endpoint
        $request = new WP_REST_Request( 'GET', '/wc/v2/products' );

        // adjust your query parameters as necessary
        $request->set_query_params(

            [ 
                'per_page' => 250,
                'status'   => 'publish'
            ]

        );

        // handle response
        $response = rest_do_request( $request );
        $server = rest_get_server();
        $data = $server->response_to_data( $response, false );
        $json = wp_json_encode( $data );
    
        // change $path to wherever you want the file written to in your theme (or elsewhere)
        $path = get_template_directory() . '/path/to/file/';

        // you can name the file whatever you want
        $file_name = 'wc_products' . '.json';
        
        // write the file
        file_put_contents( $path . $file_name, $json );
        
        // call function to upload to Amazon s3
        upload_json_to_s3();

    }

    // upload file to Amazon s3
    function upload_json_to_s3() {

        $file = get_template_directory() . '/path/to/file/wc_products.json';
    
        define('AWS_S3_KEY', 'YOUR_S3_KEY');
        define('AWS_S3_SECRET', 'YOUR_S3_SECRET_KEY');
        // change to your preferred s3 region
        define('AWS_S3_REGION', 'us-east-2');
        define('AWS_S3_BUCKET', 'yourbucket');
        define('AWS_S3_URL', 'https://s3.'.AWS_S3_REGION.'.amazonaws.com/'.AWS_S3_BUCKET.'/');
    
        if (defined('AWS_S3_URL')) {

            require_once('S3.php');

            S3::setAuth(AWS_S3_KEY, AWS_S3_SECRET);
            S3::setRegion(AWS_S3_REGION);
            S3::setSignatureVersion('v4');
            S3::putObject(S3::inputFile($file), AWS_S3_BUCKET, 'path/in/bucket/'.$file, S3::ACL_PUBLIC_READ);

        }
    }

} else {
    // delete this if you are using this for other endpoints
    add_action( 'admin_notices', 'WCRJS3_woocommerce_notice' );
}

// delete this if you are using non-woocommerce endpoints
function WCRJS3_woocommerce_notice() { ?>

    <div class="error notice">
        <p><?php _e( 'WooCommerce needs to be installed and activated to run this plugin. Please activate WooCommerce.', 'textdomain' ); ?></p>
    </div>

<?php }
