<?php
//custom fields
$newsletter_title = get_field('newsletter_title', 'option');
$newsletter_subtitle = get_field('newsletter_subtitle', 'option');

?>

<section class="newsletter">
	<div class="newsletter--intro">
		<h3 class="newsletter--title"><?php echo $newsletter_title; ?></h3>
		<p class="newsletter--subtitle"><?php echo $newsletter_subtitle; ?></p>
	</div>

	<div class="mailchimp-form">

		<!-- Begin Mailchimp Signup Form -->
	<div class="newsletter--form" id="mc_embed_signup">

		<form action="https://agrispesa.us8.list-manage.com/subscribe/post?u=a601ffa5369b98db7030601ee&amp;id=cae80e4aed&amp;f_id=001468e0f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_self">
				<div id="mc_embed_signup_scroll" class="signup">
					<div class="mc-field-group">
						<input type="email" value="" name="EMAIL" placeholder="Il tuo indirizzo email" class="input-text required email" id="mce-EMAIL">
					</div>
					<div class="mc-field-group">
						<input type="text" value="" name="FNAME" class="input-text show-name" placeholder="Il tuo nome" id="mce-FNAME">
						<span id="mce-FNAME-HELPERTEXT" class="helper_text"></span>
					</div>

						<div id="mergeRow-gdpr" class="mergeRow gdpr-mergeRow content__gdprBlock mc-field-group">
								<div class="content__gdpr">
									<div class="checkbox-form form-agree">
										<input type="checkbox" id="gdpr_13" name="gdpr[13]" value="Y" class="av-checkbox">
										<label class="green" for="gdpr_13">Ho letto e accetto la <a href="<?php echo esc_url( home_url( '/' ) ); ?>privacy-policy" target="_blank" title="Privacy Policy">Privacy Policy</a></label>
									</div>
								 </div>
						</div>


					<div class="mailchimp-form--buttons">
						<input type="submit" value="Iscriviti" name="subscribe" id="mc-embedded-subscribe" class="btn btn-primary btb-small">
					</div>

					<div id="mce-responses" class="clear">
						<div class="response" id="mce-error-response" style="display:none"></div>
						<div class="response" id="mce-success-response" style="display:none"></div>
					</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
					<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_42fcc9d5ed6c3e5687d0de042_0cc342225e" tabindex="-1" value=""></div>

				</div>
		</form>
	</div>
	<script>
		function validateForm() {
			if (document.forms["mc-embedded-subscribe-form"]["gdpr_13"].checked === true) {
				jQuery('#mc-embedded-subscribe-form .form-agree').removeClass('not-valid');
			} else {
				jQuery('#mc-embedded-subscribe-form .form-agree').addClass('not-valid');
				return false;
			}
		}
	</script>
	<!--End mc_embed_signup-->
	</div>

</section>
