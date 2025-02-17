<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row justify-content-center">
      <div v-if="$can('database-backup')" class="col-lg-6">
        <div class="card custom-card">
          <div class="card-header text-center settings-header">
            <h3 class="card-title">{{ $t("Update Application") }}</h3>
          </div>
          <div class="card-body text-center">
            <p
              v-if="updatedVersion == appInfo.version"
              class="bg-green rounded py-3"
            >
              {{ $t("Your application is up to date") }}
            </p>
            <button
              type="button"
              class="btn btn-primary btn-lg"
              @click="updateApplication"
              :disabled="isUpdating || updatedVersion == appInfo.version"
            >
              {{ $t("Check for Update") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import axios from "axios";
export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Update Application") };
  },
  data: () => ({
    breadcrumbsCurrent: "Update Application",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Update Application",
        url: "",
      },
    ],
    updatedVersion: null,
    isUpdating: false,
  }),

  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },

  created() {
    this.getUpdatedVersionInfo();
  },
  methods: {
    async getUpdatedVersionInfo() {
      axios
        .get("/api/get-updated-version")
        .then((response) => {
          this.updatedVersion = response.data.data;
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },

    updateApplication() {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t(
          "We highly recommend to check the update documentation carefully before do it"
        ),
        type: "info",
        showCancelButton: true,
        confirmButtonText: this.$t("Yes"),
      }).then((result) => {
        if (result.value) {
          this.isUpdating = true;
          axios
            .post("/api/update-application")
            .then((response) => {
              toast.fire({
                type: response.data.status,
                title: response.data.message,
              });
              window.location.reload();
            })
            .catch(() => {
              toast.fire({
                type: "error",
                title: this.$t("Opps...something went wrong"),
              });
              this.isUpdating = false;
            });
        }
      });
    },
  },
};
</script>
