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
              {{ $t('Edit purchase payment') }}
            </h3>
            <router-link :to="{ name: 'purchasePayments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updatePayment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="purchase">{{ $t('Purchase No')
                  }}<span class="required">*</span></label>
                  <input id="purchaseNo" v-model="form.purchaseNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('purchaseNo') }" name="purchaseNo" readonly />
                  <has-error :form="form" field="purchaseNo" />
                </div>
                <div class="form-group col-md-4">
                  <label for="purchaseTotal">{{
                    $t('Purchase Total')
                  }}</label>
                  <input id="purchaseTotal" v-model="form.purchaseTotal" type="text" step="any" class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('purchaseTotal'),
                    }" name="purchaseTotal" readonly />
                  <has-error :form="form" field="purchaseTotal" />
                </div>
                <div class="form-group col-md-4">
                  <label for="purchaseDue">{{ $t('Total Due') }}</label>
                  <input id="purchaseDue" v-model="form.purchaseDue" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('purchaseDue') }" name="purchaseDue" readonly />
                  <has-error :form="form" field="purchaseDue" />
                </div>
              </div>

              <div class="row" v-if="accounts">
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
                <div class="form-group col-md-4">
                  <label for="availableBalance">{{ $t('Available Balance') }}
                    <span class="required">*</span></label>
                  <input id="availableBalance" v-model="form.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
                </div>
                <div class="form-group col-md-4">
                  <label for="paidAmount">{{ $t('Paid Amount') }}
                    <span class="required">*</span></label>
                  <input id="paidAmount" v-model="form.paidAmount" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount" :min="form.minAmount"
                    :max="form.maxAmount" :placeholder="$t('Enter an amount')" @change="calculateDue"
                    @keyup="calculateDue" />
                  <has-error :form="form" field="paidAmount" />
                </div>
              </div>

              <div class="row">
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
                <div class="form-group col-md-3">
                  <label for="paymentDate">{{
                    $t('Payment Date')
                  }}</label>
                  <input id="paymentDate" v-model="form.paymentDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('paymentDate') }" name="paymentDate" />
                  <has-error :form="form" field="paymentDate" />
                </div>
                <div class="form-group col-md-3">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
              </div>
              <div class="form-group">
                <label for="note">{{ $t('Note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t('Save changes') }}
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
    return { title: this.$t('Edit Supplier Purchase Payment') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Payment',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Payments',
        url: '',
      },
      {
        name: 'Purchase Payments',
        url: 'purchasePayments.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      purchase: '',
      purchaseNo: '',
      availableBalance: '',
      purchaseTotal: 0,
      purchaseDue: 0,
      previousPaidAmount: 0,
      account: '',
      maxAmount: 0,
      minAmount: 0,
      paidAmount: 0,
      chequeNo: '',
      receiptNo: '',
      paymentDate: '',
      note: '',
      status: 1,
    }),
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getAccounts()
    this.getPurchasePayment()
  },
  methods: {
    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // get purchase payment
    async getPurchasePayment() {
      const { data } = await axios.get(
        window.location.origin +
        '/api/payments/purchase/' +
        this.$route.params.slug
      )
      this.form.purchaseNo = data.data.purchase.purchaseNo
      this.form.purchase = data.data.purchase
      this.form.purchaseTotal =
        data.data.purchase.subTotal +
        data.data.purchase.tax +
        data.data.purchase.transport -
        data.data.purchase.totalDiscount -
        data.data.costOfReturn
      this.form.purchaseDue = data.data.purchase.due
      this.form.previousPaidAmount = data.data.amount
      this.form.account = data.data.account
      this.form.availableBalance = data.data.account.availableBalance
      this.form.paidAmount = data.data.amount
      this.form.maxAmount = data.data.amount + data.data.purchase.due
      this.form.minAmount =
        data.data.purchase.accountReceivable > 0 ? +data.data.amount : 1
      this.form.chequeNo = data.data.transaction.cheque_no
      this.form.receiptNo = data.data.transaction.receipt_no
      this.form.paymentDate = data.data.date
      this.form.note = data.data.note
      this.form.status = data.data.status
    },

    // update available balance
    updateBalance() {
      if (this.form.account) {
        this.form.availableBalance = this.form.account.availableBalance
      }
      return this.form.availableBalance
    },

    // calculate due
    calculateDue() {
      let paid = Number(this.form.paidAmount)
      if (paid <= this.form.maxAmount && paid >= this.form.minAmount) {
        this.form.purchaseDue =
          this.form.purchase.due + this.form.previousPaidAmount - paid
      }
    },

    // update payment
    async updatePayment() {
      await this.form
        .patch(
          window.location.origin +
          '/api/payments/purchase/' +
          this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Payment updated successfully'),
          })
          this.$router.push({ name: 'purchasePayments.index' })
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
