jQuery(document).ready(function ($) {
  $(".select2").select2()
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
  methods: {
    createBox: function () {
      const $vm = this
      axios.post('/wp-json/agrispesa/v1/weekly-box', {
        product_box_id: document.getElementById('box_id').value,
        week: document.getElementById('week').value,
        products: $vm.products
      })
        .then((response) => {
          console.log(response.data)
        });
    },
    deleteProduct: function (index) {
      this.products.splice(index, 1);
    },
    addProduct: function () {
      let productId = document.getElementById('products_id').value
      let product = this.productJson.filter(function (product) {
        return product['id'] == productId
      })
      this.products.push(product[0])
    }
  }
}).mount('#box-app')
