#  ⚡️ WC REST JSON to S3
This is a simple plugin that writes the WooCommerce products REST API endpoint response to a JSON file. Then, it uploads that file to Amazon S3. 

### Why would you need this?
This plugin arose out of creating a headless WordPress/WooCommerce app with React and facing incredibly slow calls to the WP REST API. 

Nothing I tried to lessen the load times was working for me so why not load a json file with endpoint data from Amazon S3? If you could make sure that the file was updated when any product post was saved, the file would always be up-to-date and loading it from S3 would have to be quicker than an API call. And it was! Or is! Or...you know what I mean.

**WC REST JSON to S3 was born.**

The plugin includes an updated version of the fantastic `S3.php` class from Donovan Schönknecht which uses the v4 authentication method. For reference, that is found here: https://github.com/racklin/amazon-s3-php-class

### Installation
Install like any other WordPress plugin. Or copy the main function to the `functions.php` file of your active theme. 

If you need multiple endpoints written to json, duplicate the functions and change the function names and parameters for your needs.

<br />

## What to edit in the plugin file
You will need to edit the following bits in order for this to work for your setup:

<br />

### In `rest_api_products_to_json()`:

**Edit your endpoint:**
```php
$request = new WP_REST_Request( 'GET', '/wc/v2/products' );
```

<br />

**Edit your REST API query parameters:**

Note that the parameters for a REST API call are different than `WP_QUERY` arguments.
```php
$request->set_query_params(

    [ 
        'per_page' => 250,
        'status'   => 'publish'
    ]

);
```
<br />

**Edit your file path (where you want your json file to be written to):**
```php
$path = get_template_directory() . '/path/to/file/';
```

<br />

**Edit your file name (optional):**
```php
$file_name = 'wc_products' . '.json';
```

<br />

---

### In `upload_json_to_s3()`:

<br />

**Edit the json file path/location:**
```php
$file = get_template_directory() . '/path/to/file/wc_products.json';
```

<br />

**Edit your S3 information:**
```php
define('AWS_S3_KEY', 'YOUR_S3_KEY');
define('AWS_S3_SECRET', 'YOUR_S3_SECRET_KEY');
define('AWS_S3_REGION', 'us-east-2');
define('AWS_S3_BUCKET', 'yourbucket');
define('AWS_S3_URL', 'https://s3.'.AWS_S3_REGION.'.amazonaws.com/'.AWS_S3_BUCKET.'/');
```
<br />

**Edit your bucket path:**
```php
S3::putObject(S3::inputFile($file), AWS_S3_BUCKET, 'path/in/bucket/' . $file, S3::ACL_PUBLIC_READ);
```

<br />

Depending on your file paths and S3 bucket, once the file is uploaded, grab the url from Amazon S3 for your file and then use that in your API call. It *should* be much faster than a regular WP REST API call.

<br />

---
**Notes:** 

You can certainly reconfigure it to work with any REST API endpoint: posts, pages, menus, or custom post types by just changing the request endpoint and query parameters in the `wc-rest-json-s3.php` file.

<br />

---

**Important:**

Use at your own risk. Internal calls to the REST API **bypass authentication** so be aware of this if you use it on a client site. Also, this uploads the file to be **publicly** read from S3 so do not include any sensitive data.

<br />

---

**Resources:**

https://wpscholar.com/blog/internal-wp-rest-api-calls/

https://medium.com/@martindrapeau/simple-php-code-to-push-files-to-aws-s3-3396f9b3d02a

