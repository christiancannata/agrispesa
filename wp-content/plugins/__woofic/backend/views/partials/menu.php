<ul>
	<li

	><a <?php if ( $args['active_route'] == 'woofic' ): ?>
			class="active"
		<?php endif; ?> href="/wp-admin/admin.php?page=woofic"><?php esc_html_e( 'Generale', W_TEXTDOMAIN ); ?></a></li>
	<li

	>
		<a <?php if ( $args['active_route'] == 'sync-payments-methods' ): ?>
			class="active"
		<?php endif; ?>
				href="/wp-admin/admin.php?page=woofic-sync-payments-methods"><?php esc_html_e( 'Metodi di Pagamento', W_TEXTDOMAIN ); ?></a>
	</li>
	<li

	><a <?php if ( $args['active_route'] == 'sync-vat' ): ?>
			class="active"
		<?php endif; ?>
				href="/wp-admin/admin.php?page=woofic-sync-vat"><?php esc_html_e( 'Aliquote', W_TEXTDOMAIN ); ?></a>
	</li>
	<li

	><a <?php if ( $args['active_route'] == 'advanced' ): ?>
			class="active"
		<?php endif; ?>
				href="/wp-admin/admin.php?page=woofic-advanced"><?php esc_html_e( 'Avanzate', W_TEXTDOMAIN ); ?></a>
	</li>
</ul>
