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
              {{ $t('Send mail') }}
            </h3>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="save" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="subject">
                    {{ $t('Subject') }}
                    <span class="required">*</span>
                  </label>
                  <input v-model="form.subject" id="subject" name="subject" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('subject') }"
                    :placeholder="$t('Enter subject')" />
                  <has-error :form="form" field="subject" />
                </div>

                <div class="form-group col-md-12">
                  <label for="sentTo">
                    {{ $t('Sent To') }}
                    <span class="required">*</span>
                  </label>
                  <select v-model="form.sent_to" class="form-control" id="sentTo" name="sent_to">
                    <option value="all" selected>
                      {{ $t('All') }}
                    </option>
                    <option value="tenants">
                      {{ $t('Tenants') }}
                    </option>
                    <option value="subscribers">
                      {{ $t('Subscribers') }}
                    </option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <label for="greeting">
                    {{ $t('Greeting') }}
                    <span class="required">*</span>
                  </label>
                  <input v-model="form.greeting" id="greeting" name="greeting" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('greeting') }"
                    :placeholder="$t('Enter greeting')" />
                  <has-error :form="form" field="greeting" />
                </div>

                <div class="form-group col-md-12">
                  <label for="body">
                    {{ $t('Body') }}
                    <span class="required">*</span>
                  </label>
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
import '@toast-ui/editor/dist/toastui-editor.css'
import Form from 'vform'
import { Editor } from '@toast-ui/vue-editor'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Send mail') }
  },
  components: {
    Editor
  },
  data: () => ({
    breadcrumbsCurrent: 'Send mail',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Send mail',
        url: '',
      },
    ],
    url: null,
    form: new Form({
      subject: '',
      sent_to: 'all',
      greeting: '',
      body: '',
    }),
  }),
  methods: {
    updateBody() {
      this.form.body = this.$refs.toastuiEditor.invoke('getMarkdown')
    },

    async save() {
      await this.form
        .post(window.location.origin + '/api/newsletters')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Mail sent successfully'),
          })
          this.form.reset()
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
