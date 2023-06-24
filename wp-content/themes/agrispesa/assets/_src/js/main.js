/* global WPURL:readonly */
/* global WPCoupons:readonly */
/* global wc_checkout_params:readonly */

window.baseurl = WPURL.siteurl
window.userId = WPURL.userId


window.enabled_cap = [
  15100, 15121, 15122, 15062, 14030, 14041, 14041, 14022, 14010, 14020, 14018, 14100, 14030, 14011, 14046, 14022, 14040, 14020, 14055, 14046, 14051, 14021, 14042, 14033, 14031, 14052, 14020, 14053, 14053, 14010, 14014, 14100, 14032, 14050, 14054, 14030, 14040, 14044, 14033, 14033, 14013, 14040, 14034, 14043, 14040, 14022, 14037, 14010, 14010, 14020, 14030, 14050, 14025, 14020, 14010, 14054, 14023, 14023, 14026, 14020, 14013, 14020, 14010, 14040, 14020, 14055, 14026, 14010, 14010, 14012, 14044, 14030, 14045, 14031, 14035, 14045, 14057, 14051, 14045, 14040, 14018, 14020, 14037, 14050, 14057, 14050, 14020, 14046, 14047, 14013, 14058, 14036, 14024, 14022, 14034, 14040, 14040, 14014, 14048, 14025, 14048, 14048, 14010, 14030, 14100, 14026, 14026, 14023, 14050, 14049, 14050, 14054, 14020, 14020, 14030, 14057, 14020, 14020, 14026, 14100, 14037, 14016, 14040, 14030, 14030, 14010, 14010, 14018, 14020, 14030, 14050, 14042, 14030, 14054, 14015, 14031, 14059, 14010, 14050, 14100, 14010, 14010, 14015, 14053, 14020, 14026, 14020, 14030, 14050, 14020, 14058, 14020, 14020, 14020, 14100, 14016, 14039, 14023, 14010, 14049, 14030, 14017, 14100, 14010, 14100, 14100, 14059, 14010, 14030, 14100, 14040, 14020, 14018, 14019, 14019, 14040, 32043, 12066, 12050, 12051, 12040, 12042, 12050, 12050, 12060, 12060, 12100, 12052, 12042, 12062, 12040, 12050, 12030, 12050, 12050, 12060, 12030, 12040, 12040, 12062, 12069, 12040, 12024, 12055, 12045, 12060, 12040, 12060, 12050, 12064, 12030, 12060, 12050, 12056, 12030, 12030, 12065, 12046, 12040, 12040, 12066, 12051, 12068, 12052, 12050, 12060, 12040, 12060, 12042, 12060, 12040, 12055, 12030, 12060, 12062, 12060, 12037, 12040, 12069, 12050, 12038, 12050, 12050, 12048, 12040, 12050, 12050, 12055, 12060, 12040, 16031, 16100, 16121, 16122, 16123, 16124, 16125, 16126, 16127, 16128, 16129, 16131, 16132, 16133, 16134, 16136, 16138, 16139, 16141, 16142, 16143, 16144, 16145, 16146, 16147, 16148, 16149, 16151, 16152, 16153, 16154, 16157, 16159, 16161, 16162, 16164, 16165, 16166, 16167, 18129, 16156, 16157, 16166, 16149, 16184, 16036, 20864, 20861, 20812, 20821, 20081, 20048, 20041, 20041, 20040, 20080, 20042, 20042, 20060, 20020, 20043, 20862, 20020, 20044, 20010, 20090, 20080, 20060, 20021, 20021, 20020, 20010, 20030, 20030, 20060, 20080, 20011, 20068, 20060, 20040, 20044, 20010, 20045, 20080, 20068, 20060, 20046, 20020, 20082, 20031, 20010, 20021, 20098, 20060, 20030, 20030, 20091, 20040, 20047, 20032, 20080, 20090, 20040, 20010, 20040, 20060, 20060, 20020, 20090, 20133, 20080, 20040, 20030, 20050, 20010, 20050, 20023, 20040, 20040, 20048, 20841, 20040, 20080, 20061, 20062, 20080, 20010, 20020, 20062, 20010, 20010, 20062, 20060, 20060, 20021, 20031, 20081, 20022, 20087, 20087, 20040, 20016, 20011, 20020, 20063, 20063, 20063, 20070, 20023, 20063, 20090, 20031, 20811, 20020, 20030, 20092, 20092, 20080, 20020, 20815, 20040, 20093, 20060, 20056, 20863, 20011, 20032, 20010, 20040, 20050, 20094, 20012, 20090, 20095, 20020, 20020, 20033, 20070, 20090, 20090, 20040, 20010, 20083, 20024, 20060, 20034, 20064, 20019, 20056, 20062, 20088, 20010, 20065, 20084, 20020, 20020, 20025, 20030, 20050, 20051, 20096, 20090, 20068, 20060, 20035, 20085, 20017, 20090, 20050, 20013, 20020, 20010, 20010, 20060, 20030, 20017, 20017, 20036, 20060, 20077, 20066, 20010, 20097, 20050, 20068, 20095, 20010, 20082, 20100, 20120, 20121, 20122, 20123, 20124, 20125, 20126, 20127, 20128, 20129, 20131, 20132, 20133, 20134, 20135, 20136, 20137, 20138, 20139, 20141, 20142, 20143, 20144, 20145, 20146, 20147, 20148, 20149, 20151, 20152, 20153, 20154, 20155, 20156, 20157, 20158, 20159, 20161, 20162, 20193, 21100, 21128, 29146, 20127, 20090, 20090, 20020, 20080, 20051, 20060, 20080, 20080, 20052, 20900, 20090, 20081, 20086, 20053, 20120, 20014, 20020, 20054, 20834, 20026, 20096, 20090, 20090, 20082, 20041, 20090, 20059, 20060, 20032, 20010, 20080, 20037, 20034, 20030, 20090, 20015, 20017, 20080, 20067, 20050, 20016, 20068, 20060, 20090, 20096, 20097, 20010, 20013, 20040, 20060, 20060, 20010, 20090, 20089, 20090, 20055, 20100, 20027, 20027, 20017, 20070, 20060, 20020, 20087, 20090, 20010, 20090, 20040, 20050, 20885, 20088, 20089, 20097, 20070, 20097, 20068, 20078, 20047, 20097, 20090, 20052, 20010, 20098, 20093, 20010, 20080, 20080, 20083, 20028, 20070, 20082, 20035, 20060, 20014, 20010, 20070, 20018, 20096, 20096, 20054, 20090, 20030, 20038, 20831, 20099, 20097, 20099, 20098, 20098, 20049, 20090, 20019, 20030, 20020, 20097, 20050, 20090, 20050, 20053, 20017, 20040, 20060, 20090, 20050, 20060, 20090, 20090, 20056, 20067, 20060, 20050, 20060, 20029, 20040, 20039, 20020, 20010, 20069, 20030, 20039, 20814, 20057, 20025, 20050, 20050, 20059, 20040, 20050, 20080, 20080, 20083, 20019, 20060, 20060, 20070, 20020, 20060, 20050, 20020, 20051, 20051, 20024, 20031, 20044, 20058, 20852, 20015, 20059, 20871, 20090, 20010, 20070, 20080, 20068, 20080, 20080, 20080, 20090, 20050, 28100, 28010, 28010, 28010, 28010, 28011, 28041, 28019, 28010, 28043, 28061, 28100, 28010, 28010, 28010, 28040, 28071, 28021, 28010, 28072, 28010, 28062, 28062, 28060, 28064, 28060, 28060, 28100, 28060, 28060, 28060, 28053, 28010, 28010, 28010, 28065, 28011, 28010, 28060, 28012, 28060, 28041, 28010, 28040, 28073, 28010, 28047, 28066, 28070, 28010, 28013, 28074, 28046, 28100, 28024, 28060, 28060, 28075, 28045, 28016, 28064, 28040, 28100, 28014, 28060, 28040, 28040, 28046, 28041, 28040, 28010, 28015, 28010, 28060, 28070, 28067, 28100, 28047, 28040, 28100, 28060, 28016, 28016, 28040, 28010, 28100, 28028, 28010, 28010, 28076, 28050, 28077, 28028, 28060, 28010, 28078, 28068, 28072, 28021, 28017, 28060, 28060, 28021, 28100, 28064, 28070, 28010, 28010, 28011, 28060, 28019, 28070, 28070, 28100, 28069, 28010, 28040, 28021, 28010, 28079, 28100, 28060, 28100, 28060, 29010, 29020, 29100, 43036, 43040, 27021, 27010, 27040, 27010, 27043, 27100, 27020, 27022, 27023, 27013, 27024, 27025, 27026, 27052, 27020, 27027, 27010, 27010, 27035, 27100, 27054, 27040, 27036, 27050, 27030, 27020, 27100, 27017, 27040, 27050, 27055, 27010, 27052, 27010, 27028, 27050, 27010, 27050, 27020, 27020, 27020, 27020, 27029, 27010, 27058, 27010, 27030, 97014, 138, 192, 38034, 10040, 10080, 10044, 10081, 10091, 10020, 10046, 10051, 10080, 10080, 10020, 10099, 10070, 10064, 10092, 10070, 10040, 10092, 10092, 10040, 10071, 10171, 10071, 10071, 10093, 10024, 10070, 10014, 10020, 10060, 10060, 10081, 10010, 10060, 10066, 10040, 10041, 10022, 10019, 10038, 10020, 10035, 10090, 10098, 10090, 10090, 10090, 10098, 10090, 10090, 10090, 10095, 10040, 10072, 10072, 10080, 10090, 10060, 10060, 10060, 10081, 10060, 10080, 10034, 10090, 10020, 10022, 10133, 10061, 10060, 10070, 10080, 10077, 10077, 10041, 10010, 10023, 10080, 10050, 10034, 10080, 10080, 10090, 10073, 10073, 10070, 10050, 10070, 10093, 10097, 10129, 10080, 10020, 10055, 10090, 10070, 10082, 10090, 10040, 10082, 10073, 10040, 10080, 10046, 10083, 10080, 10080, 10090, 10070, 10010, 10090, 10043, 10070, 10084, 10084, 10080, 10080, 10040, 10060, 10070, 10022, 10048, 10048, 10045, 10060, 10090, 10090, 10095, 10095, 10040, 10094, 10040, 10095, 10080, 10043, 10040, 10040, 10060, 10046, 10040, 10040, 10084, 10074, 10020, 10040, 10040, 10095, 10093, 10080, 10040, 10040, 10010, 10080, 10062, 10062, 10080, 10060, 10020, 10030, 10077, 10070, 10072, 10020, 10020, 10046, 10075, 10075, 10080, 10040, 10020, 10059, 10024, 10026, 10027, 10124, 10129, 10133, 10020, 10090, 10016, 10017, 10040, 10020, 10027, 10020, 10081, 10042, 10080, 10076, 10060, 10040, 10080, 10040, 10043, 10060, 10040, 10080, 10060, 10010, 10020, 10018, 10080, 10020, 10060, 10080, 10023, 10090, 10044, 10060, 10025, 10040, 10040, 10045, 10060, 10046, 10063, 10085, 10094, 10060, 10040, 10065, 10080, 10080, 10082, 10080, 10090, 10093, 10024, 10024, 10080, 10064, 10020, 10090, 10040, 10040, 10040, 10040, 10040, 10080, 10080, 10086, 10040, 10040, 10060, 10090, 10098, 10900, 10060, 10070, 10070, 10060, 10090, 10080, 10030, 10060, 10060, 10080, 10090, 10060, 10040, 10090, 10090, 10040, 10099, 10090, 10070, 10099, 10099, 10090, 10080, 10010, 10082, 10080, 10080, 10090, 10022, 10070, 10080, 10070, 10065, 10040, 10090, 10076, 10090, 10077, 10099, 10099, 10022, 10020, 10060, 10080, 10090, 10020, 10060, 10090, 10057, 10050, 10026, 10093, 10060, 10090, 10094, 10036, 10080, 10019, 10040, 10042, 10024, 10040, 10040, 10024, 10098, 10030, 10034, 10023, 10025, 10100, 10121, 10122, 10123, 10124, 10125, 10126, 10127, 10128, 10129, 10131, 10132, 10133, 10134, 10135, 10136, 10137, 10138, 10139, 10141, 10142, 10143, 10144, 10145, 10146, 10147, 10148, 10149, 10151, 10152, 10153, 10154, 10155, 10156, 10158, 10384, 10090, 10080, 10080, 10070, 10028, 10070, 10040, 10094, 10028, 10070, 10087, 10080, 10070, 10070, 10070, 10078, 10078, 10078, 10038, 10020, 10030, 10090, 10080, 10080, 10067, 10080, 10068, 10090, 10070, 10040, 10090, 10040, 10030, 10060, 10069, 10029, 10141, 10048, 10048, 10060, 10060, 10030, 10080, 10070, 10088, 10040, 10040, 21049, 21010, 21041, 21021, 21010, 21022, 21027, 21050, 21010, 21023, 21024, 21050, 21020, 21010, 21052, 21031, 21034, 21010, 21032, 21010, 21040, 21042, 21040, 21020, 21030, 21020, 21040, 21011, 21012, 21030, 21053, 21010, 21030, 21010, 21043, 21040, 21020, 21040, 21033, 21050, 21034, 21034, 21020, 21025, 21030, 21020, 21020, 21050, 21050, 21030, 21030, 21010, 21030, 21030, 21020, 21010, 21010, 21030, 21054, 21010, 21030, 21013, 21020, 21039, 21026, 21045, 21036, 21040, 21010, 21030, 21010, 21050, 21055, 21040, 21030, 21020, 21056, 21027, 21027, 21040, 21037, 21014, 21038, 21010, 21020, 21015, 21040, 21016, 21020, 21010, 21046, 21030, 21050, 21030, 21030, 21100, 21040, 21030, 21010, 21040, 21020, 21010, 21010, 21040, 21040, 21057, 21026, 21040, 21030, 21010, 21050, 21010, 21030, 21020, 21040, 21050, 21017, 21010, 21038, 21040, 21047, 21018, 21048, 21058, 21019, 21040, 21020, 21020, 21049, 21028, 21034, 21010, 21040, 21020, 21020, 21100, 21040, 21010, 21040, 21040, 21017, 21029, 21031, 21059, 21010, 28887, 28827, 28802, 28841, 28877, 28811, 28899, 28812, 28887, 28871, 28831, 28813, 28832, 28851, 28851, 28804, 28838, 28842, 28833, 28873, 28814, 28822, 28815, 28838, 28823, 28881, 28873, 28825, 28881, 28891, 28887, 28811, 28801, 28811, 28865, 28887, 28827, 28803, 28851, 28845, 28853, 28827, 28831, 28854, 28924, 28842, 28897, 28895, 28881, 28887, 28823, 28836, 28824, 28883, 28883, 28828, 28816, 28868, 28838, 28838, 28893, 28897, 28876, 28894, 28838, 28854, 28855, 28895, 28802, 28817, 28864, 28843, 28891, 28824, 28887, 28877, 28884, 28883, 28885, 28886, 28825, 28864, 28865, 28818, 28866, 28803, 28898, 28896, 28881, 28856, 28864, 28897, 28804, 28879, 28866, 28881, 28843, 28838, 28823, 28858, 28826, 28868, 28859, 28897, 28879, 28868, 28900, 28921, 28922, 28836, 28841, 28819, 28844, 28856, 28852, 28805, 28854, 13030, 13040, 13031, 13032, 13020, 13040, 13011, 13041, 13040, 13012, 13011, 13037, 13100, 13020, 13040, 13020, 13100, 13026, 13010, 13030, 13040, 13030, 13024, 13025, 13043, 13010, 13030, 13033, 13020, 13044, 13040, 13034, 13017, 13020, 13022, 13040, 13030, 13040, 13030, 13030, 13010, 13011, 13046, 13100, 13035, 13034, 13046, 13045, 13020, 13040, 13019, 13010, 13047, 13030, 13040, 13030, 13010, 13020, 13020, 13010, 13012, 13017, 13030, 13020, 13026, 13026, 13020, 13020, 13030, 13060, 13036, 13020, 13040, 13020, 13040, 13040, 13040, 13044, 13047, 13030, 13060, 13044, 13060, 13048, 13027, 13037, 13010, 13038, 13039, 13049, 13018, 13019, 13019, 13019, 13100, 13030, 13010, 13037, 13020, 13018, 37011, 37023, 1017, 22029
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
    let form = window.localStorage.getItem('form')
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

      window.localStorage.setItem('form', JSON.stringify(dataForm))

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
