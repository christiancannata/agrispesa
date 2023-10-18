/* global WPURL:readonly */
/* global WPCoupons:readonly */
/* global wc_checkout_params:readonly */

window.baseurl = WPURL.siteurl
window.userId = WPURL.userId


window.enabled_cap = [
  24123,
  10010,
  10014,
  10016,
  10017,
  10018,
  10019,
  10020,
  10022,
  10023,
  10024,
  10025,
  24128,
  10026,
  10027,
  10028,
  10029,
  10030,
  10034,
  10035,
  10036,
  10038,
  10040,
  10041,
  10042,
  10043,
  10044,
  10045,
  10046,
  10048,
  10050,
  10051,
  10055,
  10057,
  10059,
  10060,
  10061,
  10062,
  10063,
  10064,
  10065,
  10066,
  10067,
  10068,
  10069,
  10070,
  10071,
  10072,
  10073,
  10074,
  10075,
  10076,
  10077,
  10078,
  10080,
  10081,
  10082,
  10083,
  10084,
  10085,
  10086,
  10087,
  10088,
  10090,
  10091,
  10092,
  10093,
  10094,
  10095,
  10097,
  10098,
  10099,
  10100,
  10121,
  10122,
  10123,
  10124,
  10125,
  10126,
  10127,
  10128,
  10129,
  10131,
  10132,
  10133,
  10134,
  10135,
  10136,
  10137,
  10138,
  10139,
  10141,
  10142,
  10143,
  10144,
  10145,
  10146,
  10147,
  10148,
  10149,
  10151,
  10152,
  10153,
  10154,
  10155,
  10156,
  10158,
  10171,
  10384,
  10900,
  12024,
  12030,
  12037,
  12038,
  12040,
  12042,
  12045,
  12046,
  12048,
  12050,
  12051,
  12052,
  12055,
  12056,
  12060,
  12062,
  12064,
  12065,
  12066,
  12068,
  12069,
  12100,
  13010,
  13011,
  13012,
  13017,
  13018,
  13019,
  13020,
  13022,
  13024,
  13025,
  13026,
  13027,
  13030,
  13031,
  13032,
  13033,
  13034,
  13035,
  13036,
  13037,
  13038,
  13039,
  13040,
  13041,
  13043,
  13044,
  13045,
  13046,
  13047,
  13048,
  13049,
  13060,
  13100,
  14010,
  14011,
  14012,
  14013,
  14014,
  14015,
  14016,
  14017,
  14018,
  14019,
  14020,
  14021,
  14022,
  14023,
  14024,
  14025,
  14026,
  14030,
  14031,
  14032,
  14033,
  14034,
  14035,
  14036,
  14037,
  14039,
  14040,
  14041,
  14042,
  14043,
  14044,
  14045,
  14046,
  14047,
  14048,
  14049,
  14050,
  14051,
  14052,
  14053,
  14054,
  14055,
  14057,
  14058,
  14059,
  14100,
  15062,
  15100,
  15121,
  15122,
  192,
  20010,
  20011,
  20012,
  20013,
  20014,
  20015,
  20016,
  20017,
  20018,
  20019,
  20020,
  20021,
  20022,
  20023,
  20024,
  20025,
  20026,
  20027,
  20028,
  20029,
  20030,
  20031,
  20032,
  20033,
  20034,
  20035,
  20036,
  20037,
  20038,
  20039,
  20040,
  20041,
  20042,
  20043,
  20044,
  20045,
  20046,
  20047,
  20048,
  20049,
  20050,
  20051,
  20052,
  20053,
  20054,
  20055,
  20056,
  20057,
  20058,
  20059,
  20060,
  20061,
  20062,
  20063,
  20064,
  20065,
  20066,
  20067,
  20068,
  20069,
  20070,
  20077,
  20078,
  20080,
  20081,
  20082,
  20083,
  20084,
  20085,
  20086,
  20087,
  20088,
  20089,
  20090,
  20091,
  20092,
  20093,
  20094,
  20095,
  20096,
  20097,
  20098,
  20099,
  20100,
  20120,
  20121,
  20122,
  20123,
  20124,
  20125,
  20126,
  20127,
  20128,
  20129,
  20131,
  20132,
  20133,
  20134,
  20135,
  20136,
  20137,
  20138,
  20139,
  20141,
  20142,
  20143,
  20144,
  20145,
  20146,
  20147,
  20148,
  20149,
  20151,
  20152,
  20153,
  20154,
  20155,
  20156,
  20157,
  20158,
  20159,
  20161,
  20162,
  20193,
  20811,
  20812,
  20814,
  20815,
  20821,
  20831,
  20834,
  20841,
  20852,
  20861,
  20862,
  20863,
  20864,
  20871,
  20885,
  20900,
  21010,
  21011,
  21012,
  21013,
  21014,
  21015,
  21016,
  21017,
  21018,
  21019,
  21020,
  21021,
  21022,
  21023,
  21024,
  21025,
  21026,
  21027,
  21028,
  21029,
  21030,
  21031,
  21032,
  21033,
  21034,
  21036,
  21037,
  21038,
  21039,
  21040,
  21041,
  21042,
  21043,
  21045,
  21046,
  21047,
  21048,
  21049,
  21050,
  21052,
  21053,
  21054,
  21055,
  21056,
  21057,
  21058,
  21059,
  21100,
  21128,
  22029,
  24029,
  27010,
  27013,
  27017,
  27020,
  27021,
  27022,
  27023,
  27024,
  27025,
  27026,
  27027,
  27028,
  27029,
  27030,
  27035,
  27036,
  27040,
  27043,
  27050,
  27052,
  27054,
  27055,
  27058,
  27100,
  28010,
  28011,
  28012,
  28013,
  28014,
  28015,
  28016,
  28017,
  28019,
  28021,
  28024,
  28028,
  28040,
  28041,
  28043,
  28045,
  28046,
  28047,
  28050,
  28053,
  28060,
  28061,
  28062,
  28064,
  28065,
  28066,
  28067,
  28068,
  28069,
  28070,
  28071,
  28072,
  28073,
  28074,
  28075,
  28076,
  28077,
  28078,
  28079,
  28100,
  28801,
  28802,
  28803,
  28804,
  28805,
  28811,
  28812,
  28813,
  28814,
  28815,
  28816,
  28817,
  28818,
  28819,
  28822,
  28823,
  28824,
  28825,
  28826,
  28827,
  28828,
  28831,
  28832,
  28833,
  28836,
  28838,
  28841,
  28842,
  28843,
  28844,
  28845,
  28851,
  28852,
  28853,
  28854,
  28855,
  28856,
  28858,
  28859,
  28864,
  28865,
  28866,
  28868,
  28871,
  28873,
  28876,
  28877,
  28879,
  28881,
  28883,
  28884,
  28885,
  28886,
  28887,
  28891,
  28893,
  28894,
  28895,
  28896,
  28897,
  28898,
  28899,
  28900,
  28921,
  28922,
  28924,
  29010,
  29020,
  29100,
  29146,
  32043,
  37011,
  37023,
  38034,
  43036,
  43040,
  97014
]


jQuery(document).ready(function () {
  jQuery(".input-text.qty").attr('readonly', true)

  jQuery("#shipping_postcode").attr('maxlength', 5)
  jQuery("#shipping_postcode").attr('minlength', 5)

  jQuery("#shipping_postcode").change(function () {
    let cap = parseInt(jQuery(this).val())
    if (!window.enabled_cap.includes(cap)) {
      jQuery(this).val('')
      alert("CAP non attivo.")
    }
  })

})

function setCookie(cName, cValue, expDays) {
  let date = new Date();
  date.setTime(date.getTime() + (expDays * 24 * 60 * 60 * 1000));
  const expires = "expires=" + date.toUTCString();
  document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
}

function getCookie(cName) {
  const name = cName + "=";
  const cDecoded = decodeURIComponent(document.cookie); //to be careful
  const cArr = cDecoded.split('; ');
  let res;
  cArr.forEach(val => {
    if (val.indexOf(name) === 0) res = val.substring(name.length);
  })
  return res;
}

let hasCookie = getCookie('home_popup')
if (!hasCookie) {
  jQuery('#home_popup').fadeIn(300)
}

stickyHeader();
openMenu();
clearSearch();
openSearch();
openSubMenu();
quantityInput();
variationToRadio();
faqs();
reviewsSlider();
footerMenu();
hideBreadcrum();
productsCarousel();
productsCarouselHome();
loginForms();
showCoupon();
closeNotices();
relatedSlider();
formGiftCard();
infoAgr();
closePopup();
pressSlider();
hideGlossarioAlpha();
magazineSlider();
minimumAmount();
giftCardCheckout();
scrollTo();
showNameNewsletter();
emptyCartSlider();
accountProductsSlider();
listCategories();
moveCustomFieldsCheckout();
checkoutRemoveCheckbox();
petNameAnimation();
sliderPetfood();
galleryProduct();
sliderBox();
sliderValues();
sliderHeroes();
sliderPetValues();
removeP();
sliderBoxLanding();
//changeShippingLabel();
landingSelectVariable();


function removeP() {

  if (jQuery('body.search-results').length) {
    jQuery('.woocommerce ul.products li.product.remove-last-p > p').each(function () {
      jQuery(this).remove();
    });
  }
}

function petNameAnimation() {
  if (window.screen.width > 640) {
    let i = 1;
    let sampleMessages = ["Argo", "Black", "Mya", "Rocky", "Peggy", "Bull", "Pluto", "Pepe", " Pongo"];
    setInterval(function () {
      let newText = sampleMessages[i++ % sampleMessages.length];
      jQuery("#petname").fadeOut(600, function () {
        jQuery(this).text(newText).fadeIn(600);
      });
    }, 1 * 4000);
  }
}

function checkoutRemoveCheckbox() {
  if (jQuery('.company-shipping-label-get').length) {
    jQuery('#ship-to-different-address').hide();
    jQuery('.shipping_address').addClass('no-mg-top');
  }
}

function landingSelectVariable() {
  if (jQuery('body.page-template-landing-company').length) {
    //First load check
    //let currentUrl = jQuery('#get_url').attr("href");
    let currentUrl = 'https://www.agrispesa.it/la-tua-scatola/?add-to-cart=50&quantity=1&variation_id=18995';
    //let currentUrl = 'http://localhost:3000/agrispesa/la-tua-scatola/?add-to-cart=50&quantity=1&variation_id=18995';
    let url = new URL(currentUrl);
    const valSize = jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').filter(":checked").val();
    const valType = jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').filter(":checked").val();

    let var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

    url.searchParams.set("variation_id", var_id); // setting your param
    let newUrl = url.href;

    jQuery('#get_url').attr("href", newUrl);

    jQuery('.change-price-box').hide();
    jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();


    jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').on('change', function () {
      const valSize = jQuery(this).filter(":checked").val();
      let valType = jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').filter(":checked").val();
      let var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

      jQuery('.change-price-box').hide();
      jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();

      url.searchParams.set("variation_id", var_id);
      let newUrl = url.href;
      jQuery('#get_url').attr("href", newUrl);

    })
    jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').on('change', function () {
      const valType = jQuery(this).filter(":checked").val();
      let valSize = jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').filter(":checked").val();
      let var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

      jQuery('.change-price-box').hide();
      jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();

      url.searchParams.set("variation_id", var_id);
      let newUrl = url.href;
      jQuery('#get_url').attr("href", newUrl);
    })
  }

}

function moveCustomFieldsCheckout() {
  if (jQuery('#shipping-custom-fields').length) {
    jQuery('#shipping-custom-fields').prependTo('.woocommerce-additional-fields');
  }
  if (jQuery('#dog-custom-fields').length) {
    jQuery('#dog-custom-fields').prependTo('.woocommerce-additional-fields');
  }
  if (jQuery('#wcpay-payment-request-wrapper').length) {
    jQuery('#wcpay-payment-request-wrapper').appendTo('.woocommerce-checkout-payment');
  }
}


function listCategories() {
  if (jQuery('.cat-item-speciali.current-cat-parent').length) {
    jQuery('.negozio-sidebar--list.navigate').addClass('show-all-subs');

  }
  if (jQuery('.negozio-sidebar--list.navigate').length) {
    jQuery('.negozio-sidebar--list.navigate li.cat-item:has(ul.children) > a').addClass('i-have-kids');
    jQuery('.negozio-sidebar--list.navigate > li.cat-item:has(ul.children) > a').addClass('first-item');

    let _viewall = jQuery('.negozio-sidebar--list.navigate li.cat-item.view-all');
    _viewall.each(function () {
      let _children = jQuery(this).prev('.cat-item').find('ul.children').first();
      //jQuery(this).addClass('test');
      jQuery(this).prependTo(_children);
    });
  }
  if (jQuery('.negozio-sidebar--list.navigate .current-cat').length) {
    jQuery('.negozio-sidebar--list.navigate').addClass('its-category');
    jQuery('.negozio-sidebar--list.navigate .current-cat-ancestor > a, .negozio-sidebar--list .current-cat > a').addClass('opened');
    jQuery('.negozio-sidebar--list.navigate .current-cat-ancestor > ul.children, .negozio-sidebar--list .current-cat > ul.children').addClass('show-items');
    jQuery('.negozio-sidebar--list.navigate .current-cat-ancestor > ul.children, .negozio-sidebar--list .current-cat > ul.children').show();
    jQuery('.negozio-sidebar--list.navigate .current-cat-ancestor, .negozio-sidebar--list .current-cat').siblings().hide();
  }

  jQuery('.negozio-sidebar--list.navigate .i-have-kids').on('click', function (e) {
    e.preventDefault();
    let _this = jQuery(this);

    if (_this.hasClass('first-item')) {
      if (_this.hasClass('opened')) {
        _this.removeClass('opened');
        _this.closest('.cat-item').find('ul.children.show-items').hide();
        _this.next('ul.children').removeClass('show-items');
        _this.closest('.cat-item').find('.i-have-kids').removeClass('opened');

        _this.closest('.cat-item').siblings().slideDown();
      } else {
        _this.addClass('opened');
        _this.next('ul.children').slideDown();
        _this.next('ul.children').addClass('show-items');
        _this.next('ul.children').find('.cat-item').show();
        _this.closest('.cat-item').siblings().slideUp();
      }
    } else if (_this.hasClass('opened')) {
      _this.removeClass('opened');
      _this.closest('.cat-item').siblings().slideDown();
      _this.next('ul.children').slideUp();
      _this.next('ul.children').removeClass('show-items');
      _this.closest('.cat-item').find('.i-have-kids').removeClass('opened');


    } else {
      _this.addClass('opened');
      _this.next('ul.children').find('.cat-item').show();
      _this.next('ul.children').slideDown();
      _this.next('ul.children').addClass('show-items');
      _this.closest('.cat-item').siblings().slideUp();
    }
  });

  // jQuery('.negozio-sidebar--list .opened').on('click', function(e){
  //   e.preventDefault();
  //   console.log('cchiudi');
  //   jQuery(this).removeClass('opened');
  //   jQuery(this).addClass('closed');
  //   //jQuery(this).next('ul.children').slideToggle();
  //
  //   jQuery(this).closest('.cat-item').siblings().slideDown();
  // });


}

function showNameNewsletter() {

  jQuery('.mailchimp-form input[type=email]').on("focus", function () {
    jQuery('.mailchimp-form .show-name').fadeIn();
  });
}

function scrollTo() {
  jQuery('.scroll-to').on('click', function (event) {
    let target = jQuery(this.getAttribute('href'));
    let scrollto = target.offset().top

    if (target.length) {
      event.preventDefault();
      jQuery('html, body').stop().animate({
        scrollTop: scrollto
      }, 900);
    }
  });
}

jQuery(document).ready(function () {
  if (jQuery('form.woocommerce-checkout').length > 0) {
    let form = window.localStorage.getItem('form_' + window.userId)
    if (form) {
      form = JSON.parse(form)
      for (let field in form) {
        jQuery('input[name="' + field + '"]').val(form[field])
      }

    }
  }
})

function giftCardCheckout() {
  jQuery('button[name="apply_coupon"]').click(function (e) {
    e.preventDefault();
    let $form = jQuery(this).closest('form');

    if ($form.is('.processing')) {
      return false;
    }

    $form.addClass('processing').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });

    let data = {
      security: wc_checkout_params.apply_coupon_nonce,
      coupon_code: $form.find('input[name="coupon_code"]').val()
    };

    let button = jQuery(this)


    if (data.coupon_code.toLowerCase() != 'welovedenso' && !WPCoupons.coupons.includes(data.coupon_code.toLowerCase())) {
      jQuery.ajax({
        type: 'POST',
        url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
        data: data,
        success: function (code) {
          jQuery('.woocommerce-error, .woocommerce-message').remove();
          $form.removeClass('processing').unblock();
          if (code) {
            jQuery(".coupon-form").before(code)
            button.closest('.woocommerce-coupons-section').find('.woocommerce-error').show()

            jQuery(document.body).trigger('applied_coupon_in_checkout', [data.coupon_code]);
            jQuery(document.body).trigger('update_checkout', {
              update_shipping_method: false
            });
          }
        },
        dataType: 'html'
      });
    } else {

      let dataForm = {}

      jQuery("form.woocommerce-checkout input").each(function () {
        dataForm[jQuery(this).attr('name')] = jQuery(this).val()
      })

      window.localStorage.setItem('form_' + window.userId, JSON.stringify(dataForm))

      jQuery.ajax({
        type: 'GET',
        url: '/wp-json/agrispesa/v1/check-cart-coupon?user_id=' + window.userId + '&email=' + jQuery("#billing_email").val() + '&coupon_code=' + data.coupon_code,
        error: function (e) {
          $form.removeClass('processing').unblock();
          alert(e.responseJSON.error)
          jQuery('#coupon_code').val('')
        },
        success: function (res) {

          location.href = ''
          /*data.coupon_code = 'WELOVEDENSO'
          jQuery(document.body).trigger('applied_coupon_in_checkout', []);
          jQuery(document.body).trigger('update_checkout', {
            update_shipping_method: false
          });


          $form.removeClass('processing');*/
          /* res.coupon_code.forEach(function(coupon_code){

             data.coupon_code = coupon_code
             jQuery.ajax({
               type: 'POST',
               url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
               data: data,
               success: function (code) {
                 jQuery('.woocommerce-error, .woocommerce-message').remove();
                 $form.removeClass('processing').unblock();
                 if (code) {
                   jQuery(".coupon-form").before(code)
                   button.closest('.woocommerce-coupons-section').find('.woocommerce-error').show()

                 }
               },
               dataType: 'html'
             });
           })*/

        }
      });
    }


  })

}

function minimumAmount() {
  if (jQuery('.minimum-amount-advice').length) {
    //jQuery('.wc-proceed-to-checkout').hide();
    jQuery('.minimum-amount-advice').prependTo('.checkout--preview--bottom');
    jQuery('.minimum-amount-advice').show();
  }
}

function hideGlossarioAlpha() {
  let glossarioElements = jQuery('.glossario--anchor');
  if (glossarioElements.length) {
    glossarioElements.each(function () {
      let target = jQuery(this).attr('data-alpha');

      jQuery('.glossario--link[data-alpha="' + target + '"]').removeClass('disabled');
    });
  }

  jQuery('.sliding-link').on('click', function (event) {
    let target = jQuery(this.getAttribute('href'));
    let scrollto = target.offset().top - 35

    if (target.length) {
      event.preventDefault();
      jQuery('html, body').stop().animate({
        scrollTop: scrollto
      }, 800);
    }
  });
}

// function changeShippingLabel() {
//   if (jQuery('.cart_totals').length) {
//     let amount = jQuery('.cart_totals').find('.shipping .amount').html();
//     jQuery('.woocommerce-shipping-totals td').html(amount);
//   }
// }


function pressSlider() {

  let _carousel = jQuery(".press--slider");

  _carousel.slick({
    dots: true,
    arrows: false,
    infinite: true,
    speed: 300,
    slidesToShow: 6,
    slidesToScroll: 6,
    centerMode: false,
    autoplay: false,
    responsive: [{
      breakpoint: 1240,
      settings: {
        slidesToShow: 5,
        slidesToScroll: 5,
        dots: true,
        arrows: false
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
        dots: true,
        arrows: false
      }
    },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true,
          arrows: false
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true,
          arrows: false
        }
      }
    ]
  });

}

function closePopup() {
  // Close Popup
  jQuery('[popup-close]').on('click', function () {
    let popup_name = jQuery(this).attr('popup-close');
    jQuery('[popup-name="' + popup_name + '"]').fadeOut(300);
    setCookie('home_popup', 1, 14)
  });


  // Close Popup When Click Outside
  jQuery('.popup').on('click', function () {
    let popup_name = jQuery(this).find('[popup-close]').attr('popup-close');
    jQuery('[popup-name="' + popup_name + '"]').fadeOut(300);
    setCookie('home_popup', 1, 14)
  }).children().click(function () {
    return false;
  });
}

function infoAgr() {
  jQuery('.info_agr--button').on('click', function (e) {
    e.preventDefault();
    jQuery(this).toggleClass('active');
    jQuery('.info_agr').toggleClass('hide');
  });
}

function formGiftCard() {

  if (jQuery('.gift-card-page').length) {
    jQuery('.woocommerce-breadcrumb').hide();
  }

  jQuery('.gift-card-content-editor input,.gift-card-content-editor textarea').on('focus', function () {
    jQuery(this).parent().find('label').addClass('move');
  })

  jQuery('label[for = ywgc-delivery-date]').text('Data di consegna');
  jQuery('label[for = ywgc-recipient-name]').text('Il suo nome');
  jQuery('label[for = ywgc-recipient-email]').text('Il suo indirizzo email');
  jQuery('label[for = ywgc-sender-name]').text('Il tuo nome');
  jQuery('label[for = ywgc-edit-message]').text('Scrivi un messaggio');


  if (jQuery("#ywgc-edit-message").length) {
    jQuery("#ywgc-edit-message").attr('maxlength', '350')
  }

}

function closeNotices() {
  jQuery('.woocommerce-notices-wrapper .close-notice').on('click', function (e) {
    e.preventDefault();
    jQuery(this).closest('.woocommerce-notices-wrapper').remove();
  });
}

function showCoupon() {
  jQuery('.show-coupon').on('click', function (e) {
    e.preventDefault();
    jQuery('.my-coupon').slideToggle();
  });
}

function loginForms() {
  let showLogin = jQuery('.show-login-form');
  let showRegister = jQuery('.show-register-form');
  let loginForm = jQuery('.check-login-form');
  let registerForm = jQuery('.check-register-form');

  showLogin.on('click', function (e) {
    e.preventDefault();
    loginForm.slideToggle();
    registerForm.slideToggle();
  });
  showRegister.on('click', function (e) {
    e.preventDefault();
    loginForm.slideToggle();
    registerForm.slideToggle();
  });

}

function hideBreadcrum() {
  let boxPage = jQuery('.the-box-page');
  if (boxPage.length) {
    jQuery('.woocommerce-breadcrumb').hide();
    jQuery('.summary .price').hide();
  }
}


function footerMenu() {
  if (window.screen.width < 641) {
    jQuery('.footer--menu--title').on('click', function () {
      jQuery(this).next('.footer--menu--list').slideToggle();
      jQuery(this).find('.footer--menu--title__icon').toggleClass('rotate');
      jQuery(this).closest('.footer-menu').siblings().find('.footer--menu--list').slideUp();
      jQuery(this).closest('.footer-menu').siblings().find('.footer--menu--title__icon').removeClass('rotate');
    });
  }
}

function reviewsSlider() {

  let _carousel = jQuery(".reviews--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 900,
      settings: {
        arrows: false,
        dots: true
      }
    }]
  });
}

function galleryProduct() {

  let _carousel = jQuery(".pd-gallery-slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 900,
      settings: {
        arrows: false,
        dots: true,
        slidesToShow: 3
      }
    }]
  });

  jQuery('.woocommerce-product-gallery__image > a').on('click', function (e) {
    e.preventDefault();
  });

  jQuery('.pd-gallery-slider--link').on('click', function (e) {
    e.preventDefault();
    let varImg = jQuery(this).attr("href");

    jQuery('.woocommerce-product-gallery__image').find('.wp-post-image').attr("src", varImg);
    jQuery('.woocommerce-product-gallery__image').find('.wp-post-image').attr("srcset", varImg);
  });

}

function sliderPetfood() {

  let _carousel = jQuery(".products-petfood");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: true,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1240,
      settings: {
        slidesToShow: 2,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    },]

  });
}

function sliderBox() {

  let _carousel = jQuery(".box-types--flex");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1340,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }, {
      breakpoint: 990,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }]

  });
}

function sliderBoxLanding() {

  let _carousel = jQuery(".wb-section-box--flex");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1340,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }, {
      breakpoint: 990,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }]

  });
}

function sliderValues() {

  let _carousel = jQuery(".agri-values--flex");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1340,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }, {
      breakpoint: 990,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }]

  });
}

function sliderHeroes() {

  let _carousel = jQuery(".pet-heroes--flex");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1340,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }, {
      breakpoint: 990,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }]

  });
}

function sliderPetValues() {

  let _carousel = jQuery(".pet-values--flex");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: false,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1340,
      settings: {
        slidesToShow: 3,
        arrows: false,
        dots: true,
      }
    }, {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }, {
      breakpoint: 990,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true,
        centerMode: false
      }
    }]

  });
}

function productsCarouselHome() {

  let _carousel = jQuery(".products-carousel-home--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function productsCarousel() {

  let _carousel = jQuery(".products-carousel");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 5,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 4,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function emptyCartSlider() {

  if (jQuery('body.woocommerce-cart .emptycart').length) {
    jQuery('.page-header').hide();
  }

  let _carousel = jQuery(".emptycart--loop");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 2,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 2,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 2,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function accountProductsSlider() {

  let _carousel = jQuery(".account-get-products--loop");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1400,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 1100,
      settings: {
        slidesToShow: 3,
      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function magazineSlider() {

  let _carousel = jQuery(".magazine--slider");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 1240,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 860,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}

function relatedSlider() {

  let _carousel = jQuery(".related--list");

  _carousel.slick({
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    nextArrow: '<span class="slick-next-agr icon-arrow-right"></span>',
    prevArrow: '<span class="slick-prev-agr icon-arrow-left"></span>',
    responsive: [{
      breakpoint: 860,
      settings: {
        slidesToShow: 2,
        arrows: false,
        dots: true
      }
    }, {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        arrows: false,
        dots: true
      }
    }]
  });
}


function faqs() {

  let faqTitle = jQuery('.faq__title');

  faqTitle.on('click', function (e) {
    e.preventDefault();
    let description = jQuery(this).next('.faq__description');
    let others = jQuery(this).closest('.faq__item').siblings();

    jQuery(this).find('.faq__icon').toggleClass('show-faq');
    others.find('.faq__icon').removeClass('show-faq');
    others.find('.faq__description').slideUp();
    description.slideToggle();
  });
  if (window.screen.width > 640) {
    if (jQuery('body.post-type-archive-faq').length) {
      if (jQuery('.faq-1').length) {
        jQuery('.faq-1 .faq__title').trigger("click");
      } else {
        jQuery('.faq-11 .faq__title').trigger("click");
      }
    }
  }
}

function variationToRadio() {
  jQuery(document).on('change', '.variation-radios input', function () {
    jQuery('.variation-radios input:checked').each(function (index, element) {
      let $el = jQuery(element);
      let thisName = $el.attr('name');
      let thisVal = $el.attr('value');
      jQuery('select[name="' + thisName + '"]').val(thisVal).trigger('change');
    });
  });
  jQuery(document).on('woocommerce_update_variation_values', function () {
    jQuery('.variation-radios input').each(function (index, element) {
      let $el = jQuery(element);
      let thisName = $el.attr('name');
      let thisVal = $el.attr('value');
      $el.removeAttr('disabled');
      if (jQuery('select[name="' + thisName + '"] option[value="' + thisVal + '"]').is(':disabled')) {
        $el.prop('disabled', true);
      }
    });
  });
}

function quantityInput() {

  jQuery(function () {
    if (!String.prototype.getDecimals) {
      String.prototype.getDecimals = function () {
        let num = jQuery(this),
          match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        if (!match) {
          return 0;
        }
        return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
      }
    }
    // Quantity "plus" and "minus" buttons
    jQuery(document.body).on('click', '.product-quantity--plus, .product-quantity--minus', function () {
      let $qty = jQuery(this).closest('.product-quantity--change').find('.quantity .qty'),
        currentVal = parseFloat($qty.val()),
        max = parseFloat($qty.attr('max')),
        min = parseFloat($qty.attr('min')),
        step = $qty.attr('step');

      // Format values
      if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
      if (max === '' || max === 'NaN') max = '';
      if (min === '' || min === 'NaN') min = 1;
      if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

      // Change the value
      if (jQuery(this).is('.product-quantity--plus')) {

        if (max && (currentVal >= max)) {
          $qty.val(max);
          if (jQuery('.shop-buttons-flex').length && jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button')) {
            jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', max);
          }
        } else {
          $qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
          if (jQuery('.shop-buttons-flex').length && jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button')) {
            try {
              jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', currentVal + parseFloat(step)).toFixed(step.getDecimals());
            } catch (e) {
              console.log(e)
            }
          }
        }
      } else {
        if (min && (currentVal <= min)) {
          $qty.val(min);
          if (jQuery('.shop-buttons-flex').length) {
            jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', min);
          }
        } else if (currentVal > 0) {
          $qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
          if (jQuery('.shop-buttons-flex').length) {
            jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', currentVal - parseFloat(step)).toFixed(step.getDecimals());
          }
        }
      }


      setTimeout(function () {
        jQuery("[name='update_cart']").removeAttr('disabled');
        jQuery("[name='update_cart']").trigger("click");
      }, 500);


    });
  });

}


function openSubMenu() {
  let link = jQuery('.get-user-menu');
  let menu = jQuery('.top-user__menu');

  if (window.screen.width > 640) {
    link.on('click', function (e) {
      e.preventDefault();
      jQuery(this).toggleClass('active');
      menu.toggleClass('active');
      jQuery(".widget_shopping_cart_content").removeClass('active');
    });
  }
}


function openSearch() {
  jQuery('.openSearch').on('click', function (e) {
    e.preventDefault();
    jQuery('.header--search').addClass('showme');
    jQuery(this).closest('.menu--search').addClass('hideme');
  });
}

function clearSearch() {
  jQuery('.delete-search').on('click', function () {
    jQuery('.search-input-field').val('');
  });
}

function stickyHeader() {
  let header = jQuery('.header');
  let headerH = header.outerHeight();
  let lastScrollTop = 0;

  jQuery(window).on('scroll', function () {
    let st = jQuery(this).scrollTop();

    if (jQuery(document).scrollTop() >= headerH) {
      if (st > lastScrollTop) {
        //scroll down
        jQuery('.header').addClass("sticky-hide");
        jQuery('.header').removeClass("sticky");
      } else {
        //scroll up
        jQuery('.header').addClass("sticky");
        jQuery('.header').removeClass("sticky-hide");
      }
      lastScrollTop = st;
    } else {
      jQuery('.header').removeClass("sticky-hide");
      jQuery('.header').removeClass("sticky");

    }

  });
}

function openMenu() {
  jQuery('.get-menu, .close-menu').on('click', function (e) {
    e.preventDefault();

    let menu = jQuery('.agr-menu');
    let body = jQuery('body');

    menu.toggleClass('show-menu');
    body.toggleClass('fixed');
  });
}


// function getImageBrightness(image, callback) {
//   let thisImgID = image.attr("id");
//
//   let img = document.createElement("img");
//   img.src = image.attr("src");
//
//   img.style.display = "none";
//   document.body.appendChild(img);
//
//   let colorSum = 0;
//
//   img.onload = function () {
//     // create canvas
//     let canvas = document.createElement("canvas");
//     canvas.width = this.width;
//     canvas.height = this.height;
//
//     let ctx = canvas.getContext("2d");
//     ctx.drawImage(this, 0, 0);
//
//     let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
//     let data = imageData.data;
//     let r, g, b, avg;
//
//     for (let x = 0, len = data.length; x < len; x += 4) {
//       r = data[x];
//       g = data[x + 1];
//       b = data[x + 2];
//
//       avg = Math.floor((r + g + b) / 3);
//       colorSum += avg;
//     }
//
//     let brightness = Math.floor(colorSum / (this.width * this.height));
//     callback(thisImgID, brightness);
//   }
// }


// getImageBrightness(jQuery('#getBright'), function(thisImgID, brightness) {
//   if (brightness < 127.5) {
//     jQuery('.hero').addClass("light-hero");
//     jQuery('.hero-landing').addClass("light-hero");
//   } else {
//     jQuery('.hero').addClass("dark-hero");
//     jQuery('.hero-landing').addClass("dark-hero");
//   }
// });
