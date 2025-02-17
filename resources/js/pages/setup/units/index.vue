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
        <div class="card">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Units") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group">
                <a href="/setup/units/pdf" v-tooltip="$t('Export Table')" class="btn btn-secondary">
                  <i class="fas fa-file-export"></i>
                </a>
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link :to="{ name: 'units.create' }" class="btn btn-primary">
                  {{ $t("Create") }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
              </div>
            </div>
          </div>
          <div class="card-body">
            <search class="col-md-12" v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
            <div class="col-md-12">
              <table-loading v-show="loading" />
              <div class="table-responsive table-custom mt-3" id="printMe">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ $t("#") }}</th>
                      <th>{{ $t("Name") }}</th>
                      <th>{{ $t("Code") }}</th>
                      <th>{{ $t("Note") }}</th>
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
                            pagination.per_page *
                            (pagination.current_page - 1) +
                            (i + 1)
                          }}
                        </span>
                        <span v-else>{{ i + 1 }}</span>
                      </td>
                      <td>{{ data.name }}</td>
                      <td>{{ data.code }}</td>
                      <td>{{ data.note }}</td>
                      <td>
                        <span v-if="data.status === 1" class="badge bg-success">{{ $t("Active") }}</span>
                        <span v-else class="badge bg-danger">{{
                          $t("Inactive")
                        }}</span>
                      </td>
                      <td class="text-right no-print">
                        <div class="btn-group">
                          <router-link v-tooltip="$t('Edit')" :to="{
                                name: 'units.edit',
                                params: { slug: data.slug },
                              }" class="btn btn-info btn-sm">
                            <i class="fas fa-edit" />
                          </router-link>
                          <a v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm"
                            @click="deleteData(data.slug)">
                            <i class="fas fa-trash" />
                          </a>
                        </div>
                      </td>
                    </tr>
                    <tr v-show="!loading && !items.length">
                      <td colspan="6">
                        <EmptyTable />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
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

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Units") };
  },
  data: () => ({
    breadcrumbsCurrent: "Units",
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
        name: "Units",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination"]),
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === "") {
        this.getData();
      } else {
        this.searchData();
      }
    },
  },
  created() {
    this.getData();
  },
  methods: {
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/units?page=",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
      });
    },

    // Pagination
    async paginate() {
      this.query === "" ? this.getData() : this.searchData();
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/searchData", {
        term: this.query,
        path: "/api/units/search/",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("You will not be able to return to this!"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/units/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
              } else {
                Swal.fire(
                  this.$t("Failed!"),
                  this.$t("There was something wrong."),
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
