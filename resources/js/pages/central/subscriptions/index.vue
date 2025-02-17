<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("All Subscriptions") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                  <i class="fas fa-sync"></i>
                </a>
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body position-relative">
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t('ID') }}</th>
                    <th>{{ $t('Tenant') }}</th>
                    <th>{{ $t('Plan') }}</th>
                    <th>{{ $t('Quantity') }}</th>
                    <th>{{ $t('Approved By') }}</th>
                    <th>{{ $t('Created At') }}</th>
                    <th>{{ $t('Ends At') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(subscription, i) in items" :key="i">
                    <td>{{ subscription.id }}</td>
                    <td v-if="subscription.tenant">
                      <a :href="subscription.tenant.domain_url">
                        {{ subscription.tenant.name }}
                      </a>
                    </td>
                    <td>{{ subscription.plan?.name }}</td>
                    <td>{{ subscription.quantity }}</td>
                    <td>
                      <span v-tooltip="subscription.approved_by?.email">
                        {{ subscription.approved_by?.name }}
                      </span>
                    </td>
                    <td>{{ subscription.created_at | moment('Do MMM, YYYY') }}</td>
                    <td>{{ subscription.ends_at | moment('Do MMM, YYYY') }}</td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="12">
                      <EmptyTable />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer">
            <div class="dtable-footer">
              <div class="form-group row display-per-page">
                <label>{{ $t('per_page') }} </label>
                <div>
                  <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
              </div>
              <!-- pagination-start -->
              <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
                class="justify-flex-end" @paginate="paginate" />
              <!-- pagination-end -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  layout: "central",
  middleware: ["auth"],
  metaInfo() {
    return { title: this.$t("All Subscriptions") };
  },
  data: () => ({
    STATUS_PENDING: 0,
    STATUS_ACCEPTED: 1,
    STATUS_REJECTED: 2,

    breadcrumbsCurrent: "All Subscriptions",
    breadcrumbs: [
      {
        name: "Subscriptions",
        url: "home",
      },
      {
        name: "All Subscriptions",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", [
      "items",
      "loading",
      "pagination",
      "appInfo",
      "tenant",
    ]),
  },
  created() {
    this.getData();
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/subscriptions?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // Pagination
    async paginate() {
      this.query === '' ? await this.getData() : await this.searchData()
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1
    },

    // Reload after search
    async reload() {
      this.query = ''
    },

    // print table
    async print() {
      await this.$htmlToPaper('printMe')
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },

    async update(slug, status) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("You will not be able to return to this!"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$axios
            .patch("/api/subscriptions/" + slug, {
              status: status,
            })
            .then(() => {
              Swal.fire(
                this.$t("Updated successfully!"),
                this.$t("Updated successfully."),
                "success"
              );
              this.getData();
            })
            .catch((e) => {
              Swal.fire(
                this.$t("Failed!"),
                this.$t("There was something wrong."),
                "warning"
              );

              console.log(e)
            });
        }
      });
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("subscriptions.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/subscriptions/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                this.getData();
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("subscriptions.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },
  },
};
</script>
