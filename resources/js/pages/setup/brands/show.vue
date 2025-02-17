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
          <div class="card-header setings-header no-print">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Brand Details") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group">
                <router-link :to="{ name: 'brands.index' }" class="btn btn-dark float-right">
                  <i class="fas fa-long-arrow-alt-left" />
                  {{ $t("Back") }}
                </router-link>
                <a href="#" @click="printWindow" class="btn btn-default"><i class="fas fa-print"></i> {{
                  $t("Print") }}</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="invoice p-3 mb-3">
              <div class="table-responsive table-custom">
                <table class="table">
                  <thead>
                    <tr>
                      <th>{{ $t("Preview") }}</th>
                      <th>{{ $t("Brand Name") }}</th>
                      <th>{{ $t("Short Code") }}</th>
                      <th v-if="allData.note">{{ $t("Note") }}</th>
                      <th>{{ $t("Status") }}</th>
                      <th class="text-right">
                        {{ $t("Created At") }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <a v-if="allData.image" href="#" id="show-modal" @click="showModal = true">
                          <img :src="allData.image" class="rounded preview-sm" loading="lazy" />
                        </a>
                        <div v-else class="bg-secondary rounded no-preview-sm">
                          <small>{{ $t("No Preview") }}</small>
                        </div>
                      </td>
                      <td>{{ allData.name }}</td>
                      <td>{{ allData.code }}</td>
                      <td v-if="allData.note">{{ allData.note }}</td>
                      <td>
                        <span v-if="allData.status === 1" class="badge bg-success">{{ $t("Active") }}</span>
                        <span v-else class="badge bg-danger">{{ $t("Inactive") }}</span>
                      </td>
                      <td class="text-right">
                        {{ allData.createdAt | moment("Do MMM, YYYY") }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

     <!--  activity logs -->
     <div class="card custom-card w-100 mt-5">
      <div class="card-header setings-header">
        <div class="col-xl-4 col-4">
          <h3 class="card-title">
            {{ $t("Activity log") }}
          </h3>
        </div>
        <div class="col-xl-8 col-8 float-right text-right">
          <div class="btn-group c-w-100">
            <a
              @click="refreshTable()"
              href="#"
              v-tooltip="'Refresh'"
              class="btn btn-success"
            >
              <i class="fas fa-sync"></i>
            </a>
            <a
              @click="print"
              v-tooltip="$t('Print Table')"
              class="btn btn-info"
            >
              <i class="fas fa-print"></i>
            </a>
          </div>
        </div>
      </div>
      <table-loading v-show="loading" />
      <div class="card-body position-relative">
        <div class="row">
          <div class="col-6 col-xl-4 mb-2">
            <search
              v-model="query"
              @reset-pagination="resetPagination()"
              @reload="reload"
            />
          </div>
        </div>
        <div id="printMe" class="table-responsive table-custom mt-3">
          <div v-show="items.length > 0" v-for="(data, i) in items" :key="i">
            <div class="card mb-0 border border-gray">
              <div class="card-body py-1">
                <div class="row">
                  <div
                    class="col-1 d-flex justify-content-center align-items-center"
                  >
                    <i
                      v-if="data.event == 'Update'"
                      class="fa fa-magic"
                      aria-hidden="true"
                    ></i>
                    <i
                      v-if="data.event == 'Create'"
                      class="fa fa-plus-circle"
                      aria-hidden="true"
                    ></i>
                    <i
                      v-if="data.event == 'Delete'"
                      class="fa fa-trash"
                      aria-hidden="true"
                    ></i>
                  </div>
                  <div class="col-11">
                    <div class="row">
                      <div class="col-12">
                        <p class="text-bold mb-0">{{ data.causer_name }}</p>
                      </div>
                      <div class="col-12">
                        <p class="mb-0">
                          {{ data.description }}
                        </p>
                      </div>
                      <div class="col-12">
                        <p class="mb-0">{{ data.performedAt }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center" v-show="!loading && !items.length">
            <EmptyTable />
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="dtable-footer">
          <div class="form-group row display-per-page">
            <label>{{ $t("per_page") }} </label>
            <div>
              <select
                @change="updatePerPager"
                v-model="perPage"
                class="form-control form-control-sm ml-1"
              >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
          </div>
          <!-- pagination-start -->
          <pagination
            v-if="pagination && pagination.last_page > 1"
            :pagination="pagination"
            :offset="5"
            class="justify-flex-end"
            @paginate="paginate"
          />
          <!-- pagination-end -->
        </div>
      </div>
    </div>


    <!-- use the modal component, pass in the prop -->
    <Modal v-if="showModal" @close="showModal = false">
      <h5 slot="header">{{ $t("Attached Image Preview") }}</h5>
      <div class="w-100" slot="body">
        <img :src="allData.image" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Brand Details") };
  },
  data: () => ({
    breadcrumbsCurrent: "Brand Details",
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
        name: "Brands",
        url: "brands.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    url: null,
    showModal: false,
    allData: "",
    perPage: 10,
    query: "",
  }),

  computed: {
    ...mapGetters("operations", ["appInfo", "items", "loading", "pagination"]),
  },

  watch: {
    // watch search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        this.getActivity();
      } else {
        this.searchData();
      }
    },
  },

  created() {
    this.getBrand();
    this.getActivity();
  },
  methods: {
    // get the brand
    async getBrand() {
      const { data } = await axios.get(
        window.location.origin + "/api/brands/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

      // get activity logs
      async getActivity() {
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      this.$store.state.operations.loading = true;
      let slug = this.$route.params.slug;
      let modelName = "Brand";
      await this.$store.dispatch("operations/fetchSpecificLogs", {
        path: "/api/activity-log-specific?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
        slug: slug,
        modelName: modelName,
      });
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let slug = this.$route.params.slug;
      let modelName = "Brand";
      await this.$store.dispatch("operations/fetchSpecificLogs", {
        path: "/api/activity-log-specific?page=",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
        term: this.query,
        slug: slug,
        modelName: modelName,
      });
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getActivity() : this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getActivity() : this.searchData();
    },

    // reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // reload after search
    async reload() {
      this.query = "";
    },

    // pagination
    async paginate() {
      this.getActivity();
    },

    // print
    printWindow() {
      window.print();
    },
  },
};
</script>

<style lang="scss" scoped></style>
