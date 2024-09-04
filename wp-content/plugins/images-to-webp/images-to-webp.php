<?php
/*
	Plugin Name: Images to WebP
	Plugin URI: https://www.paypal.me/jakubnovaksl
	Description: Convert JPG, PNG and GIF images to WEBP, speed up your web
	Version: 4.7
	Author: KubiQ
	Author URI: https://kubiq.sk
	Text Domain: images-to-webp
	Domain Path: /languages
*/

defined('ABSPATH') || exit;

class images_to_webp{
	var $plugin_admin_page;
	var $settings;
	var $tab;
	var $extensions = array( 'jpg', 'jpeg', 'gif', 'png' );

	function __construct(){
		$this->settings = get_site_option('images_to_webp_settings');

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_menu', array( $this, 'plugin_menu_link' ) );
		add_action( 'wp_ajax_convert_old_images', array( $this, 'convert_old_images' ) );
		add_action( 'wp_ajax_itw_subdirectories', array( $this, 'itw_subdirectories' ) );
		add_action( 'wp_ajax_itw_get_all_subdirectories', array( $this, 'itw_get_all_subdirectories' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'mod_rewrite_rules', array( $this, 'mod_rewrite_rules' ), 77, 1 );
		add_filter( 'wp_delete_file', array( $this, 'wp_delete_file' ) );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'wp_update_attachment_metadata' ), 77, 2 );
		add_action( 'fly_image_created', array( $this, 'fly_images_to_webp' ), 10, 2 );
		add_action( 'bis_image_created', array( $this, 'bis_images_to_webp' ), 10, 2 );
	}

	function admin_enqueue_scripts( $hook ){
		if( $hook == 'media_page_images-to-webp' ){
			if( isset( $_GET['tab'] ) && $_GET['tab'] == 'convert' ){
				wp_enqueue_style( 'jstree', plugin_dir_url( __FILE__ ) . 'assets/jstree.min.css', array(), '3.2.1' );
				wp_enqueue_script( 'jstree', plugin_dir_url( __FILE__ ) . 'assets/jstree.min.js', array('jquery'), '3.2.1' );
				wp_add_inline_script( 'jstree', 'var transparency_status_message = "' . __( 'Please wait, converting your images is in progress...', 'images-to-webp' ) . '", error_message = "' . __( 'Error {{ERROR}}, trying to continue with missing images... ', 'images-to-webp' ) . '"' );
				wp_enqueue_script( 'itw_convert', plugin_dir_url( __FILE__ ) . 'assets/convert.js', array('jstree'), 1 );
			}
		}
	}

	function plugins_loaded(){
		load_plugin_textdomain( 'images-to-webp', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function activate(){
		// first run test
		include_once 'tests/server.php';
		// active flag
		update_site_option( 'active_images_to_webp', 1 );
		// maybe load default settings
		if( ! $this->settings = get_site_option( 'images_to_webp_settings', 0 ) ){
			$default_method = array_keys( $methods );
			$default_method = current( $default_method );
			$default_options = array(
				'upload_convert' => 1,
				'extensions' => $this->extensions,
				'webp_quality' => 85,
				'method' => $default_method,
				'delete_originals' => 0,
			);
			update_site_option( 'images_to_webp_settings', $default_options );
			$this->settings = $default_options;
		}
		// write .htaccess rules
		flush_rewrite_rules( true );
		// then test again
		include_once 'tests/configs.php';
	}

	function mod_rewrite_rules( $default_rules ){
		return PHP_EOL . $this->generate_mod_rewrite_rules() . PHP_EOL . PHP_EOL . $default_rules;
	}

	function generate_mod_rewrite_rules(){
		$rewrite_rules = '';
		if( get_site_option( 'active_images_to_webp', 0 ) ){
			if( count( $this->settings['extensions'] ) ){
				$this->settings['extensions'] = array_intersect( $this->extensions, $this->settings['extensions'] );
				$rewrite_rules =
					'# BEGIN Images to WebP' .

					PHP_EOL . '<IfModule mod_mime.c>' .
					PHP_EOL . 'AddType image/webp .webp' .
					PHP_EOL . '</IfModule>' .

					PHP_EOL . '<IfModule mod_rewrite.c>' .
					PHP_EOL . 'RewriteEngine On' .
					PHP_EOL . 'RewriteCond %{HTTP_ACCEPT} image/webp' .
					PHP_EOL . 'RewriteCond %{REQUEST_FILENAME} "/"' .
					PHP_EOL . 'RewriteCond %{REQUEST_FILENAME} "\.(' . implode( '|', $this->settings['extensions'] ) . ')$"' .
					PHP_EOL . 'RewriteCond %{REQUEST_FILENAME}\.webp -f' .
					PHP_EOL . 'RewriteCond %{QUERY_STRING} !no_webp' .
					PHP_EOL . 'RewriteRule ^(.+)$ $1\.webp [NC,T=image/webp,E=webp,L]' .
					PHP_EOL . '</IfModule>' .

					PHP_EOL . '# END Images to WebP';
			}
			$rewrite_rules = apply_filters( 'itw_htaccess', $rewrite_rules );
		}
		return $rewrite_rules;
	}

	function filter_plugin_actions( $links, $file ){
		array_unshift( $links, '<a href="upload.php?page=' . basename( __FILE__ ) . '">' . __('Settings') . '</a>' );
		return $links;
	}

	function plugin_menu_link(){
		$this->plugin_admin_page = add_submenu_page(
			'upload.php',
			__( 'Images to WebP', 'images-to-webp' ),
			__( 'Images to WebP', 'images-to-webp' ),
			'manage_options',
			basename( __FILE__ ),
			array( $this, 'admin_options_page' )
		);
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'filter_plugin_actions' ), 10, 2 );
	}

	function plugin_admin_tabs( $current = 'general' ){
		$tabs = array(
			'general' => __( 'General', 'images-to-webp' ),
			'convert' => __( 'Convert existing images', 'images-to-webp' ),
		); ?>
		<h2 class="nav-tab-wrapper">
		<?php foreach( $tabs as $tab => $name ){ ?>
			<a class="nav-tab <?php echo $tab == $current ? 'nav-tab-active' : '' ?>" href="?page=<?php echo basename( __FILE__ ) ?>&amp;tab=<?php echo esc_attr( $tab ) ?>"><?php echo esc_html( $name ) ?></a>
		<?php } ?>
		</h2><br><?php
	}

	function admin_options_page(){
		if( get_current_screen()->id != $this->plugin_admin_page ) return;
		$this->tab = isset( $_GET['tab'] ) && in_array( $_GET['tab'], array( 'general', 'convert' ) ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		if( isset( $_POST['plugin_sent'] ) ){
			if( check_admin_referer('itw_general') ){
				$this->settings = array();

				$this->settings['upload_convert'] = intval( $_POST['upload_convert'] );
				if( $this->settings['upload_convert'] !== 1 ){
					$this->settings['upload_convert'] = 0;
				}
				
				$this->settings['extensions'] = array_map( 'sanitize_text_field', $_POST['extensions'] );
				$this->settings['extensions'] = array_intersect( $this->extensions, $this->settings['extensions'] );
				
				$this->settings['webp_quality'] = intval( $_POST['webp_quality'] );
				if( $this->settings['webp_quality'] < 0 || $this->settings['webp_quality'] > 100 ){
					$this->settings['webp_quality'] = 100;
				}
				
				$this->settings['method'] = sanitize_text_field( $_POST['method'] );
				if( ! in_array( $this->settings['method'], array( 'gd', 'imagick' ) ) ){
					$this->settings['method'] = '';
				}

				$this->settings['delete_originals'] = intval( $_POST['delete_originals'] );
				if( $this->settings['delete_originals'] !== 1 ){
					$this->settings['delete_originals'] = 0;
				}
				
				update_site_option( 'images_to_webp_settings', $this->settings );
				flush_rewrite_rules( true );
			}
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Images to WebP', 'images-to-webp' ); ?></h2>
			<?php $this->plugin_admin_tabs( $this->tab ); ?>
			<?php include_once 'tabs/tab-' . $this->tab . '.php'; ?>
		</div><?php
	}

	function convert_image( $file ){
		if( is_file( $file ) ){
			$image_extension = pathinfo( $file, PATHINFO_EXTENSION );
			if( in_array( $image_extension, $this->settings['extensions'] ) ){
				require_once 'methods/method-' . $this->settings['method'] . '.php';
				$convert = new webp_converter();
				$response = $convert->convertImage( $file, $this->settings['webp_quality'] );
				if( $response ){
					if( $response['size']['after'] >= $response['size']['before'] ){
						unlink( $response['path'] );
						return false;
					}else{
						if( isset( $this->settings['delete_originals'] ) && $this->settings['delete_originals'] === 1 ){
							unlink( $file );
						}
					}
				}
				return true;
			}
		}
		return false;
	}

	function itw_subdirectories(){
		if( defined('DOING_AJAX') && DOING_AJAX ){
			if( current_user_can('administrator') ){
				if( check_ajax_referer('itw_convert') ){
					$ABSPATH = str_replace( '\\', '/', $this->get_abspath() );

					if( $_REQUEST['folder'] == '#' ){
						$dir = $ABSPATH;
					}else{
						$dir = $ABSPATH . $_REQUEST['folder'] . '/';
					}

					$response = array();
					$directories = scandir( $dir );
					foreach( $directories as $sub ){
						if( $sub != '.' && $sub != '..' && is_dir( $dir . $sub ) ){
							$id = explode( $ABSPATH, $dir . $sub );
							if( isset( $id[1] ) ){
								$response[] = array(
									'id' => esc_attr( $id[1] ),
									'parent' => esc_attr( $_REQUEST['folder'] ),
									'text' => esc_html( $sub ),
									'children' => true
								);
							}
						}
					}
					wp_send_json( $response );
				}
			}
		}
		exit();
	}

	function get_all_subdirectories( $folders, $base = false ){
		$ABSPATH = $this->get_abspath();
		if( $base === false ) $base = $ABSPATH;
		$all_folders = array();
		if( is_array( $folders ) ){
			foreach( $folders as $folder ){
				if( $folder != '.' && $folder != '..' ){
					$folder = sanitize_text_field( $folder );
					$folder = realpath( $base . $folder );
					if( is_dir( $folder ) ){
						$secure_path = realpath( $ABSPATH );
						$secure_path_len = strlen( $secure_path );
						if( substr( $folder, 0, $secure_path_len ) === $secure_path ){
							$all_folders[] = str_replace( $ABSPATH, '', $folder );
							$subfolders = scandir( $folder );
							$subfolders = $this->get_all_subdirectories( $subfolders, $folder . '/' );
							$all_folders = array_merge( $all_folders, $subfolders );
						}
					}
				}
			}
		}
		return $all_folders;
	}

	function itw_get_all_subdirectories(){
		if( defined('DOING_AJAX') && DOING_AJAX ){
			if( current_user_can('administrator') ){
				if( check_ajax_referer('itw_convert') ){
					$all_folders = $this->get_all_subdirectories( $_REQUEST['folders'] );
					$all_folders = array_unique( $all_folders );
					wp_send_json( $all_folders );
				}
			}
		}
		exit();
	}

	function convert_old_images(){
		if( defined('DOING_AJAX') && DOING_AJAX ){
			if( current_user_can('administrator') ){
				if( check_ajax_referer('itw_convert') ){
					$only_missing = intval( $_POST['only_missing'] );
					if( $only_missing !== 0 ){
						$only_missing = 1;
					}
					$ABSPATH = str_replace( '\\', '/', $this->get_abspath() );
					$folder = str_replace( ':\\\\', ':/', $_POST['folder'] );
					$folder = str_replace( '\\\\', '/', $folder );
					$folder = preg_replace( '#^' . $ABSPATH . '#', '', $folder );
					$folder = realpath( $ABSPATH . $folder );
					if( is_dir( $folder ) ){
						$secure_path = realpath( $ABSPATH );
						$secure_path_len = strlen( $secure_path );
						if( substr( $folder, 0, $secure_path_len ) === $secure_path ){
							$files = scandir( $folder );
							$converted = 0;
							foreach( $files as $file ){
								if( ! $only_missing || ! file_exists( $folder . '/' . $file . '.webp' ) ){
									$converted += $this->convert_image( $folder . '/' . $file ) ? 1 : 0;
								}
							}
							printf( __( '%d converted', 'images-to-webp' ), $converted );
						}
					}
				}
			}
		}
		exit();
	}

	function wp_delete_file( $path ){
		$source = $path . '.webp';
		if( is_writable( $source ) ) unlink( $source );
		return $path;
	}

	function wp_update_attachment_metadata( $data, $attachmentId ){
		if( $this->settings['upload_convert'] == 1 ){
			if( $data && isset( $data['file'] ) && isset( $data['sizes'] ) ){
				$upload = wp_upload_dir();
				$path = $upload['basedir'] . '/' . dirname( $data['file'] ) . '/';
				$sizes = array();
				$sizes['source'] = $upload['basedir'] . '/' . $data['file'];
				foreach( $data['sizes'] as $key => $size ){
					$url = $path . $size['file'];
					if( in_array( $url, $sizes ) ) continue;
					$sizes[ $key ] = $url;
				}

				$sizes = apply_filters( 'itw_sizes', $sizes, $attachmentId );

				foreach( $sizes as $size ){
					if( ! file_exists( $size . '.webp' ) ){
						$this->convert_image( $size );
					}
				}
			}
		}
		return $data;
	}

	function fly_images_to_webp( $attachment_id, $fly_file_path ){
		$this->convert_image( $fly_file_path );
	}

	function bis_images_to_webp( $attachment_id, $bis_file_path ){
		$this->convert_image( $bis_file_path );
	}

	function get_abspath(){
		return apply_filters( 'itw_abspath', ABSPATH );
	}
}

$images_to_webp = new images_to_webp();

register_activation_hook( __FILE__, array( $images_to_webp, 'activate' ) );
register_deactivation_hook( __FILE__, 'deactivate_images_to_webp' );
register_uninstall_hook( __FILE__, 'uninstall_images_to_webp' );

function deactivate_images_to_webp(){
	update_site_option( 'active_images_to_webp', 0 );
	flush_rewrite_rules( true );
}

function uninstall_images_to_webp(){
	delete_site_option('images_to_webp_settings');
	delete_site_option('active_images_to_webp');
}