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
                {{ $t("Inventory") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                  <i class="fas fa-sync"></i>
                </a>
                <a :href="exportUrl" v-tooltip="$t('Export to Excel')" class="btn btn-info">
                  <i class="fa fa-arrow-circle-down"></i>
                </a>
                <a href="/products/pdf" v-tooltip="$t('Export to PDF')" class="btn btn-secondary">
                  <i class="fas fa-download"></i>
                </a>
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
             <div class="row d-fex justify-content-between">
                <div class="col-6 col-xl-4 mb-2">
                  <search
                    v-model="query"
                    @reset-pagination="resetPagination()"
                    @reload="reload"
                  />
                </div>
                <div class="card-tools col-3 col-xl-2 mb-2">
                  <select
                    v-model="filterType"
                    class="form-control"
                    id="summeryType"
                    name="summeryType"
                  >
                    <option value="default" selected>
                      {{ $t("Filter") }}
                    </option>
                    <option value="low_to_high_stock">
                      {{ $t("Filter By Low To High Stock") }}
                    </option>
                    <option value="high_to_low_stock">
                      {{ $t("Filter By High To Low Stock") }}
                    </option>
                    <option value="low_to_high_purchase_price">
                      {{ $t("Filter By Low To High Price") }}
                    </option>
                    <option value="high_to_low_purchase_price">
                      {{ $t("Filter By High To Low Price") }}
                    </option>
                    <option value="inactive">
                      {{ $t("Filter By Inactive Status") }}
                    </option>
                    <option value="active">
                      {{ $t("Filter By Active Status") }}
                    </option>
                    <option value="non_zero_stock">
                      {{ $t("Filter Non Zero Stock") }}
                    </option>
                    <option value="zero_stock">
                      {{ $t("Filter Zero Stock") }}
                    </option>
                  </select>
                </div>
            </div>
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Code") }}</th>
                    <th>{{ $t("Name") }}</th>
                    <th>{{ $t("Item Model") }}</th>
                    <th>{{ $t("Stock") }}</th>
                    <th>{{ $t("Avg. Purchase Price") }}</th>
                    <th>{{ $t("Selling Price") }}</th>
                    <th>{{ $t("Inventory Value") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th class="no-print">{{ $t("Action") }}</th>
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
                    <td>{{ data.code | withPrefix(prefix) }}</td>
                    <td>
                      <router-link :to="{
                        name: 'products.show',
                        params: { slug: data.slug },
                      }">
                        {{ data.name }}
                      </router-link>
                    </td>
                    <td>{{ data.itemModel }}</td>
                    <td>
                      <span v-if="data.availableQty < data.alertQty" v-tooltip="$t('Stock is less than alert qty!')"
                        class="badge badge-danger p-2">
                        <i class="fas fa-exclamation"></i>
                      </span>
                      <span v-if="data.itemUnit">
                        {{ data.availableQty }} {{ data.itemUnit.code }}
                      </span>
                    </td>
                    <td>{{ data.avgPurchasePrice | withCurrency }}</td>
                    <td>
                      <span v-if="data.discount > 0">
                        <del>{{ data.regularPrice | withCurrency }}</del>{{ data.sellingPrice | withCurrency }} ({{
                          data.discount
                        }}%)
                      </span>
                      <span v-else>{{ data.regularPrice | withCurrency }}
                      </span>
                    </td>
                    <td>
                      {{
                        (data.avgPurchasePrice * data.availableQty)
                        | withCurrency
                      }}
                    </td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t("Active")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("Inactive")
                      }}</span>
                    </td>
                    <td class="text-right no-print">
                      <div class="btn-group">
                        <router-link v-if="$can('inventory-history')" v-tooltip="$t('Inventory History')"
                          :to="{
                            name: 'inventory.history',
                            params: { slug: data.slug },
                          }" class="btn btn-primary btn-sm">
                          <i class="fas fa-history" />
                        </router-link>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="11">
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
    return { title: this.$t("Inventory") };
  },
  data: () => ({
    breadcrumbsCurrent: "Inventory",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Products",
        url: "products.index",
      },
      {
        name: "Inventory",
        url: "",
      },
    ],
    query: "",
    filterType: "default",
    showModal: false,
    perPage: 10,
    prefix: "",
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
    exportUrl() {
      // Create a dynamic export URL with query parameters
      return `/inventory/excel?term=${this.query}`;
    },
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

    filterType: function (newQ, oldQ) {
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
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/inventory?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
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
      await this.$store.dispatch("operations/searchDataWithFilterType", {
        path: "/api/inventory/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        filterType: this.filterType,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
      this.filterType = "default";
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    refreshTable() {
      this.query = "";
      this.filterType = "default";
      this.query === "" ? this.getData() : this.searchData();
    },
  },
};
</script>
