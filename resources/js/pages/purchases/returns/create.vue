<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12 col-xl-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Create purchase return') }}
            </h3>
            <router-link :to="{ name: 'purchaseReturns.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="savePurchaseReturn" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="returnReason">{{ $t('Return Reason') }}
                    <span class="required">*</span></label>
                  <input id="returnReason" v-model="form.returnReason" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('returnReason') }" name="returnReason" :placeholder="$t('Enter a reason')
                      " />
                  <has-error :form="form" field="returnReason" />
                </div>
                <div class="form-group col-md-6">
                  <label for="supplier">{{ $t('Supplier') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.supplier" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('supplier') }" name="supplier"
                    :placeholder="$t('Select a supplier')" @input="assignPurchases" />
                  <has-error :form="form" field="supplier" />
                </div>
              </div>
              <div v-if="products && !form.purchase" class="row">
                <div class="form-group col-md-12">
                  <label for="product">{{
                    $t('Select Products')
                  }}</label>
                  <v-select :disabled="form.client == ''" multiple v-model="form.product" :options="products"
                    label="label" :class="{ 'is-invalid': form.errors.has('product') }" name="product"
                    :placeholder="$t('Search products')" @input="assignPurchases" />
                  <has-error :form="form" field="product" />
                </div>
              </div>
              <div class="row" v-if="form.supplier && supplierPurchases">
                <div class="form-group col-md-12">
                  <label for="purchase">{{ $t('Purchases') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.purchase" :options="supplierPurchases" label="purchaseNo"
                    :class="{ 'is-invalid': form.errors.has('purchase') }" name="purchase"
                    :placeholder="$t('Select a purchase')" @input="storeProducts" />
                  <has-error :form="form" field="purchase" />
                </div>
              </div>
              <div v-if="form.selectedProducts && form.selectedProducts.length > 0" class="row mt-3 mb-4">
                <div v-if="form.errors.errors && form.errors.errors.selectedProducts
                  " class="w-95 m-auto">
                  <div v-for="(msg, i) in form.errors.errors.selectedProducts" :key="i" class="callout callout-danger">
                    <p><i class="icon fas fa-ban"></i> {{ msg }}</p>
                  </div>
                </div>
                <div class="table-responsive table-custom w-95 m-auto">
                  <table class="table table-hover table-sm text-center">
                    <thead>
                      <tr>
                        <th>{{ $t('#') }}</th>
                        <th>{{ $t('Code') }}</th>
                        <th>{{ $t('Name') }}</th>
                        <th>{{ $t('Purchased Qty') }}</th>
                        <th>{{ $t('Current Qty') }}</th>
                        <th>{{ $t('Returned Qty') }}</th>
                        <th>{{ $t('Unit Cost') }}</th>
                        <th>{{ $t('Total Price') }}</th>
                        <th>{{ $t('Return Price') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, i) in form.selectedProducts" :key="i">
                        <td>{{ ++i }}</td>
                        <td>{{ item.code | withPrefix(prefix) }}</td>
                        <td>
                          <router-link v-if="$can('product-view')" :to="{
                            name: 'products.show',
                            params: { slug: item.slug },
                          }">
                            {{ item.name }}
                          </router-link>
                          <span v-else>{{ item.name }}</span>
                        </td>
                        <td>{{ item.purchaseQty }} {{ item.unit }}</td>
                        <td>{{ item.totalReturnQty }} {{ item.unit }}</td>
                        <td>
                          <div class="input-group custom-qty-input">
                            <input type="button" value="-" class="button-minus icon-shape icon-sm btn-danger"
                              data-field="quantity" @click="
                                updateItem(Number(item.returnQty) - 1, i - 1)
                                " />
                            <input type="number" step="any" :id="`returnQty-${i}`" placeholder="Return Qty"
                              class="quantity-field border-0 incrementor" @change="updateItem($event.target.value, i - 1)"
                              @keyup="updateItem($event.target.value, i - 1)" :value="item.returnQty" min="0"
                              :max="item.purchaseQty" />
                            <input type="button" value="+" class="button-plus icon-shape icon-sm btn-primary"
                              data-field="quantity" @click="
                                updateItem(
                                  item.returnQty > 0
                                    ? Number(item.returnQty) + 1
                                    : 1,
                                  i - 1
                                )
                                " />
                          </div>
                        </td>
                        <td>{{ item.purchasePrice | withCurrency }}</td>
                        <td>{{ item.totalPrice | withCurrency }}</td>
                        <td>{{ item.returnTotal | withCurrency }}</td>
                      </tr>
                      <tr v-if="form.purchase">
                        <td colspan="7" class="text-right">
                          <strong>{{ $t('Subtotal') }}:</strong>
                        </td>
                        <td>
                          <strong>{{
                            form.purchase.subTotal | withCurrency
                          }}</strong>
                        </td>
                        <td>
                          <strong>{{ form.totalReturn | withCurrency }}</strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-if="form.purchase" class="row">
                <div class="form-group col-md-4">
                  <label for="discount">{{
                    $t('Discount')
                  }}</label>
                  <input id="discount" v-model="form.purchase.totalDiscount" type="number" step="any" class="form-control"
                    name="discount" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="transportCost">{{
                    $t('Transport Cost')
                  }}</label>
                  <input id="transportCost" v-model="form.purchase.transport" type="number" step="any"
                    class="form-control" name="transportCost" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="purchaseTax">{{
                    $t('Purchase Tax')
                  }}</label>
                  <input id="purchaseTax" v-model="form.newTax" type="number" step="any" class="form-control"
                    name="purchaseTax" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="purchaseTotal">{{
                    $t('Purchase Total')
                  }}</label>
                  <input id="purchaseTotal" v-model="form.purchaseTotal" type="number" step="any" class="form-control"
                    name="purchaseTotal" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="totalPaid">{{ $t('Total Paid') }}</label>
                  <input id="totalPaid" v-model="form.purchase.totalPaid" type="number" step="any" class="form-control"
                    name="totalPaid" readonly />
                </div>
                <div v-if="form.returnAmount > 0" class="form-group col-md-4">
                  <label for="returnAmountText">{{
                    $t('Return Amount')
                  }}</label>
                  <input id="returnAmountText" v-model="form.returnAmountText" type="text" class="form-control" :class="{
                    'is-invalid': form.errors.has('returnAmountText'),
                  }" name="returnAmountText" readonly />
                  <has-error :form="form" field="returnAmountText" />
                </div>
                <div v-else class="form-group col-md-4">
                  <label for="newDueText">{{
                    $t('Purchase Due')
                  }}</label>
                  <input id="newDueText" v-model="form.newDueText" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('newDueText') }" name="newDueText" readonly />
                  <has-error :form="form" field="newDueText" />
                </div>
              </div>

              <div v-if="accounts && form.returnAmount > 0" class="row">
                <div class="form-group col-md-6">
                  <label for="account">{{ $t('Account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.account" :options="accounts" label="label"
                    :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                    :placeholder="$t('Select an account')">
                     <template slot="option" slot-scope="option">
                        <img :src="option.image" style="width: 30px; height: 30px;" />
                        {{ option.label }}
                    </template>
                  </v-select>
                  <has-error :form="form" field="account" />
                </div>
                <div class="form-group col-md-3">
                  <label for="chequeNo">{{ $t('Cheque No') }}</label>
                  <input id="chequeNo" v-model="form.chequeNo" type="text" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('chequeNo') }" name="chequeNo"
                    :placeholder="$t('Enter a cheque number')" />
                  <has-error :form="form" field="chequeNo" />
                </div>
                <div class="form-group col-md-3">
                  <label for="receiptNo">{{ $t('Receipt No') }}</label>
                  <input id="receiptNo" v-model="form.receiptNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('receiptNo') }" name="receiptNo"
                    :placeholder="$t('Enter a receipt no')" />
                  <has-error :form="form" field="receiptNo" />
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
                  <label for="date">{{
                    $t('Return Date')
                  }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                  <has-error :form="form" field="date" />
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
                <i class="fas fa-save" /> {{ $t('Save') }}
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
    return { title: this.$t('Create Purchase Return') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Purchase Return',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Purchase Returns',
        url: 'purchaseReturns.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      returnReason: '',
      account: '',
      chequeNo: '',
      receiptNo: '',
      supplier: '',
      purchase: '',
      product: '',
      selectedProducts: [],
      totalReturn: 0,
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      purchaseTotal: 0,
      newSubTotal: 0,
      purchaseTax: 0,
      purchaseTransport: 0,
      purchaseDiscount: 0,
      newTax: 0,
      taxRate: 0,
      purchaseDue: 0,
      newDue: 0,
      newDueText: '',
      returnAmount: 0,
      returnAmountText: 0,
    }),
    products: '',
    accounts: '',
    supplierPurchases: '',
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getSuppliers()
    this.getProducts()
    this.getAccounts()
    this.prefix = this.appInfo.productPrefix
  },
  methods: {
    // get all suppliers
    async getSuppliers() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-suppliers',
      })
    },

    // get products
    async getProducts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-products'
      )
      this.products = data.data
    },

    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // assign purchases
    async assignPurchases() {
      this.form.selectedProducts = []
      this.form.purchase = ''
      if (this.form.supplier) {
        axios
          .post(window.location.origin + '/api/supplier/filter-purchases', {
            products: this.form.product,
            supplierSlug: this.form.supplier.slug,
          })
          .then((response) => {
            this.supplierPurchases = response.data.data
          })
      } else {
        this.form.product = ''
        this.form.supplier = ''
      }
    },

    // store item in array
    storeProducts() {
      this.form.selectedProducts = []
      this.form.purchaseTotal = this.form.purchase.purchaseTotal
      this.form.purchaseDue = this.form.purchase.due
      this.form.purchaseTax = this.form.purchase.tax
      this.form.newTax = this.form.purchase.tax
      this.form.taxRate = this.form.purchase.taxRate
      this.form.purchaseTransport = this.form.purchase.transport
      this.form.purchaseDiscount = this.form.purchase.totalDiscount
      this.form.newDue = this.form.purchase.due
      this.form.newDueText = this.form.purchase.due
      for (var key in this.form.purchase.purchaseProducts) {
        let purchaseItem = this.form.purchase.purchaseProducts[key]
        this.form.selectedProducts.unshift({
          id: purchaseItem.productID,
          slug: purchaseItem.productSlug,
          name: purchaseItem.productName,
          code: purchaseItem.productCode,
          unit: purchaseItem.productUnit,
          purchaseQty: purchaseItem.quantity,
          totalReturnQty:
            Number(purchaseItem.quantity) - Number(purchaseItem.returnQty),
          returnQty: 0,
          returnTotal: 0,
          purchasePrice: purchaseItem.unitCost,
          totalPrice: purchaseItem.unitCostTotal,
        })
      }
      this.calculateSum()
      return
    },

    // update items
    updateItem(value, index) {
      let selectedProduct = this.form.selectedProducts[index]
      if (selectedProduct && value > 0) {
        selectedProduct.returnQty = Number(value)
        selectedProduct.returnTotal =
          selectedProduct.returnQty * selectedProduct.purchasePrice
        this.form.selectedProducts[index] = selectedProduct
      }
      this.calculateSum()
    },

    // calculate sum
    calculateSum() {
      this.form.totalReturn = this.form.selectedProducts.reduce(function (
        prev,
        cur
      ) {
        return Number((prev + cur.returnTotal).toFixed(2))
      },
        0)

      this.form.newSubTotal = this.form.selectedProducts.reduce(function (
        prev,
        cur
      ) {
        return Number(
          (
            prev +
            (cur.purchaseQty - cur.returnQty) * cur.purchasePrice
          ).toFixed(2)
        )
      },
        0)

      this.form.newTax = Number(
        ((this.form.taxRate / 100) * this.form.newSubTotal).toFixed(2)
      )
      this.form.purchaseTotal =
        this.form.newSubTotal +
        this.form.newTax +
        this.form.purchaseTransport -
        this.form.purchaseDiscount

      this.form.purchaseDue = Number(
        (this.form.purchaseTotal - this.form.purchase.totalPaid).toFixed(2)
      )
      if (this.form.purchaseDue >= 0) {
        this.form.newDue = Number(
          (this.form.purchaseTotal - this.form.purchase.totalPaid).toFixed(2)
        )
        this.form.newDueText =
          this.form.purchaseTotal +
          ' - ' +
          this.form.purchase.totalPaid +
          ' = ' +
          this.form.newDue
        this.form.returnAmount = 0
      } else {
        this.form.returnAmount = (
          this.form.purchase.totalPaid - this.form.purchaseTotal
        ).toFixed(2)
        this.form.returnAmountText =
          this.form.purchase.totalPaid +
          ' - ' +
          this.form.purchaseTotal +
          ' = ' +
          this.form.returnAmount
        this.form.purchaseDue = 0
      }
      return
    },

    // save return
    async savePurchaseReturn() {
      await this.form
        .post(window.location.origin + '/api/purchase-returns')
        .then(({ data }) => {
          toast.fire({
            type: 'success',
            title: this.$t('Purchase return added successfully'),
          })
          this.$router.push({ name: 'purchaseReturns.show', params: { slug: data.data.slug }, })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
