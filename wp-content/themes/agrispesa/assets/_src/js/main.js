/* global WPURL:readonly */
/* global wc_checkout_params:readonly */

window.baseurl = WPURL.siteurl
window.userId = WPURL.userId


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
//landingSelectVariable();
checkoutRemoveCheckbox();
petNameAnimation();
sliderPetfood();
galleryProduct();
sliderBox();
sliderValues();
sliderHeroes();
sliderPetValues();
//changeShippingLabel();


function petNameAnimation() {
  if (window.screen.width > 640) {
    var i = 1;
    var sampleMessages = ["Argo", "Black", "Mya", "Rocky", "Peggy", "Bull", "Pluto", "Pepe", " Pongo"];
    setInterval(function () {
      var newText = sampleMessages[i++ % sampleMessages.length];
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

  //First load check
  var currentUrl = jQuery('#get_url').attr("href");
  //var currentUrl = 'https://agrispesa.it/carrello?&quantity=1&variation_id=60';
  var url = new URL(currentUrl);
  const valSize = jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').filter(":checked").val();
  const valType = jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').filter(":checked").val();

  var var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

  url.searchParams.set("variation_id", var_id); // setting your param
  var newUrl = url.href;

  jQuery('#get_url').attr("href", newUrl);

  jQuery('.change-price-box').hide();
  jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();


  jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').on('change', function () {
    const valSize = jQuery(this).filter(":checked").val();
    var valType = jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').filter(":checked").val();
    var var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

    jQuery('.change-price-box').hide();
    jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();

    url.searchParams.set("variation_id", var_id);
    var newUrl = url.href;
    jQuery('#get_url').attr("href", newUrl);

  })
  jQuery('.landing-box .variation-radios input[name="attribute_pa_tipologia"]').on('change', function () {
    const valType = jQuery(this).filter(":checked").val();
    var valSize = jQuery('.landing-box .variation-radios input[name="attribute_pa_dimensione"]').filter(":checked").val();
    var var_id = jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').attr('data-id');

    jQuery('.change-price-box').hide();
    jQuery('.change-price-box[data-size="' + valSize + '"][data-type="' + valType + '"]').show();

    url.searchParams.set("variation_id", var_id);
    var newUrl = url.href;
    jQuery('#get_url').attr("href", newUrl);
  })


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
  if (jQuery('.negozio-sidebar--list').length) {
    jQuery('.negozio-sidebar--list li.cat-item:has(ul.children) > a').addClass('i-have-kids');
    jQuery('.negozio-sidebar--list > li.cat-item:has(ul.children) > a').addClass('first-item');

    let _viewall = jQuery('.negozio-sidebar--list li.cat-item.view-all');
    _viewall.each(function () {
      let _children = jQuery(this).prev('.cat-item').find('ul.children').first();
      jQuery(this).addClass('test');
      jQuery(this).prependTo(_children);
      console.log(_viewall);
    });
  }
  if (jQuery('.negozio-sidebar--list .current-cat').length) {
    jQuery('.negozio-sidebar--list').addClass('its-category');
    jQuery('.negozio-sidebar--list .current-cat-ancestor > a, .negozio-sidebar--list .current-cat > a').addClass('opened');
    jQuery('.negozio-sidebar--list .current-cat-ancestor > ul.children, .negozio-sidebar--list .current-cat > ul.children').addClass('show-items');
    jQuery('.negozio-sidebar--list .current-cat-ancestor > ul.children, .negozio-sidebar--list .current-cat > ul.children').show();
    jQuery('.negozio-sidebar--list .current-cat-ancestor, .negozio-sidebar--list .current-cat').siblings().hide();
  }

  jQuery('.negozio-sidebar--list .i-have-kids').on('click', function (e) {
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

    jQuery.ajax({
      type: 'POST',
      url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
      data: data,
      success: function (code) {
        jQuery('.woocommerce-error, .woocommerce-message').remove();
        $form.removeClass('processing').unblock();
        if (code) {
          jQuery(".coupon-form").before(code)
          jQuery(document.body).trigger('applied_coupon_in_checkout', [data.coupon_code]);
          jQuery(document.body).trigger('update_checkout', {
            update_shipping_method: false
          });
        }
      },
      dataType: 'html'
    });

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
    console.log(varImg);

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
    slidesToShow:3,
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
          if (jQuery('.shop-buttons-flex').length) {
            jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', max);
          }
        } else {
          $qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
          if (jQuery('.shop-buttons-flex').length) {
            jQuery(this).closest('.shop-buttons-flex').find('.add_to_cart_button').attr('data-quantity', currentVal + parseFloat(step)).toFixed(step.getDecimals());
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


function getImageBrightness(image, callback) {
  let thisImgID = image.attr("id");

  let img = document.createElement("img");
  img.src = image.attr("src");

  img.style.display = "none";
  document.body.appendChild(img);

  let colorSum = 0;

  img.onload = function () {
    // create canvas
    let canvas = document.createElement("canvas");
    canvas.width = this.width;
    canvas.height = this.height;

    let ctx = canvas.getContext("2d");
    ctx.drawImage(this, 0, 0);

    let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    let data = imageData.data;
    let r, g, b, avg;

    for (let x = 0, len = data.length; x < len; x += 4) {
      r = data[x];
      g = data[x + 1];
      b = data[x + 2];

      avg = Math.floor((r + g + b) / 3);
      colorSum += avg;
    }

    let brightness = Math.floor(colorSum / (this.width * this.height));
    callback(thisImgID, brightness);
  }
}


// getImageBrightness(jQuery('#getBright'), function(thisImgID, brightness) {
//   if (brightness < 127.5) {
//     jQuery('.hero').addClass("light-hero");
//     jQuery('.hero-landing').addClass("light-hero");
//   } else {
//     jQuery('.hero').addClass("dark-hero");
//     jQuery('.hero-landing').addClass("dark-hero");
//   }
// });
