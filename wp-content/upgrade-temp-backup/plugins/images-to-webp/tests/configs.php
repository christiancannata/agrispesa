<?php

defined('ABSPATH') || exit;

$code = 0;
$filesize = 999;
$test_image = plugins_url( 'test.png', __FILE__ );

$url = $test_image . '?no_cache=' . time();

$handle = curl_init();

$proxy = new WP_HTTP_Proxy();

if( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ){
	curl_setopt( $handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
	curl_setopt( $handle, CURLOPT_PROXY, $proxy->host() );
	curl_setopt( $handle, CURLOPT_PROXYPORT, $proxy->port() );

	if( $proxy->use_authentication() ){
		curl_setopt( $handle, CURLOPT_PROXYAUTH, CURLAUTH_ANY );
		curl_setopt( $handle, CURLOPT_PROXYUSERPWD, $proxy->authentication() );
	}
}

$is_local = isset( $parsed_args['local'] ) && $parsed_args['local'];
$ssl_verify = isset( $parsed_args['sslverify'] ) && $parsed_args['sslverify'];
if( $is_local ){
	$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify, $url );
}elseif( ! $is_local ){
	$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify, $url );
}

curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 120 );
curl_setopt( $handle, CURLOPT_TIMEOUT, 120 );

curl_setopt( $handle, CURLOPT_URL, $url );

curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, ( true === $ssl_verify ) ? 2 : false );
curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify );

curl_setopt( $handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36' );
curl_setopt( $handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );

curl_setopt( $handle, CURLOPT_HEADER, TRUE );
curl_setopt( $handle, CURLOPT_HTTPHEADER, array(
	'Cache-Control: no-cache',
	'Accept: text/html,application/xhtml+xml,image/webp,image/apng',
	'Custom-Header: text/html,application/xhtml+xml,image/webp,image/apng'
));
curl_setopt( $handle, CURLOPT_FRESH_CONNECT, TRUE );
curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, TRUE );
curl_setopt( $handle, CURLOPT_RETURNTRANSFER, TRUE );
curl_setopt( $handle, CURLOPT_AUTOREFERER, TRUE );

curl_setopt( $handle, CURLOPT_MAXREDIRS, 10 );
curl_setopt( $handle, CURLOPT_REFERER, site_url() );

curl_exec( $handle );

if( curl_errno( $handle ) ){
	deactivate_plugins( __DIR__ );
	wp_die( 'error:' . curl_error( $handle ) );
}else{
	$code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
	$filesize = curl_getinfo( $handle, CURLINFO_SIZE_DOWNLOAD );
}
curl_close( $handle );

if( $code == 200 ){
	if( $filesize > 100 ){
		deactivate_plugins( __DIR__ );

		$is_nginx = ( strpos( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false );

		if( $is_nginx ){
			wp_die(
				__( 'You need to add this map directive to your http config, usually nginx.conf ( inside of the http{} section ):', 'images-to-webp' ) . 
				'<pre><code style="user-select:all">' . 
				htmlentities(
					'map $arg_no_webp $no_webp{' . PHP_EOL . 
				    '	default	"";' . PHP_EOL . 
					'	"1"		"no_webp";' . PHP_EOL . 
					'}' . PHP_EOL . 
					PHP_EOL . 
					'map $http_accept $webp_suffix{' . PHP_EOL . 
					'	default "";' . PHP_EOL . 
					'	"~*webp" ".webp";' . PHP_EOL . 
					'}' . PHP_EOL
				) . 
				'</code></pre><br>' . 
				__( 'then you need to add this to your server block, usually site.conf or /nginx/sites-enabled/default ( inside of the server{} section ):', 'images-to-webp' ) . 
				'<pre><code style="user-select:all">' . 
				htmlentities(
					'location ~* ^/.+\.(png|gif|jpe?g)$ {' . PHP_EOL . 
					'	add_header Vary Accept;' . PHP_EOL . 
					'	try_files $uri$webp_suffix$no_webp $uri =404;' . PHP_EOL . 
					'}'
				) . 
				'</code></pre><br>' . 
				__( 'and the most important: RESTART YOUR SERVER after these changes!', 'images-to-webp' ) . 
				'<br>' . 
				__( 'Then try to activate Images to WebP again ;)', 'images-to-webp' )
			);
		}else{
			wp_die(
				__( 'Add the following code to your <code>.htaccess</code> file above <code># BEGIN WordPress</code>,', 'images-to-webp' ) . 
				'<br>' . 
				__( 'then try to activate Images to WebP again:', 'images-to-webp' ) . 
				'<pre><code style="user-select:all">' . htmlentities( $this->generate_mod_rewrite_rules() ) . '</code></pre>' . 
				'<br>' . 
				__( "If you have a proxy setup or some combination of NGiNX and Apache on your server, you may probably need to disable NGiNX direct processing of image static files.", 'images-to-webp' ) . 
				'<br><br>' . 
				__( "If you have these lines in .htaccess and you still see this message, then there is some other problem with your server configuration.", 'images-to-webp' )
			);
		}
	}
}else{
	deactivate_plugins( __DIR__ );
	wp_die( sprintf( __( 'Testing file <code>%1$s</code> is not accessible, but returns HTTP response code <code>%2$d</code>. Contact your hosting provider or developer.', 'images-to-webp' ), $test_image, $code ) );
}