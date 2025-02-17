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
              {{ $t('Create non invoice payment') }}
            </h3>
            <router-link :to="{ name: 'nonInvoicePayments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="savePayment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items">
                <div class="form-group col-md-6">
                  <label for="client">{{ $t('Client')
                  }}<span class="required">*</span></label>
                  <v-select v-model="form.client" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('client') }" name="client"
                    :placeholder="$t('Select a client')" @input="calculateValues" />
                  <has-error :form="form" field="client" />
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
    return { title: this.$t('Create Non Invoice Payment') }
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
        name: 'Non Invoice Payments',
        url: 'nonInvoicePayments.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      client: '',
      type: 0,
      account: '',
      amount: '',
      chequeNo: '',
      receiptNo: '',
      max: 99999999999,
      nonInvoiceTotal: 0,
      nonInvoicePaid: 0,
      nonInvoiceDue: 0,
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
  },
  methods: {
    // get all clients
    async getClients() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/clients-for-noninvoice-payments',
      })
    },

    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // calculate values
    calculateValues() {
      if (this.form.client) {
        this.form.nonInvoiceTotal = this.form.client.nonInvoiceDue
        this.form.nonInvoicePaid = this.form.client.nonInvoicePaid
        this.form.nonInvoiceDue = this.form.client.nonInvoiceCurrentDue
        this.updateMax()
      }
      return
    },

    // update values
    updateValues() {
      let amount = Number(this.form.amount)
      if (this.form.client && this.form.type == 1) {
        this.form.nonInvoicePaid =
          Number(this.form.client.nonInvoicePaid) + amount
        this.form.nonInvoiceDue =
          Number(this.form.client.nonInvoiceCurrentDue) - amount
      }

      if (this.form.client && this.form.type == 0) {
        this.form.nonInvoiceTotal =
          Number(this.form.client.nonInvoiceDue) + amount
        this.form.nonInvoicePaid = Number(this.form.client.nonInvoicePaid)
        this.form.nonInvoiceDue =
          Number(this.form.client.nonInvoiceCurrentDue) + amount
      }
      return
    },

    // update max amount
    updateMax() {
      if (this.form.client && this.form.type == 1) {
        this.form.max = this.form.client.nonInvoiceCurrentDue
      } else {
        this.form.max = 99999999999
      }
      this.updateValues()
      return
    },

    // save payment
    async savePayment() {
      await this.form
        .post(window.location.origin + '/api/payments/non-invoice')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Payment added successfully'),
          })
          this.$router.push({ name: 'nonInvoicePayments.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
