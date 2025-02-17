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
              {{ $t('Edit page') }}
            </h3>
            <router-link :to="{ name: 'pages.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->

          <!-- form start -->
          <form role="form" enctype="multipart/form-data" @submit.prevent="updateData" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-12">
                  <label for="name">{{ $t('Pages') }}
                    <span class="required">*</span></label>
                  <input id="name" v-model="form.name" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                    :placeholder="$t('Name')" />
                  <has-error :form="form" field="name" />
                </div>

                <div class="form-group col-md-12">
                  <label for="type">{{ $t('Type') }}</label>
                  <select id="type" v-model="form.type" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="0">{{ $t('Information') }}</option>
                    <option value="1">{{ $t('Need Help') }}</option>
                  </select>
                  <has-error :form="form" field="type" />
                </div>

                <div class="form-group col-md-12">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>

                <div class="form-group col-md-12">
                  <label for="body">{{ $t('Content') }}<span class="required">*</span></label>
                  <Editor ref="toastuiEditor" :initialValue="form.content" id="content" name="content"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('content') }" height="500px"
                    initialEditType="html" @change="updateContent" />
                  <has-error :form="form" field="content" />
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
import { Editor } from '@toast-ui/vue-editor'
import '@toast-ui/editor/dist/toastui-editor.css'


export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Page') }
  },
  components: {
    Editor,
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Page',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Pages',
        url: 'pages.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    url: null,
    form: new Form({
      name: '',
      type: 0,
      content: '',
      status: 1,
    }),
    loading: true,
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getData()
  },
  methods: {
    updateContent() {
      this.form.content = this.$refs.toastuiEditor.invoke('getHTML')
    },
    // get data
    async getData() {
      const { data } = await axios.get(
        window.location.origin + '/api/pages/' + this.$route.params.id
      )
      this.form.fill(data.data)
      this.$refs.toastuiEditor.invoke('setHTML', data.data.content)
    },
    // update
    async updateData() {
      await this.form
        .patch(
          window.location.origin + '/api/pages/' + this.$route.params.id
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Page updated successfully'),
          })
          this.$router.push({ name: 'pages.index' })
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
