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
              {{ $t('Send Notification') }}
            </h3>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="save" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="email">
                    {{ $t('Email') }}
                  </label>
                  <input id="email" v-model="email" type="text" class="form-control" name="email"
                    :placeholder="$t('Email')" disabled />
                </div>

                <div class="form-group col-md-12">
                  <label for="subject">{{ $t('Subject') }}
                    <span class="required">*</span></label>
                  <input v-model="form.subject" id="subject" name="subject" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('subject') }"
                    :placeholder="$t('Enter subject')" />
                  <has-error :form="form" field="subject" />
                </div>

                <div class="form-group col-md-12">
                  <label for="greeting">{{ $t('Greeting') }} <span class="required">*</span>
                  </label>
                  <input v-model="form.greeting" id="greeting" name="greeting" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('greeting') }"
                    :placeholder="$t('Enter greeting')" />
                  <has-error :form="form" field="greeting" />
                </div>

                <div class="form-group col-md-12">
                  <label for="body">{{ $t('Body') }}<span class="required">*</span></label>
                  <Editor ref="toastuiEditor" :initialValue="form.body" id="body" name="body" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('body') }" height="500px" initialEditType="markdown"
                    @change="updateBody" />
                  <has-error :form="form" field="body" />
                </div>

              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-save" /> {{ $t('Send') }}
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
// vue page transition
import '@toast-ui/editor/dist/toastui-editor.css'
import Form from 'vform'
import { Editor } from '@toast-ui/vue-editor'
import axios from "axios";

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Send Notification') }
  },
  components: {
    Editor
  },
  created() {
    this.getTenant()
  },
  data: () => ({
    breadcrumbsCurrent: 'Send Notification',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Send Notification',
        url: '',
      },
    ],
    url: null,
    email: '',
    form: new Form({
      subject: '',
      greeting: '',
      body: '',
    }),
  }),
  methods: {
    // get the tenant
    async getTenant() {
      const { data } = await axios.get(
        window.location.origin + '/api/tenants/' + this.$route.params.id
      )

      this.email = data.email
    },

    updateBody() {
      this.form.body = this.$refs.toastuiEditor.invoke('getMarkdown')
    },

    resetForm() {
      this.form.reset()
    },

    async save() {
      await this.form
        .post(window.location.origin + '/api/send-notification/' + this.$route.params.id)
        .then((response) => {
          toast.fire({
            type: 'success',
            title: this.$t('Notification sent successfully'),
          })

          if (response.status === 200) {
            this.resetForm()
            this.$refs.toastuiEditor.invoke('setMarkdown', '')
          }
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

<style lang="scss" scoped>

</style>
