<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="login-beauty">

<div class="login-beauty--image">
	<img src="<?php echo get_template_directory_uri(); ?>/assets/images/farmers/login.jpg" class="login-beauty--image__img" alt="Registrati ad Agrispesa" />
</div>


<div class="login-beauty--forms">
<div class="beautiful-forms">

	<div class="password-form">
		<h2 class="beautiful-forms--title">Hai perso la password?</h2>
		<p class="beautiful-forms--message">Nessun problema. Inserisci la tua email, riceverai un link per generarne una nuova.</p>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">


	<p class="woocommerce-form-row">
		<label for="user_login"><?php esc_html_e( 'Indirizzo email', 'woocommerce' ); ?></label>
		<input class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Inserisci l'email che usi per accedere'" type="text" name="user_login" id="user_login" autocomplete="username" />
	</p>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<p class="woocommerce-form-row form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button style="margin-top: 16px;" type="submit" class="btn btn-primary" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Recupera password', 'woocommerce' ); ?></button>
	</p>

	<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

</form>
</div>
</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
