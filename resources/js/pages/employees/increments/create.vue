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
              {{ $t('Create salary increment') }}
            </h3>
            <router-link :to="{ name: 'increments.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveIncrement" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="reason">{{ $t('Increment Reason')
                  }}<span class="required">*</span></label>
                  <input id="reason" v-model="form.reason" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('reason') }" name="reason" :placeholder="$t('Enter a reason')
                      " />
                  <has-error :form="form" field="reason" />
                </div>
                <div class="form-group col-md-6">
                  <label for="employee">{{ $t('Employee')
                  }}<span class="required">*</span></label>
                  <v-select v-if="items" v-model="form.employee" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('employee') }" name="employee"
                    :placeholder="$t('Select an employee')" />
                  <has-error :form="form" field="employee" />
                </div>
              </div>
              <div class="row" v-if="form.employee">
                <div class="form-group col-md-6">
                  <label for="presentSalary">{{ $t('Present Salary')
                  }}<span class="required">*</span></label>
                  <input id="presentSalary" v-model="form.employee.totalSalary" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('presentSalary') }" name="presentSalary" readonly />
                  <has-error :form="form" field="presentSalary" />
                </div>
                <div class="form-group col-md-6">
                  <label for="incrementAmount">{{ $t('Increment Amount') }}
                    <span class="required">*</span></label>
                  <input id="incrementAmount" v-model="form.incrementAmount" type="number" step="any" class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('incrementAmount'),
                    }" name="incrementAmount" :placeholder="$t('Enter increment amount')"
                    min="0" :max="form.employee.totalSalary" />
                  <has-error :form="form" field="incrementAmount" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="incrementDate">{{
                    $t('Increment Date')
                  }}</label>
                  <input id="incrementDate" v-model="form.incrementDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('incrementDate') }" name="incrementDate" />
                  <has-error :form="form" field="incrementDate" />
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
import { mapGetters } from 'vuex'
import Form from 'vform'
export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Create Salary Increment') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Salary Increment',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Increments',
        url: 'increments.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    url: null,
    form: new Form({
      reason: '',
      employee: '',
      presentSalary: '',
      incrementAmount: '',
      incrementDate: new Date().toISOString().slice(0, 10),
      status: 1,
      note: '',
    }),
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getEmployees()
  },
  methods: {
    // get all employees
    async getEmployees() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-employees',
      })
    },

    // save increment
    async saveIncrement() {
      await this.form
        .post(window.location.origin + '/api/increments')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Increment added successfully'),
          })
          this.$router.push({ name: 'increments.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
