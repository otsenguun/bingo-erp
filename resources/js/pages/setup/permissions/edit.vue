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
        <form role="form" @submit.prevent="updatePermission" @keydown="form.onKeydown($event)">
          <div class="card">
            <div class="card-header setings-header">
              <div class="col-xl-4 col-4">
                <h3 class="card-title">
                  {{ $t("Edit permission") }}
                </h3>
              </div>
              <div class="col-xl-8 col-8 float-right text-right">
                <router-link :to="{ name: 'permissions.index' }" class="btn btn-dark float-right">
                  <i class="fas fa-long-arrow-alt-left" />
                  {{ $t("Back") }}
                </router-link>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group col-md-12">
                <label for="name">{{ $t("Name") }}
                  <span class="required">*</span></label>
                <input id="name" v-model="form.name" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                  :placeholder="$t('Enter a name')" />
                <has-error :form="form" field="name" />
              </div>
              <div class="form-group col-md-12">
                <label for="guardName">{{ $t("Guard Name") }}
                  <span class="required">*</span></label>
                <input id="guardName" v-model="form.guardName" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('guardName') }" name="guardName" :placeholder="$t('Enter a guard name')
                    " />
                <has-error :form="form" field="guardName" />
              </div>
              <div class="form-group col-md-12">
                <label for="slug">{{ $t("Slug") }}
                  <span class="required">*</span></label>
                <input id="slug" v-model="form.slug" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('slug') }" name="slug"
                  :placeholder="$t('Enter a slug')" />
                <has-error :form="form" field="slug" />
              </div>
            </div>
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t("Save changes") }}
              </v-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Edit Permission") };
  },
  data: () => ({
    breadcrumbsCurrent: "Edit Permission",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Setup",
        url: "setup.index",
      },
      {
        name: "Permissions",
        url: "permissions.index",
      },
      {
        name: "Edit",
        url: "",
      },
    ],
    form: new Form({
      name: "",
      guardName: "",
      slug: "",
    }),
    loading: true,
  }),
  created() {
    this.getPermission();
  },
  methods: {
    // get permission
    async getPermission() {
      const { data } = await axios.get(
        window.location.origin + "/api/permissions/" + this.$route.params.slug
      );
      this.form.name = data.data.name;
      this.form.guardName = data.data.guardName;
      this.form.slug = data.data.slug;
    },

    // update permission
    async updatePermission() {
      await this.form
        .patch(
          window.location.origin + "/api/permissions/" + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Permission updated successfully"),
          });
          this.$router.push({ name: "permissions.index" });
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },
  },
};
</script>
