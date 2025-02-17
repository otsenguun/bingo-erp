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
              {{ $t('Create invoice return') }}
            </h3>
            <router-link :to="{ name: 'invoiceReturns.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveInvoiceReturn" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="returnReason">{{ $t('Return Reason') }}
                    <span class="required">*</span></label>
                  <input id="returnReason" v-model="form.returnReason" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('returnReason') }" name="returnReason"
                    :placeholder="$t('Enter a reason')" />
                  <has-error :form="form" field="returnReason" />
                </div>
                <div class="form-group col-md-6">
                  <label for="client">{{ $t('Client') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.client" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('client') }" name="client"
                    :placeholder="$t('Select a client')" @input="assignInvoices" />
                  <has-error :form="form" field="client" />
                </div>
              </div>
              <div v-if="products" class="row">
                <div class="form-group col-md-12">
                  <label for="product">{{
                    $t('Select Products')
                  }}</label>
                  <v-select :disabled="form.client == ''" multiple v-model="form.product" :options="products"
                    label="label" :class="{ 'is-invalid': form.errors.has('product') }" name="product"
                    :placeholder="$t('Search products')" @input="assignInvoices" />
                  <has-error :form="form" field="product" />
                </div>
              </div>
              <div class="row" v-if="form.client && clientInvoices">
                <div class="form-group col-md-12">
                  <label for="invoice">{{ $t('Invoices') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.invoice" :options="clientInvoices" label="label"
                    :class="{ 'is-invalid': form.errors.has('invoice') }" name="invoice"
                    :placeholder="$t('Select an invoice')" @input="storeProducts" />
                  <has-error :form="form" field="invoice" />
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
                        <th>{{ $t('Item Name') }}</th>
                        <th>{{ $t('Invoice Qty') }}</th>
                        <th>{{ $t('Current Qty') }}</th>
                        <th>{{ $t('Return Qty') }}</th>
                        <th>{{ $t('Unit Price') }}</th>
                        <th>{{ $t('Total Price') }}</th>
                        <th class="text-right">
                          {{ $t('Return Price') }}
                        </th>
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
                        <td>{{ item.qty }} {{ item.unit }}</td>
                        <td>{{ item.totalReturnQty }} {{ item.unit }}</td>
                        <td>
                          <div class="input-group custom-qty-input">
                            <input type="button" value="-" class="button-minus icon-shape icon-sm btn-danger"
                              data-field="quantity" @click="updateItem(item.returnQty - 1, i - 1)" />
                            <input type="number" step="any" :id="`returnQty-${i}`" :value="item.returnQty" name="quantity"
                              class="quantity-field border-0 incrementor" min="0" :max="item.maxQty"
                              @change="updateItem($event.target.value, i - 1)"
                              @keyup="updateItem($event.target.value, i - 1)" placeholder="Return Qty" />
                            <input type="button" value="+" class="button-plus icon-shape icon-sm btn-primary"
                              data-field="quantity" @click="
                                updateItem(Number(item.returnQty) + 1, i - 1)
                                " />
                          </div>
                        </td>
                        <td>{{ item.unitCost | withCurrency }}</td>
                        <td>{{ item.totalPrice | withCurrency }}</td>
                        <td class="text-right">
                          {{ item.returnTotal | withCurrency }}
                        </td>
                      </tr>
                      <tr v-if="form.invoice">
                        <td colspan="7" class="text-right">
                          <strong>{{ $t('Subtotal') }}</strong>
                        </td>
                        <td class="text-center">
                          <strong>{{
                            form.invoice.subTotal | withCurrency
                          }}</strong>
                        </td>
                        <td class="text-right">
                          <strong>{{ form.totalReturn | withCurrency }}</strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-if="form.invoice" class="row">
                <div v-if="form.discountPercentage > 0" class="form-group col-md-2">
                  <label for="discountType">{{
                    $t('Discount Type')
                  }}</label>
                  <select id="discountType" v-model="form.discountType" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('discountType') }" name="discountType" disabled>
                    <option value="0">{{ $t('Fixed') }}</option>
                    <option value="1">{{ $t('Percentage') }}(%)</option>
                  </select>
                  <has-error :form="form" field="discountType" />
                </div>
                <div class="form-group" :class="form.discountPercentage > 0 ? 'col-md-2' : 'col-md-4'">
                  <label for="invoiceDiscount">{{
                    $t('Total discount')
                  }}</label>
                  <input id="invoiceDiscount" v-model="form.invoiceDiscount" type="number" step="any" class="form-control"
                    name="invoiceDiscount" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="invoiceTransport">{{
                    $t('Transport Cost')
                  }}</label>
                  <input id="invoiceTransport" v-model="form.invoiceTransport" type="number" step="any"
                    class="form-control" name="invoiceTransport" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="invoiceTax">{{
                    $t('Invoice Tax')
                  }}</label>
                  <input id="invoiceTax" v-model="form.newTax" type="number" step="any" class="form-control"
                    name="invoiceTax" readonly />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="invoiceTotal">{{
                    $t('Invoice Total')
                  }}</label>
                  <input id="invoiceTotal" v-model="form.invoiceTotal" type="number" step="any" class="form-control"
                    name="invoiceTotal" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="totalPaid">{{ $t('Total Paid') }}</label>
                  <input id="totalPaid" v-model="form.invoice.totalPaid" type="number" step="any" class="form-control"
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
                    $t('New Due')
                  }}</label>
                  <input id="newDueText" v-model="form.newDueText" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('newDueText') }" name="newDueText" readonly />
                  <has-error :form="form" field="newDueText" />
                </div>
              </div>
              <div v-if="accounts && form.returnAmount > 0" class="row">
                <div class="form-group col-md-4">
                  <label for="account">{{ $t('Account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.account" :options="accounts" label="label"
                    :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                    :placeholder="$t('Select an account')" @input="updateBalance">
                     <template slot="option" slot-scope="option">
                        <img :src="option.image" style="width: 30px; height: 30px;" />
                        {{ option.label }}
                    </template>
                  </v-select>
                  <has-error :form="form" field="account" />
                </div>
                <div class="form-group col-md-2">
                  <label for="availableBalance">{{
                    $t('Available Balance')
                  }}</label>
                  <input id="availableBalance" v-model="form.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
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
                  <label for="date">{{ $t('Return Date') }}</label>
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
    return { title: this.$t('Create Invoice Return') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Invoice Return',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Invoice Returns',
        url: 'invoiceReturns.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      returnReason: '',
      account: '',
      availableBalance: 0,
      chequeNo: '',
      receiptNo: '',
      client: '',
      invoice: '',
      product: '',
      selectedProducts: [],
      totalReturn: 0,
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      invoiceTax: 0,
      newTax: 0,
      invoiceDiscount: 0,
      discountType: 0,
      discountPercentage: 0,
      invoiceTransport: 0,
      invoiceTaxRate: 0,
      invoiceTotal: 0,
      invoiceDue: 0,
      totalPaid: 0,
      newDue: 0,
      newDueText: '',
      returnAmount: 0,
      returnAmountText: 0,
      newSubTotal: 0,
    }),
    products: '',
    accounts: '',
    clientInvoices: '',
    prefix: '',
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getClients()
    this.getProducts()
    this.getAccounts()
    this.prefix = this.appInfo.productPrefix
  },
  methods: {
    // get all clients
    async getClients() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-clients',
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

    // update available balance
    updateBalance() {
      this.form.availableBalance = 0
      if (this.form.account) {
        this.form.availableBalance = this.form.account.availableBalance
      }
      return
    },

    // assign invoices
    async assignInvoices() {
      this.form.selectedProducts = []
      this.form.invoice = ''
      if (this.form.client) {
        axios
          .post(window.location.origin + '/api/client/filter-invoices', {
            products: this.form.product,
            clientSlug: this.form.client.slug,
          })
          .then((response) => {
            this.clientInvoices = response.data.data
          })
      } else {
        this.form.product = ''
        this.form.client = ''
      }
    },

    // store item in array
    storeProducts() {
      this.form.selectedProducts = []
      this.form.invoiceTax = this.form.invoice.tax
      this.form.newTax = this.form.invoice.tax
      this.form.invoiceTaxRate = this.form.invoice.taxRate
      this.form.invoiceTotal = this.form.invoice.invoiceTotal
      this.form.invoiceTransport = this.form.invoice.transport
      this.form.invoiceDiscount = this.form.invoice.discount
      this.form.discountType = this.form.invoice.discountType
      this.form.discountPercentage = this.form.invoice.discountPercentage
      this.form.invoiceDue = this.form.invoice.due
      this.form.newDue = this.form.invoice.due
      this.form.newDueText = this.form.invoice.due
      for (var key in this.form.invoice.invoiceProducts) {
        let invoiceItem = this.form.invoice.invoiceProducts[key]
        this.form.selectedProducts.unshift({
          id: invoiceItem.productID,
          slug: invoiceItem.productSlug,
          name: invoiceItem.productName,
          code: invoiceItem.productCode,
          unit: invoiceItem.productUnit,
          taxType: invoiceItem.taxType,
          taxRate: invoiceItem.taxRate,
          oldQty: invoiceItem.quantity,
          qty: invoiceItem.quantity,
          returnQty: invoiceItem.returnQty,
          totalReturnQty: invoiceItem.quantity - invoiceItem.returnQty,
          inventoryCount: invoiceItem.inventoryCount,
          avgPurchasePrice: invoiceItem.purchasePrice,
          unitPrice: invoiceItem.salePrice,
          unitCost: invoiceItem.unitCost,
          totalPrice: invoiceItem.unitCostTotal,
          returnTotal: 0,
          productTax: invoiceItem.unitTax,
          totalTax: invoiceItem.taxTotal,
          maxQty: invoiceItem.quantity - 1,
        })
      }
      return
    },

    // update items
    updateItem(value, index) {
      let selectedProduct = this.form.selectedProducts[index]
      if (selectedProduct && value >= 0 && value < selectedProduct.qty) {
        selectedProduct.returnQty = Number(value)
        selectedProduct.returnTotal =
          selectedProduct.returnQty * selectedProduct.unitCost
        this.form.selectedProducts[index] = selectedProduct
      }
      this.calculateSum()
    },

    // calculate sum
    calculateSum() {
      // calculate total
      let length = this.form.selectedProducts.length
      this.form.newSubTotal = this.form.totalReturn = 0
      for (let i = 0; i < length; i++) {
        let looProduct = this.form.selectedProducts[i]
        this.form.newSubTotal += Number(
          (
            (looProduct.qty - looProduct.returnQty) *
            looProduct.unitCost
          ).toFixed(2)
        )
        this.form.totalReturn += Number(looProduct.returnTotal.toFixed(2))
      }

      // update discount
      if (this.form.discountType == 1) {
        this.form.invoiceDiscount = Number(
          (this.form.discountPercentage / 100) * this.form.newSubTotal
        ).toFixed(2)
      }

      // update tax, total and due
      this.form.newTax = Number(
        ((this.form.invoiceTaxRate.rate / 100) * this.form.newSubTotal).toFixed(
          2
        )
      )
      this.form.invoiceTotal = Number(
        (
          this.form.newSubTotal +
          this.form.newTax +
          this.form.invoiceTransport -
          this.form.invoiceDiscount
        ).toFixed(2)
      )
      this.form.invoiceDue = Number(
        (this.form.invoiceTotal - this.form.invoice.totalPaid).toFixed(2)
      )

      // calculate new due or payable
      if (this.form.invoiceDue >= 0) {
        this.form.newDue = this.form.invoiceTotal - this.form.invoice.totalPaid
        this.form.newDueText =
          this.form.invoiceTotal +
          ' - ' +
          this.form.invoice.totalPaid +
          ' = ' +
          Number(this.form.newDue).toFixed(2)
        this.form.returnAmount = 0
      } else {
        this.form.returnAmount = Number(
          (this.form.invoice.totalPaid - this.form.invoiceTotal).toFixed(2)
        )
        this.form.returnAmountText =
          this.form.invoice.totalPaid +
          ' - ' +
          this.form.invoiceTotal +
          ' = ' +
          this.form.returnAmount
        this.form.invoiceDue = 0
        this.form.newDue = 0
      }
      return
    },

    // save return
    async saveInvoiceReturn() {
      await this.form
        .post(window.location.origin + '/api/invoice-returns')
        .then(({ data }) => {
          toast.fire({
            type: 'success',
            title: this.$t('Invoice return added successfully'),
          })
          this.$router.push({ name: 'invoiceReturns.show', params: { slug: data.data.slug }, })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
