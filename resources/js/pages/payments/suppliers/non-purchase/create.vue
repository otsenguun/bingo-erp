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
              {{ $t('Create non purchase payment') }}
            </h3>
            <router-link :to="{ name: 'nonPurchasePayments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="savePayment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="supplier">{{ $t('Supplier')
                  }}<span class="required">*</span></label>
                  <v-select v-model="form.supplier" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('supplier') }" name="supplier"
                    :placeholder="$t('Select a supplier')" @input="calculateValues" />
                  <has-error :form="form" field="supplier" />
                </div>
                <div class="form-group col-md-6">
                  <label for="type">{{ $t('Type') }}
                    <span class="required">*</span></label>
                  <select id="type" v-model="form.type" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('type') }" @change="updateMax">
                    <option value="0">
                      {{ $t('Add Due') }}
                    </option>
                    <option value="1">
                      {{ $t('Add Payment') }}
                    </option>
                  </select>
                  <has-error :form="form" field="type" />
                </div>
              </div>
              <div v-if="form.supplier" class="row">
                <div class="form-group col-md-4">
                  <label for="nonPurchaseTotal">{{
                    $t('Non Purchase Total')
                  }}</label>
                  <input id="nonPurchaseTotal" v-model="form.nonPurchaseTotal" type="number" step="any"
                    class="form-control" name="nonPurchaseTotal" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="nonPurchasePaid">{{
                    $t('Non Purchase Paid')
                  }}</label>
                  <input id="nonPurchasePaid" v-model="form.nonPurchasePaid" type="number" step="any" class="form-control"
                    name="nonPurchasePaid" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="currentDue">{{
                    $t('Current Due')
                  }}</label>
                  <input id="nonPurchaseDue" v-model="form.nonPurchaseDue" type="number" step="any" class="form-control"
                    name="nonPurchaseDue" readonly />
                </div>
              </div>
              <div class="row" v-if="form.type == 1 && accounts">
                <div class="form-group col-md-6">
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
                <div class="form-group col-md-6">
                  <label for="availableBalance">{{
                    $t('Available Balance')
                  }}</label>
                  <input id="availableBalance" v-model="form.availableBalance" type="text" step="any" class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                  <has-error :form="form" field="availableBalance" />
                </div>
                <div class="form-group col-md-6">
                  <label for="chequeNo">{{ $t('Cheque No') }}</label>
                  <input id="chequeNo" v-model="form.chequeNo" type="text" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('chequeNo') }" name="chequeNo"
                    :placeholder="$t('Enter a cheque number')" />
                  <has-error :form="form" field="chequeNo" />
                </div>
                <div class="form-group col-md-6">
                  <label for="receiptNo">{{ $t('Receipt No') }}</label>
                  <input id="receiptNo" v-model="form.receiptNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('receiptNo') }" name="receiptNo"
                    :placeholder="$t('Enter a receipt no')" />
                  <has-error :form="form" field="receiptNo" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="amount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amount" type="number" step="any" min="1" :max="form.max"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }" name="amount"
                    :placeholder="$t('Enter an amount')" @change="updateValues" @keyup="updateValues" />
                  <has-error :form="form" field="amount" />
                </div>
                <div class="form-group col-md-4">
                  <label for="paymentDate">{{
                    $t('Payment Date')
                  }}</label>
                  <input id="paymentDate" v-model="form.paymentDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('paymentDate') }" name="paymentDate" />
                  <has-error :form="form" field="paymentDate" />
                </div>
                <div class="form-group col-md-4">
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
    return {
      title: this.$t('Create Non Purchase Payment'),
    }
  },
  data: () => ({
    breadcrumbsCurrent:
      'Create Payment',
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
        name: 'Non Purchase Payments',
        url: 'nonPurchasePayments.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      supplier: '',
      type: 0,
      account: '',
      amount: '',
      chequeNo: '',
      receiptNo: '',
      max: 99999999999,
      nonPurchaseTotal: 0,
      nonPurchasePaid: 0,
      nonPurchaseDue: 0,
      availableBalance: 0,
      paymentDate: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
    }),
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getSuppliers()
    this.getAccounts()
  },
  methods: {
    // get all suppliers
    async getSuppliers() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/suppliers-for-nonpurchase-payments',
      })
    },

    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
      // assign default account
      if (this.accounts && this.accounts.length > 0) {
        let defaultAccountSlug = this.appInfo.defaultAccountSlug;
        this.form.account = this.accounts.find(account => account.slug == defaultAccountSlug);
        this.updateBalance()
      }
    },

    // update available balance
    updateBalance() {
      this.form.availableBalance = 0
      if (this.form.account) {
        this.form.availableBalance = this.form.account.availableBalance
      }
    },

    // calculate values
    calculateValues() {
      if (this.form.supplier) {
        this.form.nonPurchaseTotal = this.form.supplier.nonPurchaseTotalDue
        this.form.nonPurchasePaid = this.form.supplier.nonPurchasePaid
        this.form.nonPurchaseDue = this.form.supplier.nonPurchaseCurrentDue
        this.updateMax()
      }
      return
    },

    // update values
    updateValues() {
      let amount = Number(this.form.amount)
      if (this.form.supplier && this.form.type == 1) {
        this.form.nonPurchasePaid =
          Number(this.form.supplier.nonPurchasePaid) + amount
        this.form.nonPurchaseDue =
          Number(this.form.supplier.nonPurchaseCurrentDue) - amount
      }

      if (this.form.supplier && this.form.type == 0) {
        this.form.nonPurchaseTotal =
          Number(this.form.supplier.nonPurchaseTotalDue) + amount
        this.form.nonPurchasePaid = Number(this.form.supplier.nonPurchasePaid)
        this.form.nonPurchaseDue =
          Number(this.form.supplier.nonPurchaseCurrentDue) + amount
      }
      return
    },

    // update max amount
    updateMax() {
      if (this.form.supplier && this.form.type == 1) {
        this.form.max = this.form.supplier.nonPurchaseCurrentDue
      } else {
        this.form.max = 99999999999
      }
      this.updateValues()
      return
    },

    // save payment
    async savePayment() {
      await this.form
        .post(window.location.origin + '/api/payments/non-purchase')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t(
              'Payment added successfully'
            ),
          })
          this.$router.push({ name: 'nonPurchasePayments.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
