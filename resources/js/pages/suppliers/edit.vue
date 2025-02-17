<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Edit Supplier') }}</h3>
            <router-link :to="{ name: 'suppliers.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateSupplier" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="name">{{ $t('Name') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Enter a name')" />
                  <has-error :form="form" field="name" />
                </div>
                <div class="form-group col-md-6">
                  <label for="companyName">{{
                    $t('Company Name')
                  }}</label>
                  <input id="companyName" v-model="form.companyName" type="companyName" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('companyName') }" name="companyName"
                    :placeholder="$t('Enter a company name')" />
                  <has-error :form="form" field="companyName" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="phoneNumber">{{ $t('Contact Number') }}
                    <span class="required">*</span></label>
                  <input id="phoneNumber" v-model="form.phoneNumber" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('phoneNumber') }" name="phoneNumber"
                    :placeholder="$t('Enter a contact number')" />
                  <has-error :form="form" field="phoneNumber" />
                </div>
                <div class="form-group col-md-4">
                  <label for="email">{{ $t('Email') }}</label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                    :placeholder="$t('Enter your email address')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-4">
                  <label for="taxRegistrationNumber">{{
                    $t("Tax Registration Number")
                  }}</label>
                  <input id="taxRegistrationNumber" v-model="form.taxRegistrationNumber" type="taxRegistrationNumber" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('taxRegistrationNumber') }" name="taxRegistrationNumber"
                    :placeholder="$t('Enter a tax registration number')" />
                  <has-error :form="form" field="taxRegistrationNumber" />
                </div>
              </div>
              <div class="form-group">
                <label for="address">{{ $t('Address') }}</label>
                <textarea id="address" v-model="form.address" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('address') }" :placeholder="$t('Enter an address')" />
                <has-error :form="form" field="address" />
              </div>
              <div class="row">
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

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Supplier') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Supplier',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Suppliers',
        url: 'suppliers.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      name: '',
      email: '',
      phoneNumber: '',
      companyName: '',
      taxRegistrationNumber: '',
      address: '',
      image: '',
      status: 1,
    }),
    url: null,
    loading: true,
  }),
  mounted() {
    this.getSupplier()
  },
  methods: {
    // get supplier
    async getSupplier() {
      const { data } = await axios.get(
        window.location.origin + '/api/suppliers/' + this.$route.params.slug
      )
      this.form.name = data.data.name
      this.form.email = data.data.email
      this.form.phoneNumber = data.data.phoneNumber
      this.form.companyName = data.data.companyName
      this.form.taxRegistrationNumber = data.data.taxRegistrationNumber
      this.form.address = data.data.address
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

    // update supplier
    async updateSupplier() {
      await this.form
        .patch(
          window.location.origin + '/api/suppliers/' + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Supplier updated successfully'),
          })
          this.$router.push({ name: 'suppliers.index' })
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
