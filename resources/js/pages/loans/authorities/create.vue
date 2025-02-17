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
              {{ $t('Create a loan authority') }}
            </h3>
            <router-link :to="{ name: 'authorities.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="saveAuthority" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="name">{{ $t('Name') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Enter a name')" />
                  <has-error :form="form" field="name" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="email">{{ $t('Email') }}</label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                    :placeholder="$t('Enter your email address')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-6">
                  <label for="contactNumber">{{ $t('Contact Number') }}
                    <span class="required">*</span></label>
                  <input id="contactNumber" v-model="form.contactNumber" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('contactNumber') }" name="contactNumber"
                    :placeholder="$t('Enter a contact number')" />
                  <has-error :form="form" field="contactNumber" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="ccLoanLimit">{{ $t('Cash Credit (CC) Loan Limit') }}
                    <span class="required">*</span></label>
                  <input id="ccLoanLimit" v-model="form.ccLoanLimit" type="number" step=".01" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('ccLoanLimit') }" name="ccLoanLimit"
                    :placeholder="$t('Enter cc loan limit')" />
                  <has-error :form="form" field="ccLoanLimit" />
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
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="address">{{ $t('Address') }}</label>
                  <textarea id="address" v-model="form.address" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('address') }" name="address"
                    :placeholder="$t('Enter an address')" />
                  <has-error :form="form" field="address" />
                </div>
                <div class="form-group col-md-6">
                  <label for="note">{{ $t('Note') }}</label>
                  <textarea id="note" v-model="form.note" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                  <has-error :form="form" field="note" />
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
export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Create Loan Authority') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Create Loan Authority',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Loan Authorities',
        url: 'authorities.index',
      },
      {
        name: 'Create',
        url: '',
      },
    ],
    form: new Form({
      name: '',
      email: '',
      contactNumber: '',
      ccLoanLimit: 10000000,
      address: '',
      note: '',
      status: 1,
    }),
    loading: true,
  }),
  methods: {
    // save category
    async saveAuthority() {
      await this.form
        .post(window.location.origin + '/api/loan-authorities')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Loan authority added successfully'),
          })
          this.$router.push({ name: 'authorities.index' })
        })
        .catch(() => {
          toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
        })
    },
  },
}
</script>
