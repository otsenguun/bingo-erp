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
              {{ $t('Create a loan payment') }}
            </h3>
            <router-link :to="{ name: 'loanPayments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveLoanPayment" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div v-if="items" class="row">
                <div class="form-group col-md-6">
                  <label for="loan">{{ $t('Loan') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.loan" :options="items" label="reference"
                    :class="{ 'is-invalid': form.errors.has('loan') }" name="loan"
                    :placeholder="$t('Select a loan')" @input="updateAmount" />
                  <has-error :form="form" field="loan" />
                </div>
                <div class="form-group col-md-6">
                  <label for="referenceNo">{{ $t('Reference') }}
                    <span class="required">*</span></label>
                  <input id="referenceNo" v-model="form.referenceNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('referenceNo') }" name="referenceNo"
                    :placeholder="$t('Enter reference')" />
                  <has-error :form="form" field="referenceNo" />
                </div>
              </div>
              <div class="row" v-if="accounts">
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
                  <input id="availableBalance" v-model="form.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                </div>
              </div>
              <div class="row" v-if="form.loan">
                <div class="form-group col-md-6">
                  <label for="payableAmount">{{
                    $t('Payable Amount')
                  }}</label>
                  <input id="payableAmount" v-model="form.loan.payable" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('payableAmount') }" name="payableAmount" readonly />
                </div>
                <div class="form-group col-md-6">
                  <label for="due">{{ $t('Due') }}</label>
                  <input id="due" v-model="form.loan.due" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('due') }" name="amount" readonly />
                  <has-error :form="form" field="due" />
                </div>
              </div>
              <div class="row" v-if="form.loan">
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input :disabled="form.disabled" id="amount" v-model="form.amount" type="number" step="any"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }" name="amount"
                    :placeholder="$t('Enter an amount')" :max="form.loan.due" />
                  <has-error :form="form" field="amount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="interest">{{ $t('Interest') }} </label>
                  <input :disabled="form.disabled" id="interest" v-model="form.interest" type="number" step="any"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('interest') }" name="interest"
                    :placeholder="$t('Enter an interest rate in percentage')" min="0" />
                  <has-error :form="form" field="interest" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('Date') }}</label>
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
              <div class="form-group">
                <label for="note">{{ $t('Note') }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
              <div class="form-group">
                <label for="image">{{ $t('Image') }}</label>
                <div class="custom-file">
                  <input id="image" type="file" class="custom-file-input" name="image"
                    :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                  <label class="custom-file-label" for="image">{{
                    $t('Choose file')
                  }}</label>
                </div>
                <has-error :form="form" field="image" />
                <div class="bg-light mt-4 w-25">
                  <img v-if="url" :src="url" class="img-fluid" :alt="$t('Attached Image')" />
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
    return { title: this.$t('Create Loan Payment') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Loan Payment',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Loan Payments',
        url: 'loanPayments.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      referenceNo: '',
      loan: '',
      account: '',
      amount: '',
      interest: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      image: '',
      availableBalance: 0,
      maxAmount: 0,
      disabled: false,
    }),
    url: null,
    options: [],
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items', 'appInfo']),
  },
  created() {
    this.getLoans()
    this.getAccounts()
  },
  methods: {
    // get all loans
    async getLoans() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-loans',
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
      }
      this.updateBalance()
    },

    // update balance
    updateBalance() {
      this.form.availableBalance = 0
      if (this.form.account) {
        this.form.availableBalance = this.form.account.availableBalance
      }
      return
    },

    // update loan amount
    updateAmount() {
      this.form.disabled = false
      this.form.amount = 0
      this.form.interest = 0

      if (this.form.loan.loanType == 1) {
        this.form.amount = Number(this.form.loan.perMonth).toFixed(2)
        this.form.interest = Number(
          this.form.loan.interestAmount / this.form.loan.duration
        ).toFixed(2)
        this.form.disabled = true
      }
      return
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0]
      const reader = new FileReader()
      if (
        file.size < 2111775 &&
        (file.type === 'image/jpeg' ||
          file.type === 'image/png' ||
          file.type === 'image/gif')
      ) {
        reader.onloadend = (file) => {
          this.form.image = reader.result
        }
        reader.readAsDataURL(file)
        this.url = URL.createObjectURL(file)
      } else {
        Swal.fire(
          this.$t('Error!'),
          this.$t('Please select a valid thumbnail with size less than 2 MB'),
          'error'
        )
      }
    },

    // save loan payment
    async saveLoanPayment() {
      await this.form
        .post(window.location.origin + '/api/loan-payments')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Loan payment added successfully'),
          })
          this.$router.push({ name: 'loanPayments.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
