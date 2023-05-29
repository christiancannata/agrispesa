<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="tabs" class="settings-tab">
		<?php include( plugin_dir_path( __FILE__ ) . 'partials/menu.php' ) ?>


		<div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


			<div class="postbox" style="max-width: inherit">
				<h2 class="title">Metodi di pagamento</h2>

				<p>
					<?php
					if ( empty( $args['fic_payments_methods'] ) ):
					?>
				<div class="alert alert-danger">Non ci sono metodi di pagamento su FattureInCloud!</div>
				<?php
				endif;
				?>

				</p>
				<form method="POST" action="">
					<table class="table">
						<thead>
						<th>Metodo di pagamento WooCommerce</th>
						<th>Metodo di pagamento FattureInCloud</th>
						</thead>
						<tbody>
						<?php foreach ( $args['payments_methods'] as $payment ): ?>
							<tr>
								<td><?php echo $payment['name']; ?></td>
								<td>
									<?php
									if ( empty( $args['fic_payments_methods'] ) || ! $payment['fic_id'] ): ?>
										<div class="alert alert-danger">Nessun metodo di pagamento associato.</div>
									<?php else:
										?>
										<select autocomplete="off" name="payment_methods[<?php echo $payment['id']; ?>]"
												class="form-control">
											<?php foreach ( $args['fic_payments_methods'] as $paymentMethod ): ?>
												<option value="<?php echo $paymentMethod->getId() ?>"
														<?php if ( $paymentMethod->getId() == $payment['fic_id'] ): ?> selected <?php endif; ?>
												><?php echo $paymentMethod->getName() ?></option>
											<?php endforeach; ?>
											<option value="0"
													<?php if ( $paymentMethod->getId() == 0 ): ?> selected <?php endif; ?>
											>Disabilita invio su Fatture in Cloud
											</option>
										</select>
									<?php
									endif;
									?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>

					</table>

					<button class="button button-primary button-large">Salva e sincronizza</button>
				</form>


			</div>


		</div>

	</div>

</div>
