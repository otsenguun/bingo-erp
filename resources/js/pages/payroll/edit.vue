<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 v-if="form.employee" class="card-title">
              {{ $t('Edit Payroll') }}
            </h3>
            <router-link :to="{ name: 'payroll.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updatePayroll" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div v-if="items" class="form-group col-md-6">
                  <label for="employee">{{ $t('Employee')
                  }}<span class="required">*</span></label>
                  <v-select v-if="items" v-model="form.employee" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('employee') }" name="employee"
                    :placeholder="$t('Select an employee')" @input="calculateTotalSalary" />
                  <has-error :form="form" field="employee" />
                </div>
                <div class="form-group col-md-6">
                  <label for="salaryMonth">{{ $t('Salary Month')
                  }}<span class="required">*</span></label>
                  <select id="salaryMonth" v-model="form.salaryMonth" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('salaryMonth') }" name="salaryMonth">
                    <option value="" selected disabled>
                      {{ $t('Select a salary month') }}
                    </option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                  </select>
                  <has-error :form="form" field="salaryMonth" />
                </div>
              </div>
              <div v-if="form.employee" class="row">
                <div class="form-group col-md-4">
                  <label for="presentSalary">{{
                    $t('Present Salary')
                  }}</label>
                  <input id="presentSalary" v-model="form.employee.totalSalary" type="number" step="any"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('presentSalary') }" name="presentSalary"
                    readonly />
                </div>
                <div class="form-group col-md-4">
                  <label for="deductionAmount">{{
                    $t('Deduction Amount')
                  }}</label>
                  <input id="deductionAmount" v-model="form.deductionAmount" type="number" step="any" class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('deductionAmount'),
                    }" name="deductionAmount" min="0" :placeholder="$t('Enter a deduction amount')"
                    @change="calculateTotalSalary" @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="deductionAmount" />
                </div>
                <div class="form-group col-md-4">
                  <label for="deductionReason">{{
                    $t('Deduction Reason')
                  }}</label>
                  <input id="deductionReason" v-model="form.deductionReason" type="text" class="form-control" :class="{
                    'is-invalid': form.errors.has('deductionReason'),
                  }" name="deductionReason" :placeholder="$t('Enter a deduction reason')" />
                  <has-error :form="form" field="deductionReason" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="mobileBill">{{
                    $t('Mobile Bill')
                  }}</label>
                  <input id="mobileBill" v-model="form.mobileBill" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mobileBill') }" name="mobileBill" min="0"
                    :placeholder="$t('Enter a mobile bill')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="mobileBill" />
                </div>
                <div class="form-group col-md-3">
                  <label for="foodBill">{{
                    $t('Food Bill')
                  }}</label>
                  <input id="foodBill" v-model="form.foodBill" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('foodBill') }" name="foodBill"
                    :placeholder="$t('Enter a food bill')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="foodBill" />
                </div>
                <div class="form-group col-md-3">
                  <label for="bonus">{{ $t('Bonus') }}</label>
                  <input id="bonus" v-model="form.bonus" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('bonus') }" name="bonus" min="0"
                    :placeholder="$t('Enter a bonus')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="bonus" />
                </div>
                <div class="form-group col-md-3">
                  <label for="commission">{{
                    $t('Commission')
                  }}</label>
                  <input id="commission" v-model="form.commission" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('commission') }" name="commission"
                    :placeholder="$t('Enter a commission')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="commission" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="festivalBonus">{{
                    $t('Festival Bonus')
                  }}</label>
                  <input id="festivalBonus" v-model="form.festivalBonus" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('festivalBonus') }" name="festivalBonus" min="0" :placeholder="$t('Enter a festival bonus')
                        " @change="calculateTotalSalary" @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="festivalBonus" />
                </div>
                <div class="form-group col-md-3">
                  <label for="travelAllowance">{{
                    $t('Travel Allowance(TA)')
                  }}</label>
                  <input id="travelAllowance" v-model="form.travelAllowance" type="number" step="any" class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('travelAllowance'),
                    }" name="travelAllowance" :placeholder="$t('Enter a travel allowance')"
                    @change="calculateTotalSalary" @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="travelAllowance" />
                </div>
                <div class="form-group col-md-3">
                  <label for="others">{{ $t('Others') }}</label>
                  <input id="others" v-model="form.others" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('others') }" name="others" min="0"
                    :placeholder="$t('Enter others amount')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="others" />
                </div>
                <div class="form-group col-md-3">
                  <label for="advance">{{
                    $t('Advance')
                  }}</label>
                  <input id="advance" v-model="form.advance" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('advance') }" name="advance"
                    :placeholder="$t('Advance')" @change="calculateTotalSalary"
                    @keyup="calculateTotalSalary" />
                  <has-error :form="form" field="advance" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="totalSalary">{{
                    $t('Total Salary')
                  }}</label>
                  <input id="totalSalary" v-model="form.totalSalary" type="number" step="any" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('totalSalary') }" name="totalSalary" readonly />
                  <has-error :form="form" field="totalSalary" />
                </div>
                <div v-if="accounts" class="form-group col-md-4">
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
                <div class="form-group col-md-4">
                  <label for="availableBalance">{{
                    $t('Available Balance')
                  }}</label>
                  <input id="availableBalance" v-model="form.account.availableBalance" type="number" step="any"
                    class="form-control" :class="{
                      'is-invalid': form.errors.has('availableBalance'),
                    }" name="availableBalance" readonly />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="chequeNo">{{ $t('Cheque No') }}</label>
                  <input id="chequeNo" v-model="form.chequeNo" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('chequeNo') }" name="chequeNo"
                    :placeholder="$t('Enter a cheque number')" />
                  <has-error :form="form" field="chequeNo" />
                </div>
                <div class="form-group col-md-4">
                  <label for="salaryDate">{{
                    $t('Salary Date')
                  }}</label>
                  <input id="salaryDate" v-model="form.salaryDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('salaryDate') }" name="salaryDate" />
                  <has-error :form="form" field="salaryDate" />
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
    return { title: this.$t('Edit Payroll') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Payroll',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Payroll',
        url: 'payroll.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      chequeNo: '',
      salaryMonth: '',
      employee: '',
      account: '',
      presentSalary: '',
      deductionAmount: '',
      deductionReason: '',
      mobileBill: '',
      foodBill: '',
      bonus: '',
      commission: '',
      advance: '',
      festivalBonus: '',
      travelAllowance: '',
      others: '',
      totalSalary: '',
      salaryDate: '',
      status: 1,
      note: '',
      image: '',
    }),
    url: null,
    accounts: '',
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getEmployees()
    this.getAccounts()
    this.getPayroll()
  },
  methods: {
    // get all employees
    async getEmployees() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-employees',
      })
    },
    // get accounts
    async getAccounts() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-accounts'
      )
      this.accounts = data.data
    },

    // get payroll
    async getPayroll() {
      const { data } = await axios.get(
        window.location.origin + '/api/payroll/' + this.$route.params.slug
      )
      this.form.chequeNo = data.data.chequeNo
      this.form.salaryMonth = data.data.salaryMonth
      this.form.employee = data.data.employee
      this.form.account = data.data.account
      this.form.availableBalance = data.data.account.availableBalance
      this.form.presentSalary = data.data.employee.totalSalary
      this.form.deductionAmount = data.data.deductionAmount
      this.form.deductionReason = data.data.deductionReason
      this.form.mobileBill = data.data.mobileBill
      this.form.foodBill = data.data.foodBill
      this.form.bonus = data.data.bonus
      this.form.commission = data.data.commission
      this.form.advance = data.data.advance
      this.form.festivalBonus = data.data.festivalBonus
      this.form.travelAllowance = data.data.travelAllowance
      this.form.others = data.data.others
      this.form.totalSalary = data.data.transaction.amount
      this.form.salaryDate = data.data.salaryDate
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

    // calculate total
    calculateTotalSalary() {
      let salary = Number(this.form.employee.totalSalary)
      let deduction = Number(this.form.deductionAmount)
      let mobileBill = Number(this.form.mobileBill)
      let foodBill = Number(this.form.foodBill)
      let bonus = Number(this.form.bonus)
      let commission = Number(this.form.commission)
      let advance = Number(this.form.advance)
      let festivalBonus = Number(this.form.festivalBonus)
      let travelAllowance = Number(this.form.travelAllowance)
      let others = Number(this.form.others)
      this.form.totalSalary =
        mobileBill +
        foodBill +
        bonus +
        commission +
        festivalBonus +
        travelAllowance +
        others +
        advance -
        deduction
      if (salary > 0) {
        return (this.form.totalSalary = salary + this.form.totalSalary)
      }
      return this.form.totalSalary
    },

    updateBalance() {
      this.form.availableBalance = 0
      if (this.form.account) {
        this.form.availableBalance = this.form.account.availableBalance
      }
    },

    // update payroll
    async updatePayroll() {
      await this.form
        .patch(
          window.location.origin + '/api/payroll/' + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Payroll updated successfully'),
          })
          this.$router.push({ name: 'payroll.index' })
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

<style lang="scss" scoped></style>
