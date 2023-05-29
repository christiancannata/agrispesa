<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WooFic
 * @author    Christian Cannata <christian@christiancannata.com>
 * @copyright 2022 Christian Cannata
 * @license   GPL 2.0+
 * @link      https://christiancannata.com
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="tabs" class="settings-tab">
		<?php include( plugin_dir_path( __FILE__ ) . 'partials/menu.php' ) ?>


		<div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


			<div class="postbox" style="max-width: inherit">
				<h2 class="title">Chiave di Licenza WooFic</h2>

				<?php if ( ! $args['woofic_active_licence'] ): ?>
					<form method="POST" action="">
						<p>
							Il tuo sito non è collegato con WooFic, inserisci la tua chiave di licenza ricevuta.
						</p>
						<label>Chiave di Licenza</label><br>
						<input autocomplete="off" style="width:100%" type="text" name="woofic_licence_key" required

								<?php if ( get_option( 'woofic_licence_key' ) ): ?>
									value="<?php echo get_option( 'woofic_licence_key' ); ?>"
								<?php endif; ?>
						>
						<br><br>
						<label>Email di acquisto della licenza</label><br>
						<input autocomplete="off" style="width:100%" type="text" name="woofic_licence_email" required
								<?php if ( get_option( 'woofic_licence_email' ) ): ?>
									value="<?php echo get_option( 'woofic_licence_email' ); ?>"
								<?php endif; ?>
						>
						<br><br>
						<button class="button-primary" type="submit">Attiva la tua licenza</button>

					</form>
				<?php else: ?>
					<p>Correttamente collegato a Woofic</p>
					<br><br>
					<table>
						<tr>
							<td><b>Chiave di licenza</b></td>
							<td><?php echo $args['woofic_active_licence']['licenseKey'] ?></td>
						</tr>
						<tr>
							<td><b>Email</b></td>
							<td><?php echo get_option( 'woofic_license_email' ) ?></td>
						</tr>
						<tr>
							<td><b>Attivata il</b></td>
							<?php if ( $args['woofic_active_licence']['createdAt'] ): ?>
								<td> Attiva
									dal <?php echo ( new \DateTime( $args['woofic_active_licence']['createdAt'] ) )->format( "d-m-Y" ) ?> </td>
							<?php else: ?>
								<td>Non ancora attivata</td>
							<?php endif; ?>
						</tr>
						<tr>
							<td><b>Scade il</b></td>
							<td><?php echo ( new \DateTime( $args['woofic_active_licence']['expiresAt'] ) )->format( "d-m-Y" ) ?></td>
						</tr>
					</table><br><br>

					<a href="/wp-admin/admin.php?logout_woofic=1&page=woofic" class="button-primary">Disconnetti
						da
						WooFic</a>
				<?php endif; ?>
			</div>


			<?php if ( $args['woofic_licence_key'] ): ?>
				<div class="postbox" style="max-width: inherit">
					<h2 class="title">Configurazione del Client</h2>

					<p>
						Collega la tua Zoho APP al tuo sito Wordpress, per fare questo <a
								href="https://www.zoho.com/accounts/protocol/oauth-setup.html" target="_blank">leggi le
							istruzioni in
							questo link</a> e crea la tua app.<br>
					<ol>

					</ol>
					</p>

					<form method="POST" action="">

						<label>Client ID</label><br>
						<input autocomplete="off" style="width:100%" type="text" name="woofic_client_id" required

								<?php if ( get_option( 'woofic_client_id' ) ): ?>
									value="<?php echo get_option( 'woofic_client_id' ) ?>"
								<?php endif; ?>
						>
						<br><br>
						<label>Client Secret</label><br>
						<input autocomplete="off" style="width:100%" type="password" name="woofic_client_secret"
							   required

								<?php if ( get_option( 'woofic_client_secret' ) ): ?>
									value="<?php echo get_option( 'woofic_client_secret' ); ?>"
								<?php endif; ?>
						>
						<br><br>

						<label>Redirect URI</label><br>
						<input readonly autocomplete="off" style="width:100%" type="text" name="woofic_redirect_uri"
							   required

							   value="<?php echo get_site_url(); ?>/woofic-oauth-redirect"
						>
						<br><br>

						<button class="button-primary" type="submit">Salva i dati</button>

					</form>


				</div>
			<?php endif; ?>
			<?php if ( get_option( 'woofic_client_id' ) && get_option( 'woofic_client_secret' ) && get_option( 'woofic_redirect_uri' ) ): ?>
				<div class="postbox" style="max-width: inherit">
					<h2 class="title">Connessione a FattureInCloud</h2>

					<?php if ( ! $args['access_token'] ): ?>
						<p>
							Il tuo sito non è collegato con Zoho, effettua l'accesso di seguito per completare
							l'integrazione
						</p>
						<a class="button-primary" target="_blank" href="<?php echo $args['auth_url']; ?>">Accedi qui</a>
					<?php else: ?>
						<p>Correttamente collegato a FattureInCloud con token
							*******<?php echo substr( $args['access_token'], - 4 ); ?></p>

						<?php
						foreach ( get_option( 'woofic_companies' ) as $company ):
							?>
							<label>
								<input
										<?php if ( get_option( 'woofic_company_id' ) == $company->getId() ): ?> checked <?php endif; ?>
										type="radio" name="woofic_company_id" value="<?php echo $company->getId(); ?>">
								<?php echo $company->getName(); ?>
							</label>
						<?php endforeach; ?>
						<br><br>
						<a href="/wp-admin/admin.php?logout=1&page=woofic" class="button-primary">Disconnetti
							da
							FattureInCloud</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

	<div class="right-column-settings-page metabox-holder">
		<div class="postbox">
			<h3 class="hndle"><span><?php esc_html_e( 'WooFic.', W_TEXTDOMAIN ); ?></span></h3>
			<div class="inside">
				<a href="https://github.com/WPBP/WordPress-Plugin-Boilerplate-Powered"><img
							src="https://raw.githubusercontent.com/WPBP/boilerplate-assets/master/icon-256x256.png"
							alt=""></a>
			</div>
		</div>
	</div>
</div>
