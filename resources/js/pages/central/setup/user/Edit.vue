<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-12 col-xl-3">
        <SettingsSidebar />
      </div>
      <div class="col-12 col-xl-9">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
          <div class="card">
            <div class="card-header setings-header">
              <div class="col-xl-4 col-4">
                <h3 class="card-title">
                  {{ $t("Edit role") }}
                </h3>
              </div>
              <div class="col-xl-8 col-8 float-right text-right">
                <router-link :to="{ name: 'user.index' }" class="btn btn-dark float-right">
                  <i class="fas fa-long-arrow-alt-left" />
                  {{ $t("Back") }}
                </router-link>
              </div>
            </div>
            <div class="card-body">
              <div class="row">

                <div class="form-group col-md-6">
                  <label for="name">{{ $t("Name") }} <span class="required">*</span></label>
                  <input v-model="form.name" id="name" name="name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" :placeholder="$t('Enter a name')" />
                  <has-error :form="form" field="name" />
                </div>

                <div class="form-group col-md-6">
                  <label for="role">Role <span class="required">*</span></label>
                  <select id="role" v-model="form.role" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('role') }">
                    <option v-for="(role, i) in roles" :value="role.slug" :key="i">{{ role.name }}</option>
                  </select>
                  <has-error :form="form" field="role" />
                </div>

                <div class="form-group col-md-12">
                  <label for="email">{{ $t("Email") }} <span class="required">*</span></label>
                  <input v-model="form.email" id="email" name="email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" :placeholder="$t('Enter your email address')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-6">
                  <label for="password">{{ $t("Password") }}</label>
                  <input v-model="form.password" id="password" name="password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password') }" placeholder="********" />
                  <has-error :form="form" field="password" />

                </div>

                <div class="form-group col-md-6">
                  <label for="cpassword">{{ $t("confirm_password") }}</label>
                  <input v-model="form.password_confirmation" id="cpassword" name="cpassword" type="password"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                    placeholder="********" />
                  <has-error :form="form" field="password_confirmation" />
                </div>

              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t('Save changes') }}
              </v-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import SettingsSidebar from '~/components/central/SettingsSidebar'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],

  metaInfo() {
    return { title: this.$t('Edit User') }
  },

  components: {
    SettingsSidebar,
  },

  data: () => ({
    breadcrumbsCurrent: 'Edit User',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Setup',
        url: 'setup.index',
      },
      {
        name: 'Users',
        url: 'user.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    roles: [],
    form: new Form({
      name: '',
      email: '',
      password: '123456',
      password_confirmation: '123456',
      role: 'super-admin',
      mail: true,
    })
  }),

  created() {
    this.getUser();
    this.getRole();
  },

  methods: {

    async update() {
      await this.form
        .patch(window.location.origin + '/api/user/' + this.$route.params.slug)
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Successfully updated')
          })
          this.$router.push({ name: 'user.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Error!'),
          })
        })
    },

    async getUser() {
      await axios.get(`/api/user/` + this.$route.params.slug).then((data) => {
        this.form.fill(data.data);
        this.form.role = data.data.roles[0].slug;
      })
    },

    async getRole() {
      await axios.get(`/api/get-role`).then((data) => {
        this.roles = data.data;
      })
    }
  },
}
</script>

<style lang="scss" scoped></style>
