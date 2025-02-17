<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-warning">
          <strong>
            <i class="icon fas fa-edit"></i>
            {{
              $t("You need to add an A record to your DNS with IP") +
              ": " +
              server_ip
            }}
          </strong>

          <ul>
            <li>{{ $t("Type") }}: A</li>
            <li>{{ $t("Host") }}: @</li>
            <li>{{ $t("Value") }}: {{ server_ip }}</li>
          </ul>
        </div>
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Domain Requests") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link v-if="tenant.plan.limit_domains === 0 ||
                  (pagination && tenant.plan.limit_domains > pagination.total)
                  " :to="{ name: 'domain-requests.create' }" class="btn btn-primary">
                  {{ $t("Create") }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
              </div>
            </div>
          </div>
          <div class="card-body position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Requested Domain") }}</th>
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
                    <td v-html="data.status_html"></td>
                    <td class="text-right no-print">
                      <div v-if="data.id" class="btn-group">
                        <a v-if="data.status == 0" v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm"
                          @click="deleteData(data.id)">
                          <i class="fas fa-trash" />
                        </a>
                        <span v-else>{{ $t("no_action_available") }}</span>
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
          <!-- /.card-body -->
          <div class="card-footer">
            <div class="dtable-footer">
              <div class="form-group row display-per-page">
                <label>{{ $t("per_page") }} </label>
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
import axios from "axios";

export default {
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
    server_ip: "",
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
    this.getServerIp();
    this.employeePrefix = this.appInfo.employeePrefix;
  },
  methods: {
    // get the tenant
    async getServerIp() {
      const { data } = await axios.get(
        window.location.origin + "/api/server-ip"
      );
      this.server_ip = data.data.server_ip;
    },

    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/domain-requests?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
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
    async paginate() {
      this.query === "" ? this.getData() : this.searchData();
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
