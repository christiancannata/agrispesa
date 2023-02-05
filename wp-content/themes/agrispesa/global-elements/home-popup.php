<?php
//custom fields
$newsletter_title    = get_field( 'newsletter_title', 'option' );
$newsletter_subtitle = get_field( 'newsletter_subtitle', 'option' );
?>
<div class="popup newsletter-popup" id="home_popup" popup-name="popup-newsletter" style="display:none;">
    <div class="popup--content">
        <a class="close-button" popup-close="popup-newsletter" href="javascript:void(0)"><span
                    class="icon-close"></span></a>

        <div class="popup--flex">
            <div class="popup--image">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/farmers/login.jpg"
                     class="login-beauty--image__img" alt="Iscriviti alla newsletter"/>
            </div>
            <div class="popup--text">
                <div class="newsletter--intro">
                    <h3 class="newsletter--title"><?php echo $newsletter_title; ?></h3>
                    <p class="newsletter--subtitle"><?php echo $newsletter_subtitle; ?></p>
                </div>
                <div class="mailchimp-form">

                    <!-- Begin Mailchimp Signup Form -->
                    <div class="newsletter--form" id="mc_embed_signup">

                        <form action="https://agrispesa.us8.list-manage.com/subscribe/post?u=a601ffa5369b98db7030601ee&amp;id=cae80e4aed&amp;f_id=001468e0f0" onsubmit="return validateFormPopup()" method="post" id="mc-embedded-subscribe-form-popup" name="mc-embedded-subscribe-form-popup" class="validate" target="_blank" novalidate>
                    				<div id="mc_embed_signup_scroll-popup" class="signup">
                    					<div class="mc-field-group">
                    						<input type="email" value="" name="EMAIL" placeholder="Il tuo indirizzo email" class="input-text required email" id="mce-EMAIL-popup">
                                <div class="content__gdpr">
                                  <div class="checkbox-form form-agree">
                                    <input type="checkbox" id="popupGdpr" name="popupGdpr" value="Y" class="av-checkbox">
                                    <label class="green border-black" for="popupGdpr">Ho letto e accetto la <a href="<?php echo esc_url( home_url( '/' ) ); ?>privacy-policy" target="_blank" title="Privacy Policy">Privacy Policy</a></label>
                                  </div>
                                 </div>
                    					</div>
                    					<div class="mailchimp-form--buttons">
                    						<input type="submit" value="Iscriviti" name="subscribe" id="mc-embedded-subscribe-popup" class="btn btn-primary btn-small">
                    					</div>

                    					<div id="mce-responses-popup" class="clear">
                    						<div class="response" id="mce-error-response-popup" style="display:none"></div>
                    						<div class="response" id="mce-success-response-popup" style="display:none"></div>
                    					</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    					<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_42fcc9d5ed6c3e5687d0de042_0cc342225e" tabindex="-1" value=""></div>

                    				</div>
                    		</form>
                    </div>
                    <script>
                        function validateFormPopup() {
                            if (document.forms["mc-embedded-subscribe-form-popup"]["popupGdpr"].checked === true) {
                                console.log('click');
                                jQuery('#mc-embedded-subscribe-form-popup .form-agree').removeClass('not-valid');
                            } else {
                                jQuery('#mc-embedded-subscribe-form-popup .form-agree').addClass('not-valid');
                                console.log('no click');
                                return false;
                            }
                        }
                    </script>
                    <!--End mc_embed_signup-->
                </div>
            </div>
        </div>

    </div>
</div>
