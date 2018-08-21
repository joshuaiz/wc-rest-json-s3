# WC REST JSON to S3
This is a simple plugin that writes the WooCommerce products REST API endpoint response to a JSON file. Then, it uploads that file to Amazon S3. 

### Why would you need this?
This plugin arose out of creating a headless WordPress/WooCommerce app with React and facing incredibly slow calls to the WP REST API. 

Nothing I tried to lessen the load times was working for me so why not load a json file with endpoint data from Amazon S3? If you could make sure that the file was updated when any product post was saved, the file would always be up-to-date and loading it from S3 would have to be quicker than an API call. And it was! Or is! Or...you know what I mean.

**WC REST JSON to S3 was born.**

The plugin includes an updated version of the fantastic `S3.php` class from Donovan Schönknecht which uses the v4 authentication method. For reference, that is found here: https://github.com/racklin/amazon-s3-php-class

**Notes:**

Install like any other WordPress plugin.

You can certainly reconfigure it to work with any REST API endpoint: posts, pages, menus, or custom post types by just changing the request endpoint and query parameters in the `wc-rest-json-s3.php` file.

You will need to edit this file anyway to add your Amazon S3 keys, bucket names and specify the path for your `.json` file.

