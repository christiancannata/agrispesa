jQuery(document).ready(function ($) {


  $(".select2").select2()
  $('.datatable').DataTable();
  $(".generate-csv").click(function (e) {
    e.preventDefault()

  })

  $(".add-product-box").click(function (e) {
    e.preventDefault()

    let product_id = $(this).closest('tr').find('.new-product-box').val()
    let quantity = $(this).closest('tr').find('.new-quantity').val()
    let product_name = $(this).closest('tr').find('.new-product-box option:selected').data('name')

    let box_id = $(this).data('box-id')

    axios.post(WPURL.siteurl + '/wp-json/agrispesa/v1/weekly-box/' + box_id + '/products', {
      product_ids: [product_id],
      quantity: [quantity],
      product_name: [product_name]
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
