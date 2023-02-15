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
        weight += product.quantity
      })
      return weight + ' gr'
    },
    totalPrice() {
      let price = 0;
      this.products.forEach(function (product) {
        price += product.price * product.quantity
      })
      return price + ' €'
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
    addProduct: function () {
      if (document.getElementById('data_consegna').value == '') {
        alert('Inserisci una data di consegna')
        return false
      }
      if (document.getElementById('box_id').value == '') {
        alert('Seleziona una box')
        return false
      }

      let productId = document.getElementById('products_id').value
      let product = this.productJson.filter(function (product) {
        return product['id'] == productId
      })
      this.products.push(product[0])
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
          location.href = ''
        });
    }
  }
}).mount('#box-app')

