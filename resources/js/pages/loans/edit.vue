<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Edit loan') }}</h3>
            <router-link :to="{ name: 'loans.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateLoan" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row" v-if="items && accounts">
                <div class="form-group col-md-6">
                  <label for="authority">{{ $t('Loan Authority') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.authority" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('authority') }" name="authority"
                    :placeholder="$t('Select an authority')" />
                  <has-error :form="form" field="authority" />
                </div>
                <div class="form-group col-md-6">
                  <label for="account">{{ $t('Account') }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.account" :options="accounts" label="label"
                    :class="{ 'is-invalid': form.errors.has('account') }" name="account"
                    :placeholder="$t('Select an account')" disabled />
                  <has-error :form="form" field="account" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="reason">{{ $t('Loan Reason')
                  }}<span class="required">*</span></label>
                  <input id="reason" v-model="form.reason" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('reason') }" name="reason"
                    :placeholder="$t('Enter a reason')" />
                  <has-error :form="form" field="reason" />
                </div>
                <div class="form-group col-md-4">
                  <label for="referenceNo">{{ $t('Reference') }}
                    <span class="required">*</span></label>
                  <input id="referenceNo" v-model="form.referenceNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('referenceNo') }" name="referenceNo"
                    :placeholder="$t('Enter reference')" />
                  <has-error :form="form" field="referenceNo" />
                </div>
                <div class="form-group col-md-4">
                  <label for="loanType">{{
                    $t('Loan Type')
                  }}</label>
                  <select id="loanType" v-model="form.loanType" name="loanType" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('loanType') }" disabled>
                    <option value="1">
                      {{ $t('Term Loan') }}
                    </option>
                    <option value="0">
                      {{ $t('Cash Credit(CC) Loan') }}
                    </option>
                  </select>
                  <has-error :form="form" field="loanType" />
                </div>
              </div>

              <div class="row" v-if="form.authority && form.loanType == 0">
                <div class="form-group col-md-6">
                  <label for="ccLoanLimit">{{ $t('CC Limit') }}
                    <span class="required">*</span></label>
                  <input id="ccLoanLimit" v-model="form.authority.ccLimit" type="text" class="form-control"
                    name="ccLoanLimit" readonly />
                </div>
                <div class="form-group col-md-6">
                  <label for="availableAmount">{{ $t('Available Amount') }}
                    <span class="required">*</span></label>
                  <input id="availableAmount" v-model="form.authority.availableCCLoan" type="text" class="form-control"
                    name="availableAmount" readonly />
                </div>
              </div>

              <div v-if="form.loanType == 0" class="row">
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amount" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount"
                    :placeholder="$t('Enter an amount')" :min="form.due" :max="form.loanType == 0
                      ? form.authority.availableCCLoan + form.rowPayableAMount
                      : ''
                      " @change="generatePayable" @keyup="generatePayable" />
                  <has-error :form="form" field="amount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('Date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                  <has-error :form="form" field="date" />
                </div>
              </div>
              <div v-else class="row">
                <div class="form-group col-md-6">
                  <label for="amount">{{ $t('Amount') }}
                    <span class="required">*</span></label>
                  <input id="amount" v-model="form.amount" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('amount') }" name="amount"
                    :placeholder="$t('Enter an amount')" :min="form.due - form.interestAmount" :max="form.loanType == 0 ? form.authority.availableAmount : ''
                      " @change="generatePayable" @keyup="generatePayable" />
                  <has-error :form="form" field="amount" />
                </div>
                <div class="form-group col-md-6">
                  <label for="interest">{{ $t('Interest') }} (%)</label>
                  <input id="interest" v-model="form.interest" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('interest') }" min="0" max="100" name="interest"
                    :placeholder="$t('Enter an interest rate in percentage')" @change="generatePayable"
                    @keyup="generatePayable" />
                </div>
                <div class="form-group col-md-4">
                  <label for="paymentType">{{
                    $t('Payment Type')
                  }}</label>
                  <select id="paymentType" v-model="form.paymentType" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('paymentType') }" @change="generatePayable">
                    <option value="0">{{ $t('Daily') }}</option>
                    <option value="1">{{ $t('Monthly') }}</option>
                    <option value="2">{{ $t('Yearly') }}</option>
                  </select>
                  <has-error :form="form" field="paymentType" />
                </div>
                <div class="form-group col-md-4">
                  <label for="duration">{{ $t('Duration') }}
                    <span class="required">*</span></label>
                  <input id="duration" v-model="form.duration" type="number" class="form-control" step="1"
                    :class="{ 'is-invalid': form.errors.has('duration') }" name="duration"
                    :placeholder="$t('Enter a duration')" min="1" max="255" @change="generatePayable"
                    @keyup="generatePayable" />
                  <has-error :form="form" field="duration" />
                </div>
                <div class="form-group col-md-4">
                  <label for="payReturn">
                    {{ $t('Per') }}
                    <span v-if="form.paymentType == 0">{{
                      $t('Day')
                    }}</span>
                    <span v-else-if="form.paymentType == 1">{{
                      $t('Month')
                    }}</span>
                    <span v-else>{{ $t('Year') }}</span>
                  </label>
                  <input id="payReturn" v-model="form.payReturn" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('payReturn') }" name="payReturn" readonly />
                  <has-error :form="form" field="payReturn" />
                </div>
                <div class="form-group col-md-6">
                  <label for="payableAmount">{{
                    $t('Payable Amount')
                  }}</label>
                  <input id="payableAmount" v-model.lazy="form.payableAmount" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('payableAmount') }" name="payableAmount" readonly />
                </div>
                <div class="form-group col-md-6">
                  <label for="date">{{ $t('Date') }}</label>
                  <input id="date" v-model="form.date" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date') }" name="date" />
                  <has-error :form="form" field="date" />
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
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
                <div class="form-group col-md-6">
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
    return { title: this.$t('Edit Loan') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Loan',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Loans',
        url: 'loans.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      reason: '',
      authority: '',
      referenceNo: '',
      account: '',
      loanType: 1,
      amount: '',
      interest: '',
      payableAmount: '',
      rowPayableAMount: 0,
      interestAmount: 0,
      paymentType: 1,
      duration: '',
      payReturn: '',
      due: '',
      date: new Date().toISOString().slice(0, 10),
      note: '',
      status: 1,
      image: '',
    }),
    url: null,
    options: [],
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getAuthorities()
    this.getAccounts()
    this.getLoan()
  },
  methods: {
    // get all expense categories
    async getAuthorities() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-loan-authorities',
      })
    },

    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // get loan
    async getLoan() {
      const { data } = await axios.get(
        window.location.origin + '/api/loans/' + this.$route.params.slug
      )
      this.form.reason = data.data.reason
      this.form.referenceNo = data.data.reference
      this.form.authority = data.data.authority
      this.form.account = data.data.account
      this.form.amount = data.data.transaction.amount
      this.form.loanType = data.data.loanType
      this.form.interest = data.data.interestRate
      this.form.paymentType = data.data.paymentType
      this.form.duration = data.data.duration
      this.form.payReturn = data.data.perMonth
      this.form.payableAmount = Number(data.data.payable)
      this.form.rowPayableAMount = Number(data.data.payable)
      this.form.due = Number(data.data.due)
      this.form.interestAmount = Number(data.data.interestAmount)
      this.form.date = data.data.date
      this.form.note = data.data.note
      this.form.status = data.data.status
      this.url = data.data.image
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
        reader.onloadend = () => {
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
    // generate payable amount
    generatePayable() {
      let amount = Number(this.form.amount)
      let interest = Number(this.form.interest)
      let duration = Number(this.form.duration)
      this.form.payableAmount = 0
      if (this.form.loanType == 0) {
        this.form.rowPayableAMount = amount + interest
        return (this.form.payableAmount =
          amount + ' + ' + interest + ' = ' + this.form.rowPayableAMount)
      } else {
        let totalInterestAmount = 0
        let monthlyPayment = 0
        let interestRate = interest / 100
        let numOfYears = 0
        if (interest && duration) {
          if (this.form.paymentType == 0) {
            numOfYears = duration / 365
          } else if (this.form.paymentType == 1) {
            numOfYears = duration / 12
          } else {
            numOfYears = duration
          }
          monthlyPayment = Number(
            (
              ((interestRate / 12) * amount) /
              (1 - Math.pow(1 + interestRate / 12, numOfYears * -12))
            ).toFixed(2)
          )
          totalInterestAmount = Number(
            (monthlyPayment * (numOfYears * 12) - amount).toFixed(2)
          )
          this.form.rowPayableAMount = Number(
            (amount + totalInterestAmount).toFixed(2)
          )
          this.form.payableAmount =
            amount +
            ' + ' +
            totalInterestAmount +
            ' = ' +
            this.form.rowPayableAMount
          return (this.form.payReturn = Number(
            (this.form.rowPayableAMount / duration).toFixed(2)
          ))
        }
      }
    },
    // update loan
    async updateLoan() {
      await this.form
        .patch(window.location.origin + '/api/loans/' + this.$route.params.slug)
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Loan updated successfully'),
          })
          this.$router.push({ name: 'loans.index' })
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
