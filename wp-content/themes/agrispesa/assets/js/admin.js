jQuery(document).ready(function ($) {
  function formatState(state) {
    if (!state.id) {
      return state.text;
    }
    var producer = $(state.element).data('producer');
    var sku = $(state.element).data('sku');
    var price = $(state.element).data('price');
    var weight = $(state.element).data('weight');
    var conf = $(state.element).data('conf');

    var $state = $(
      '<div class="agr-select agr-select--flex"><div><div><span class="agr-select title">' + state.text + '</span><span class="agr-select weight">' + weight + '</span></div></div><div><span class="agr-select price">€' + price + '</span></div></div><div class="agr-select agr-select--flex"><div><span class="agr-select sku">' + sku + '</span></div><div><span class="agr-select conf">Cod. Conf: ' + conf + '</span></div></div><div class="agr-select agr-select--flex"><div><span class="agr-select producer">' + producer + '</span></div></div>'
    );
    return $state;
  }


  $(".select2").select2();
  $(".agr-select").select2({
    templateResult: formatState
  });

  $('.datatable').DataTable({
    "language": {
      "search": "Cerca:",
      "lengthMenu": "Mostra _MENU_ elementi",
      }
  });

  $('.box-table').DataTable({
    order: [[0, 'desc']],
    orderClasses: false,
    "pageLength": 16,
    "lengthMenu": [ 16, 32, 48, 64, 80 ],
    "language": {
      "search": "Cerca:",
      "lengthMenu": "Mostra _MENU_ box",
      }
  });


  $(".add-product-box").click(function (e) {
    e.preventDefault()

    let product_id = $(this).closest('tr').find('.new-product-box').val()
    let quantity = $(this).closest('tr').find('.new-quantity').val()
    let product_name = $(this).closest('tr').find('.new-product-box option:selected').data('name')
    let product_price = $(this).closest('tr').find('.new-product-box option:selected').data('price')

    let box_id = $(this).data('box-id')

    axios.post(WPURL.siteurl + '/wp-json/agrispesa/v1/weekly-box/' + box_id + '/products', {
      product_ids: [product_id],
      quantity: [quantity]
    })
      .then((response) => {
        location.href = ''
      });

  })

  $(".delete-product-box").click(function (e) {
    e.preventDefault()

    if (confirm('Vuoi togliere il prodotto dalla box?')) {
      let index = $(this).data('index')
      let box_id = $(this).data('box-id')


      axios.delete(WPURL.siteurl + '/wp-json/agrispesa/v1/weekly-box/' + box_id + '/products/' + index)
        .then((response) => {
          location.href = ''
        });
    }

  })

  $(".new-product-box").change(function () {
    $(this).closest('tr').find('.unit-measure').html($(this).find('option:selected').data('unit-measure'))
  })

  $(".change_week").on("change paste keyup", function () {
    const y = new Date().getFullYear();
    const jan1 = new Date(y, 0, 1);
    const jan1Day = jan1.getDay();
    const daysToMonday = jan1Day === 1 ? 0 : jan1Day === 0 ? 1 : 8 - jan1Day

    const firstWednesday = daysToMonday === 0 ? jan1 : new Date(+jan1 + daysToMonday * 86400e3);
    //console.log(moment(new Date(+firstWednesday + ((09 - 1) * 7 * 86400e3) + (86400e3 * 2) )).format('YYYY-MM-DD'));

    $('.change_shipping_date').val(moment(new Date(+firstWednesday + (($(this).val() - 1) * 7 * 86400e3) + (86400e3 * 2))).format('YYYY-MM-DD'));

  });


})

const {createApp} = Vue
createApp({
  data() {
    return {
      message: '',
      products: [],
      productJson: productIds
    }
  },
  computed: {
    totalWeight() {
      let weight = 0;
      this.products.forEach(function (product) {
        weight += (product.quantity * parseInt(product.weight))

        console.log(product)
      })
      return weight + ' gr'
    },
    totalPrice() {
      let price = 0;
      this.products.forEach(function (product) {
        price += product.price * product.quantity
      })
      return '€' + price
    }
  },
  methods: {
    createBox: function () {
      const $vm = this
      axios.post(WPURL.siteurl + '/wp-json/agrispesa/v1/weekly-box', {
        product_box_id: document.getElementById('box_id').value,
        week: document.getElementById('week').value,
        data_consegna: document.getElementById('data_consegna').value,
        products: $vm.products
      })
        .then((response) => {
          location.href = ''
        });
    },
    deleteProduct: function (index) {
      this.products.splice(index, 1);
    },
    addProduct: function (div) {
      if (document.getElementById('data_consegna').value == '') {
        alert('Inserisci una data di consegna')
        return false
      }
      if (document.getElementById('box_id').value == '') {
        alert('Seleziona una box')
        return false
      }

      let productId = document.getElementById(div).value

      let product = this.productJson.filter(function (product) {
        return product['id'] == productId
      })
      let productToAdd = product[0]
      productToAdd.quantity = 1
      this.products.push(productToAdd)
    },
    copyFromLastWeek: function () {
      if (document.getElementById('data_consegna').value == '') {
        alert('Inserisci una data di consegna')
        return false
      }
      if (document.getElementById('box_id').value == '') {
        alert('Seleziona una box')
        return false
      }

      const $vm = this
      axios.post(WPURL.siteurl + '/wp-json/agrispesa/v1/weekly-box/duplicate', {
        product_box_id: document.getElementById('box_id').value,
        week: document.getElementById('week').value,
        data_consegna: document.getElementById('data_consegna').value
      })
        .then((response) => {
          alert("Box Settimanale duplicata con successo");
          location.href = '';
        })
        .catch((error) => {
          if (error.response.status == 404) {
            alert(error.response.data.message)
            return false
          }
          console.log(error)
        })
      ;
    }
  }
}).mount('#box-app')
