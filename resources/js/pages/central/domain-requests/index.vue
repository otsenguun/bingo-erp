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
                {{ $t("Domain Requests") }}
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
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Requested Domain") }}</th>
                    <th>{{ $t("Tenant Name") }}</th>
                    <th>{{ $t("Tenant Email") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th class="text-right no-print">
                      {{ $t("Action") }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                    <td>
                      <span v-if="pagination && pagination.current_page > 1">
                        {{
                          pagination.per_page * (pagination.current_page - 1) +
                          (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>{{ data.requested_domain }}</td>
                    <td>{{ data.tenant && data.tenant.name }}</td>
                    <td>{{ data.tenant && data.tenant.email }}</td>
                    <td v-html="data.status_html"></td>
                    <td class="text-right no-print">
                      <div v-if="data.id" class="btn-group">
                        <div class="dropdown show">
                          <a class="btn btn-secondary dropdown-toggle" href="!#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $t("Action") }}
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <button :disabled="data.status == 0" v-tooltip="$t('Pending')" href="#"
                              class="btn btn-info btn-sm dropdown-item" @click.prevent="update(data.id, 0)">
                              <i class="fas fa-clock" />
                              {{ $t("Pending") }}
                            </button>

                            <button :disabled="data.status == 1" v-tooltip="$t('Connected')" href="#"
                              class="btn btn-success btn-sm dropdown-item" @click.prevent="update(data.id, 1)">
                              <i class="fas fa-link" />
                              {{ $t("Connected") }}
                            </button>

                            <button :disabled="data.status == 2" v-tooltip="$t('Rejected')" href="#"
                              class="btn btn-danger btn-sm dropdown-item" @click.prevent="update(data.id, 2)">
                              <i class="fas fa-times" />
                              {{ $t("Rejected") }}
                            </button>

                            <button :disabled="data.status == 3" v-tooltip="$t('Removed')" href="#"
                              class="btn btn-warning btn-sm dropdown-item" @click.prevent="update(data.id, 3)">
                              <i class="fas fa-minus-circle" />
                              {{ $t("Removed") }}
                            </button>

                            <button :disabled="data.status != 0" v-tooltip="$t('Delete')" href="#"
                              class="btn btn-danger btn-sm dropdown-item" @click.prevent="deleteData(data.id)">
                              <i class="fas fa-trash" /> {{ $t("Delete") }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </td>
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
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Domain Requests") };
  },
  data: () => ({
    breadcrumbsCurrent: "Domain Requests",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Domain Requests",
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
        path: "/api/domain-requests?page=",
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


    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
    },

    // delete data
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
            .patch("/api/domain-requests/" + slug, {
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
            .catch(() => {
              Swal.fire(
                this.$t("Failed!"),
                this.$t("There was something wrong."),
                "warning"
              );
            });
        }
      });
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("Do you really want to delete this Domain Request?"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/domain-requests/",
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
                  this.$t("Delete failed"),
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
