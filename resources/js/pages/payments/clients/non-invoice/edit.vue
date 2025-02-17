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
              {{ $t('Edit non invoice payment') }}
            </h3>
            <router-link :to="{ name: 'nonInvoicePayments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updatePayment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="client">{{ $t('Client')
                  }}<span class="required">*</span></label>
                  <v-select v-model="form.client" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('client') }" name="client"
                    :placeholder="$t('Select a client')" disabled />
                  <has-error :form="form" field="client" />
                </div>
                <div class="form-group col-md-6">
                  <label for="type">{{ $t('Type') }}</label>
                  <select id="type" v-model="form.type" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('type') }" disabled>
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
              <div v-if="form.client" class="row">
                <div class="form-group col-md-4">
                  <label for="nonInvoiceTotal">{{
                    $t('Non Invoice Total')
                  }}</label>
                  <input id="nonInvoiceTotal" v-model="form.nonInvoiceTotal" type="number" step="any" class="form-control"
                    name="nonInvoiceTotal" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="nonInvoicePaid">{{
                    $t('Non Invoice Paid')
                  }}</label>
                  <input id="nonInvoicePaid" v-model="form.nonInvoicePaid" type="number" step="any" class="form-control"
                    name="nonInvoicePaid" readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="currentDue">{{
                    $t('Current Due')
                  }}</label>
                  <input id="nonInvoiceDue" v-model="form.nonInvoiceDue" type="number" step="any" class="form-control"
                    name="nonInvoiceDue" readonly />
                </div>
              </div>
              <div class="row" v-if="form.type == 1 && accounts">
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
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="paidAmount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input id="paidAmount" v-model="form.paidAmount" type="number" step="any" min="1" :max="form.max"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('paidAmount') }" name="paidAmount"
                    :placeholder="$t('Enter an amount')" @change="updateValues" @keyup="updateValues" />
                  <has-error :form="form" field="paidAmount" />
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
    return { title: this.$t('Edit Invoice Return') }
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
        name: 'Non Invoice Payments',
        url: 'nonInvoicePayments.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      client: '',
      type: 0,
      account: '',
      paidAmount: '',
      chequeNo: '',
      receiptNo: '',
      max: 99999999999,
      nonInvoiceTotal: 0,
      nonInvoicePaid: 0,
      nonInvoiceDue: 0,
      rowDue: 0,
      rowPaid: 0,
      paymentDate: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
    }),
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getClients()
    this.getAccounts()
    this.getPayment()
  },
  methods: {
    // get all clients
    async getClients() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-clients',
      })
    },

    // get payment
    async getPayment() {
      const { data } = await axios.get(
        window.location.origin +
        '/api/payments/non-invoice/' +
        this.$route.params.slug
      )
      this.form.client = data.data.client
      this.form.type = data.data.type
      this.form.nonInvoiceTotal = data.data.client.nonInvoiceDue
      this.form.nonInvoicePaid = data.data.client.nonInvoicePaid
      this.form.nonInvoiceDue = data.data.client.nonInvoiceCurrentDue

      this.form.rowDue = data.data.client.nonInvoiceCurrentDue
      this.form.rowPaid = data.data.amount

      this.form.account = data.data.account
      this.form.paidAmount = data.data.amount
      this.form.paymentDate = data.data.date
      this.form.note = data.data.note
      this.form.status = data.data.status

      this.form.chequeNo = data.data.transaction
        ? data.data.transaction.cheque_no
        : ''
      this.form.receiptNo = data.data.transaction
        ? data.data.transaction.receipt_no
        : ''
      this.form.max =
        data.data.type == 1
          ? data.data.client.nonInvoiceCurrentDue + data.data.amount
          : 99999999999
    },

    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // update values
    updateValues() {
      let paidAmount = Number(this.form.paidAmount)
      if (this.form.client && this.form.type == 1) {
        this.form.nonInvoicePaid =
          Number(this.form.client.nonInvoicePaid) -
          Number(this.form.rowPaid) +
          paidAmount
        this.form.nonInvoiceDue =
          Number(this.form.client.nonInvoiceCurrentDue + this.form.rowPaid) -
          paidAmount
      }

      if (this.form.client && this.form.type == 0) {
        this.form.nonInvoiceTotal =
          Number(this.form.client.nonInvoiceDue) -
          Number(this.form.rowPaid) +
          paidAmount
        this.form.nonInvoicePaid = Number(this.form.client.nonInvoicePaid)
        this.form.nonInvoiceDue =
          Number(this.form.client.nonInvoiceCurrentDue - this.form.rowPaid) +
          paidAmount
      }
      return
    },

    // update payment
    async updatePayment() {
      await this.form
        .patch(
          window.location.origin +
          '/api/payments/non-invoice/' +
          this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Payment updated successfully'),
          })
          this.$router.push({ name: 'nonInvoicePayments.index' })
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
