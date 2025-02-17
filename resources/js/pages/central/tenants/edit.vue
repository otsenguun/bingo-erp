<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $t('Edit tenant') }}</h3>
            <router-link :to="{ name: 'tenants.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="save" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="name">
                    {{ $t('Name') }}
                    <span class="required">*</span>
                  </label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Enter a name')" />
                  <has-error :form="form" field="name" />
                </div>

                <div class="form-group col-md-6">
                  <label for="email">{{ $t('Company') }}</label>
                  <input id="company" v-model="form.company" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('company') }" name="company"
                    :placeholder="$t('Enter your company name')" />
                  <has-error :form="form" field="company" />
                </div>

                <!-- <div class="form-group col-md-6">
                  <label for="plan_id">
                    {{ $t('Plan') }}
                  </label>
                  <v-select
                    v-model="form.plan_id"
                    :options="plans"
                    :reduce="plan => plan.id"
                    :label="$t('name')"
                    :class="{ 'is-invalid': form.errors.has('plan_id') }"
                    id="plan_id"
                    :placeholder="$t('Plan')">
                  </v-select>
                  <has-error :form="form" field="plan_id"/>
                </div> -->

                <!--
                <div v-if="form.plan_id" class="form-group col-md-6">
                  <label for="subscription_status">
                    {{ $t('Status') }}
                    <span class="required">*</span>
                  </label>
                  <select
                    id="subscription_status"
                    v-model="form.subscription_status"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('subscription_status') }"
                    :required="form.plan_id"
                  >
                    <option :value="null">{{ $t('Select a status') }}</option>
                    <option value="1" :selected="form.plan_id == 1">{{ $t('Active') }}</option>
                    <option value="0" :selected="form.plan_id == 0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="subscription_status"/>
                </div>
                -->
              </div>
            </div>

            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t('Save changes') }}
              </v-button>
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
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Tenant') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Tenant',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Tenants',
        url: 'tenants.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      name: '',
      company: '',
    }),
    loading: true,
    url: null,
    plans: [],
  }),
  created() {
    this.getPlans()
    this.getTenant()
  },
  methods: {
    // get the tenant
    async getTenant() {
      const { data } = await axios.get(
        window.location.origin + '/api/tenants/' + this.$route.params.id
      )
      this.form.fill(data)
    },

    async getPlans() {
      const { data } = await axios.get(
        window.location.origin + "/api/plans"
      );
      this.plans = data.data
    },

    // update client
    async save() {
      await this.form
        .patch(
          window.location.origin + '/api/tenants/' + this.$route.params.id
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Successfully updated'),
          })
          this.$router.push({ name: 'tenants.index' })
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

<style lang="scss" scoped>

</style>
