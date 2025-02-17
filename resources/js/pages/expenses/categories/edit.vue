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
              {{ $t('Edit category') }}
            </h3>
            <router-link :to="{ name: 'expenseCats.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateCategory" @keydown="form.onKeydown($event)">
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
                <div class="form-group col-md-12">
                  <label for="note">{{ $t('Note') }}</label>
                  <vue-editor  id="editor" useCustomImageHandler @image-added="handleImageAdded" v-model="form.note" :editorToolbar="customToolbar"></vue-editor>
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
import { VueEditor } from "vue2-editor";

export default {
  components: {
    VueEditor
  },

  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Expense Category') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Expense Category',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Categories',
        url: 'expenseCats.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      name: '',
      note: '',
      status: 1,
    }),
    loading: true,
    customToolbar: [
    [{
      header: [false, 1, 2, 3, 4, 5, 6]}],
      ["bold", "italic", "underline", "strike"],
      [{align: ""}, {align: "center"}, {align: "right"}, {align: "justify"}],
      ["blockquote", "code-block"],
      [{list: "ordered"}, {list: "bullet"}],
      [{color: []}, {background: []}],
      ["link", "image",]
    ],
  }),

  mounted() {
    this.getCategory()
  },
  methods: {
    // get category
    async getCategory() {
      const { data } = await axios.get(
        window.location.origin +
        '/api/expense-categories/' +
        this.$route.params.slug
      )
      this.form.name = data.data.name
      this.form.note = data.data.note
      this.form.status = data.data.status
    },
    // update category
    async updateCategory() {
      await this.form
        .patch(
          window.location.origin +
          '/api/expense-categories/' +
          this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Category updated successfully'),
          })
          this.$router.push({ name: 'expenseCats.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Opps...something went wrong'),
          })
        })
    },

      // editor image upload
      handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {
      var formData = new FormData();
      formData.append("image", file);
      formData.append("target_dir", "expenseCategories");

      axios({
        url:  window.location.origin + "/api/rich-editor-file-upload",
        method: "POST",
        data: formData
      })
        .then(result => {
          const url = result.data.url; // Get url from response
          console.log(url);
          Editor.insertEmbed(cursorLocation, "image", url);
          resetUploader();
        })
        .catch(err => {
          console.log(err);
        });
    }


  },
}
</script>
