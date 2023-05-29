<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="tabs" class="settings-tab">
		<?php include( plugin_dir_path( __FILE__ ) . 'partials/menu.php' ) ?>


		<div id="<?php echo $args['active_route']; ?>" class="metabox-holder">


			<div class="postbox" style="max-width: inherit">
				<h2 class="title">Aliquote IVA</h2>

				<p>

				</p>
				<form method="POST" action="">
					<table class="table">
						<thead>
						<th>Nazione</th>
						<th>Aliquota</th>
						<th>Aliquota WooCommerce</th>
						<th>Aliquota FattureInCloud</th>
						</thead>
						<tbody>
						<?php foreach ( $args['aliquote'] as $tax ): ?>
							<tr>
								<td><?php echo $tax->tax_rate_country; ?></td>
								<td><?php echo number_format( $tax->tax_rate, 2 ); ?>%</td>
								<td><?php echo $tax->tax_rate_name; ?></td>
								<td>
									<select autocomplete="off" name="aliquote[<?php echo $tax->tax_rate_id; ?>]"
											class="form-control">
										<?php foreach ( $args['fic_aliquote'] as $fixAliquota ): ?>
											<option value="<?php echo $fixAliquota->getId() ?>"
													<?php if ( $fixAliquota->getId() == $tax->fic_id ): ?> selected <?php endif; ?>
											><?php echo $fixAliquota->getValue() . '% ' . $fixAliquota->getDescription(); ?></option>
										<?php endforeach; ?>
									</select>
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
