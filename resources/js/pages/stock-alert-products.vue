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
                {{ $t("Suppliers") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group">
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link v-if="$can('product-create')" :to="{ name: 'products.create' }" class="btn btn-primary">
                  {{ $t("Create") }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
              </div>

            </div>
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Category") }}</th>
                    <th>{{ $t("Code") }}</th>
                    <th>{{ $t("Name") }}</th>
                    <th>{{ $t("Stock") }}</th>
                    <th>{{ $t("Selling Price") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th v-if="$can('product-edit') ||
                      $can('product-view') ||
                      $can('product-delete')
                      " class="text-right no-print">
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
                    <td>
                      <span v-if="data.subCategory">{{ data.subCategory.name }} [{{
                        data.subCategory.code | withPrefix(subCatPrefix)
                      }}]
                      </span>
                    </td>
                    <td>{{ data.code | withPrefix(prefix) }}</td>
                    <td>
                      <router-link :to="{
                        name: 'products.show',
                        params: { slug: data.slug },
                      }">
                        {{ data.name }}
                      </router-link>
                    </td>
                    <td>
                      <span v-if="data.inventoryCount < data.alertQty" v-tooltip="$t('Stock is less than alert qty!')"
                        class="badge badge-danger p-2">
                        <i class="fas fa-exclamation"></i>
                      </span>
                      <span v-if="data.itemUnit">
                        {{ data.inventoryCount }} {{ data.itemUnit.code }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.discount > 0"><del>{{ data.regularPrice }}</del>
                        {{ data.sellingPrice | withCurrency }} ({{
                          data.discount
                        }}%)</span>
                      <span v-else>{{ data.regularPrice | withCurrency }} </span>
                    </td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t("Active")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("Inactive")
                      }}</span>
                    </td>
                    <td v-if="$can('product-edit') ||
                        $can('product-view') ||
                        $can('product-delete')
                        " class="text-right no-print">
                      <div class="btn-group">
                        <router-link v-if="$can('product-view')" v-tooltip="$t('View')" :to="{
                          name: 'products.show',
                          params: { slug: data.slug },
                        }" class="btn btn-primary btn-sm">
                          <i class="fas fa-eye" />
                        </router-link>
                        <router-link v-if="$can('product-edit')" v-tooltip="$t('Edit')" :to="{
                          name: 'products.edit',
                          params: { slug: data.slug },
                        }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-if="$can('product-delete')" v-tooltip="$t('Delete')" href="#"
                          class="btn btn-danger btn-sm" @click="deleteData(data.slug)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="10">
                      <div class="text-center py-8">
                        <img src="/../../images/result-not-found.svg" class="w-64 m-auto" alt="result-not-found" />
                        <p class="font-bold text-lg text-gray-600 dark:text-gray-200">
                          {{ $t("Sorry no data found") }}.
                        </p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
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
  middleware: ["auth"],
  metaInfo() {
    return { title: this.$t("Stock Alert Products") };
  },
  data: () => ({
    breadcrumbsCurrent: "Stock Alert Products",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Alert Products",
        url: "",
      },
    ],
    showModal: false,
    query: "",
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
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
    this.prefix = this.appInfo.productPrefix;
    this.catPrefix = this.appInfo.proCatPrefix;
    this.subCatPrefix = this.appInfo.proSubCatPrefix;
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/stock-alert-products?page=",
        currentPage: currentPage,
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
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        term: this.query,
        path: "/api/stock-alert-products/search/",
        currentPage: currentPage,
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

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
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
              path: "/api/products/",
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
                this.$t("Failed!"),
                  this.$t("There was something wrong."),
                  "warning";
              }
            });
        }
      });
    },
  },
};
</script>
