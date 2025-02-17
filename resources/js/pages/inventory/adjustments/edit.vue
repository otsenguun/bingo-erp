<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Edit adjustment') }}
            </h3>
            <router-link :to="{ name: 'adjustments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateAdjustment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="adjustmentNo">{{
                    $t('Adjustment No')
                  }}</label>
                  <input id="adjustmentNo" v-model="form.adjustmentNo" type="text" class="form-control"
                    name="adjustmentReason" readonly />
                  <has-error :form="form" field="adjustmentNo" />
                </div>
                <div class="form-group col-md-6">
                  <label for="adjustmentReason">{{
                    $t('Adjustment Reason')
                  }}</label>
                  <input id="adjustmentReason" v-model="form.adjustmentReason" type="text" class="form-control" :class="{
                    'is-invalid': form.errors.has('adjustmentReason'),
                  }" name="adjustmentReason" :placeholder="$t('Enter a reason')" />
                  <has-error :form="form" field="adjustmentReason" />
                </div>
              </div>
              <div class="row">
                <div v-if="products" class="form-group col-md-12">
                  <label for="product">{{ $t('Select Products') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.product" :options="products" label="label" :class="{
                    'is-invalid': form.errors.has('selectedProducts'),
                  }" name="product" :placeholder="$t('Search products')"
                    @input="storeProduct(form.product)" />
                  <has-error :form="form" field="selectedProducts" />
                </div>
              </div>
              <div v-if="form.selectedProducts && form.selectedProducts.length > 0" class="row mt-3 mb-2">
                <div class="table-responsive table-custom w-95 m-auto">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>{{ $t('#') }}</th>
                        <th>{{ $t('Code') }}</th>
                        <th>{{ $t('Name') }}</th>
                        <th>{{ $t('Stock') }}</th>
                        <th class="w-200px">
                          {{ $t('Adjustment Type') }}
                        </th>
                        <th class="w-250px">{{ $t('Quantity') }}</th>
                        <th class="text-right">{{ $t('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, i) in form.selectedProducts" :key="i">
                        <td>{{ ++i }}</td>
                        <td>{{ item.itemCode | withPrefix(productPrefix) }}</td>
                        <td>{{ item.name }}</td>
                        <td>
                          <span class="btn btn-warning btn-sm">{{
                            item.stockQty
                          }}</span>
                        </td>
                        <td>
                          <select class="form-control w-200px" @change="
                            updateProduct(
                              $event.target.value,
                              'adjustType',
                              i - 1
                            )
                            " :id="`adjustType-${i}`" required>
                            <option value="Increment" :selected="item.adjustType == 'Increment'">
                              {{ $t('Increment') }}
                            </option>
                            <option value="Decrement" :selected="item.adjustType == 'Decrement'">
                              {{ $t('Decrement') }}
                            </option>
                          </select>
                        </td>
                        <td>
                          <div class="input-group custom-qty-input">
                            <input type="button" value="-" class="button-minus icon-shape icon-sm btn-danger"
                              data-field="quantity"
                              @click="updateProduct(item.adjustQty > 1 ? item.adjustQty - 1 : 1, 'quantity', i - 1)" />
                            <input type="number" step="any" :id="`Qty-${i}`" name="quantity"
                              class="quantity-field border-0 incrementor" @change="
                                updateProduct($event.target.value, 'quantity', i - 1)"
                              @keyup="updateProduct($event.target.value, 'quantity', i - 1)"
                              :placeholder="$t('Quantity')" min="1" :max="item.maxQty" :value="item.adjustQty"
                              required />
                            <input type="button" value="+" class="button-plus icon-shape icon-sm btn-primary"
                              data-field="quantity" @click="updateProduct(item.adjustQty + 1, 'quantity', i - 1)" />
                          </div>
                        </td>
                        <td class="text-right">
                          <button type="button" class="btn btn-danger" @click="removeItem(item)">
                            <i class="fas fa-times"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="form-group">
                <label for="note">{{ $t('Note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="adjustmentDate">{{
                    $t('Adjustment Date')
                  }}</label>
                  <input id="adjustmentDate" v-model="form.adjustmentDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('adjustmentDate') }" name="adjustmentDate" />
                  <has-error :form="form" field="adjustmentDate" />
                </div>
                <div class="form-group col-md-6">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-eidt" /> {{ $t('Save changes') }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t('Reset') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Adjustment') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Adjustment',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Adjustments',
        url: 'adjustments.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      adjustmentNo: '',
      selectedProducts: [],
      adjustmentReason: '',
      adjustmentDate: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
    }),
    products: '',
    productPrefix: '',
    adjustmentPrefix: '',
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getProducts()
    this.getAdjustment()
    this.productPrefix = this.appInfo.productPrefix
    this.adjustmentPrefix = this.appInfo.adjustmentPrefix
  },
  methods: {
    // get the adjustment
    async getAdjustment() {
      const { data } = await axios.get(
        window.location.origin +
        '/api/inventory-adjustments/' +
        this.$route.params.slug
      )
      this.form.adjustmentNo = this.$options.filters.withPrefix(
        data.data.code,
        this.adjustmentPrefix
      )
      this.form.adjustmentReason = data.data.reason
      this.form.adjustmentDate = data.data.date
      this.form.note = data.data.note
      this.form.status = data.data.status
      this.assignProducts(data.data.adjustmentProducts)
    },

    // get products
    async getProducts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-products'
      )
      this.products = data.data
    },

    // store item in array
    storeProduct(product) {
      var index = this.form.selectedProducts.findIndex(
        (x) => x.id == product.id
      )
      let qunatity = 1
      if (index === -1) {
        // store product
        this.form.selectedProducts.unshift({
          id: product.id,
          slug: product.slug,
          name: product.name,
          itemCode: product.code,
          purchasePrice: product.avgPurchasePrice,
          stockQty: product.inventoryCount,
          adjustType: 'Increment',
          adjustQty: qunatity,
          maxQty: 9999,
        })
      } else {
        // update qty
        if (this.form.selectedProducts[index]) {
          qunatity = this.form.selectedProducts[index].adjustQty
            ? this.form.selectedProducts[index].adjustQty + 1
            : 1
          this.form.selectedProducts[index].adjustQty = qunatity
        }
      }
      return
    },

    // update array
    updateProduct(value, type, index) {
      let selectedProduct = this.form.selectedProducts[index]
      if (selectedProduct) {
        if (type == 'quantity' && selectedProduct.adjustQty >= 1) {
          selectedProduct.adjustQty = value
        } else if (type == 'adjustType') {
          selectedProduct.adjustType = value
          if (selectedProduct.adjustType == 'Decrement') {
            selectedProduct.maxQty = selectedProduct.stockQty
          } else {
            selectedProduct.maxQty = 9999
          }
        } else {
          selectedProduct.purchasePrice = value
        }
      }
      this.form.selectedProducts[index] = selectedProduct
      return
    },

    // remove item from array
    removeItem(item) {
      let index = this.form.selectedProducts.indexOf(item)
      if (index > -1) {
        this.form.selectedProducts.splice(index, 1)
      }
      return
    },

    // get order products
    assignProducts(products) {
      for (var key in products) {
        let adjustmentItem = products[key]
        this.form.selectedProducts.unshift({
          id: adjustmentItem.productID,
          slug: adjustmentItem.productSlug,
          name: adjustmentItem.productName,
          itemCode: adjustmentItem.productCode,
          itemUnit: adjustmentItem.productUnit,
          purchasePrice: adjustmentItem.purchasePrice,
          stockQty: adjustmentItem.productStock,
          oriAdjustType: adjustmentItem.type == 1 ? 'Increment' : 'Decrement',
          oriAdjustQty: adjustmentItem.quantity,
          adjustType: adjustmentItem.type == 1 ? 'Increment' : 'Decrement',
          adjustQty: adjustmentItem.quantity,
          maxQty: adjustmentItem.type == 0 ? adjustmentItem.productStock : 9999,
        })
      }
    },

    // update adjustments
    async updateAdjustment() {
      await this.form
        .patch(
          window.location.origin +
          '/api/inventory-adjustments/' +
          this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Adjustment updated successfully'),
          })
          this.$router.push({ name: 'adjustments.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Opps...something went wrong'),
          })
        })
    },
  },
}
</script>
