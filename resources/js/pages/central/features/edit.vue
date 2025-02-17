<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-8 m-auto">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Feature Edit') }}
            </h3>
            <router-link :to="{ name: 'features.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->

          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="updatePlan" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="name">{{ $t('Name') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Name')" />
                  <has-error :form="form" field="name" />
                </div>
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
import { mapGetters } from 'vuex'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Feature') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Features',
        url: 'features.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    url: null,
    form: new Form({
      name: '',
    }),
    plans: [],
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getPlan()
  },
  methods: {
    // get plan
    async getPlan() {
      const { data } = await axios.get(
        window.location.origin + '/api/features/' + this.$route.params.id
      )
      this.form.name = data.data.name
    },

    // update plan
    async updatePlan() {
      await this.form
        .patch(
          window.location.origin + '/api/features/' + this.$route.params.id
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Successfully updated'),
          })
          this.$router.push({ name: 'features.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Error!'),
          })
        })
    },
  },
}
</script>

<style lang="scss" scoped></style>
