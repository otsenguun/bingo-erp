<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div
        class="col-lg-12"
        v-if="
          $can('expense-category-list') ||
          $can('expense-category-create') ||
          $can('expense-category-edit') ||
          $can('expense-category-delete')
        "
      >
        <div class="card custom-card w-100">
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

          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search
                  v-model="query"
                  @reset-pagination="resetPagination()"
                  @reload="reload"
                />
              </div>
              <div class="col-6 col-xl-8 mb-2 text-right">
                <date-range-picker
                  ref="picker"
                  opens="left"
                  :locale-data="locale"
                  :minDate="minDate"
                  :maxDate="maxDate"
                  :singleDatePicker="false"
                  :showWeekNumbers="false"
                  :showDropdowns="true"
                  :autoApply="true"
                  v-model="dateRange"
                  @update="updateValues"
                  :linkedCalendars="true"
                  class="c-w-100"
                >
                  <template v-slot:input="picker" style="min-width: 350px">
                    {{ picker.startDate | startDate }} -
                    {{ picker.endDate | endDate }}
                  </template>
                </date-range-picker>
              </div>
            </div>
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
              <div
                v-show="items.length > 0"
                v-for="(data, i) in items"
                :key="i"
              >
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
                        <div v-if="data.routeName && data.slug" class="row">
                          <a
                            :href="jumpToViewPage(data.routeName, data.slug)"
                            target="_blank"
                          >
                            <div class="col-12">
                              <p class="text-bold mb-0">
                                {{ data.causer_name }}
                              </p>
                            </div>
                            <div class="col-12">
                              <p class="mb-0">
                                {{ data.description }}
                                {{ data.related_data_code }}
                              </p>
                            </div>
                            <div class="col-12">
                              <p class="mb-0">{{ data.performedAt }}</p>
                            </div>
                          </a>
                        </div>
                        <div v-else class="row">
                          <div class="col-12">
                            <p class="text-bold mb-0">{{ data.causer_name }}</p>
                          </div>
                          <div class="col-12">
                            <p class="mb-0">
                              {{ data.description }}
                              {{ data.related_data_code }}
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
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import i18n from "~/plugins/i18n";
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import axios from "axios";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Activity log") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "Activity log",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Activity log",
        url: "",
      },
    ],
    minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
    maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
    dateRange: {
      startDate: "",
      endDate: "",
    },
    locale: {
      direction: "ltr",
      format: "YYYY-MM-DD",
      separator: " - ",
      applyLabel: "Apply",
      cancelLabel: "Cancel",
      weekLabel: "W",
      customRangeLabel: "Custom Range",
      daysOfWeek: moment.weekdaysMin(),
      monthNames: moment.monthsShort(),
      firstDay: 1,
    },
    perPage: 25,
    query: "",
    user: "",
    codePrefix: "",
  }),
  filters: {
    startDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("From");
    },
    endDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("To");
    },
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchData();
        } else {
          this.getData();
        }
      } else {
        this.searchData();
      }
    },
  },
  created() {
    this.getData();
    this.getUser();
    this.codePrefix = this.appInfo.expCatPrefix;
  },
  methods: {
    // filter data for selected date range
    async updateValues() {
      this.dateRange.startDate = moment(this.dateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.dateRange.endDate = moment(this.dateRange.endDate).format(
        "YYYY-MM-DD"
      );
      this.searchData();
    },

    // get the user
    async getUser() {
      const { data } = await axios.get(window.location.origin + "/api/user");
      this.user = data.data;
    },

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },
    // get data
    async getData() {
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/activity-log?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // pagination
    async paginate() {
      this.query === "" ? this.getData() : this.searchData();
    },

    // reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/activity-log/search",
        term: this.query,
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // reload after search
    async reload() {
      this.query = "";
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // refresh table
    refreshTable() {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;

      this.query === "" ? this.getData() : this.searchData();

      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
        }.bind(this),
        500
      );
    },
    // delete data
    async deleteData(id) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("You will not be able to return to this!"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          axios.post("api/activity-log/delete", { id: id }).then((response) => {
            if (response.data.success) {
              Swal.fire(
                this.$t("Deleted!"),
                this.$t("Deleted successfully."),
                "success"
              );
              this.getData();
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

    // jump to the view page
    jumpToViewPage(routeName, slug) {
      if (routeName && slug) {
        return this.$router.resolve({ name: routeName, params: { slug: slug } })
          .href;
      }
      return;
    },
  },
};
</script>
