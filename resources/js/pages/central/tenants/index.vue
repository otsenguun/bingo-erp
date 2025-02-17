<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card custom-card w-100">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Tenants") }}
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
                <a href="/tenants/pdf" v-tooltip="$t('Export to PDF')" class="btn btn-secondary">
                  <i class="fas fa-file-export"></i>
                </a>
                <a @click="print" v-tooltip="$t('Export PDF')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
              </div>
              <div class="col-6 col-xl-8 mb-2 text-right">
                <date-range-picker ref="picker" opens="left" :locale-data="locale" :minDate="minDate" :maxDate="maxDate"
                  :singleDatePicker="false" :showWeekNumbers="false" :showDropdowns="true" :autoApply="true"
                  v-model="dateRange" @update="updateValues" :linkedCalendars="true" class="c-w-100">
                  <template v-slot:input="picker" style="min-width: 350px">
                    {{ picker.startDate | startDate }} -
                    {{ picker.endDate | endDate }}
                  </template>
                </date-range-picker>
              </div>
            </div>
            <table-loading v-show="loading" />
            <div id="printMe" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("S.No") }}</th>
                    <th>{{ $t("Domain") }}</th>
                    <th>{{ $t("Name & Email") }}</th>
                    <th>{{ $t("Plan") }}</th>
                    <th>{{ $t("On Trial") }}</th>
                    <th>{{ $t("Is Verified") }}</th>
                    <th>{{ $t("Is Subscribed") }}</th>
                    <th>{{ $t("Banned") }}</th>
                    <th class="text-right no-print" v-if="!isDemoMode">
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
                      {{ data.domain }}
                    </td>
                    <td class="profile-area">
                      <div class="mr-2 img">
                        <img :src="data.photo_url" :alt="data.name" class="rounded-circle" />
                      </div>
                      <div>
                        <span class="text-capitalize">{{ data.name }}</span>
                        <a :href="`mailto:${data.email}`">{{ data.email }}</a>
                      </div>
                    </td>
                    <td>{{ data.plan && data.plan.name }}</td>
                    <td>
                      <span v-if="data.on_trial" class="badge bg-success">
                        {{ $t("True") }}
                      </span>
                      <span v-else class="badge bg-danger">
                        {{ $t("False") }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.email_verified_at" class="badge bg-success">
                        {{ $t("True") }}
                      </span>
                      <span v-else class="badge bg-danger">
                        {{ $t("False") }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.is_subscribed" class="badge bg-success">
                        {{ $t("True") }}
                      </span>
                      <span v-else class="badge bg-danger">
                        {{ $t("False") }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.is_banned == false" class="badge bg-success">{{ $t("False")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("True")
                      }}</span>
                    </td>
                    <td class="text-right no-print" v-if="!isDemoMode">
                      <div class="btn-group">
                        <router-link v-if="data.email_verified_at" v-tooltip="$t('View')" :to="{
                          name: 'tenants.show',
                          params: { id: data.id },
                        }" class="btn btn-primary btn-sm">
                          <i class="fas fa-eye" />
                        </router-link>
                        <button v-if="data.email_verified_at" @click="impersonate(data.id)"
                          v-tooltip="$t('Impersonate')" class="btn btn-info btn-sm">
                          <i class="fas fa-user-secret" />
                        </button>
                        <router-link v-if="data.email_verified_at" :to="{
                          name: 'send-notification',
                          params: { id: data.id },
                        }" v-tooltip="$t('Send Email')" class="btn btn-secondary btn-sm">
                          <i class="fas fa-envelope" />
                        </router-link>

                        <router-link v-if="data.email_verified_at" v-tooltip="$t('Edit')" :to="{
                          name: 'tenants.edit',
                          params: { id: data.id },
                        }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-if="data.email_verified_at" href="#" v-tooltip="data.is_banned
                          ? $t('Unban')
                          : $t('Ban')
                          " class="btn btn-sm" :class="data.is_banned ? 'btn-success' : 'btn-warning'"
                          @click="ban(data.id)">
                          <i class="fas fa-ban" />
                        </a>
                        <a href="#" v-tooltip="$t('Delete')" class="btn btn-danger btn-sm"
                          @click="deleteData(data.id)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="9">
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
import i18n from "~/plugins/i18n";
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Tenants") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "Tenants",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Tenants",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
    clientPrefix: "",
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
    isDemoMode: window.config.isDemoMode,
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
    exportUrl() {
      // Create a dynamic export URL with query parameters
      return `/tenants/export/excel?start_date=${this.dateRange.startDate}&end_date=${this.dateRange.endDate}&term=${this.query}`;
    },
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
    this.clientPrefix = this.appInfo.clientPrefix;
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
        path: "/api/tenants?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    async impersonate(id) {
      axios
        .get(`/api/impersonate/${id}`)
        .then(({ data }) => {
          window.location.replace(data.data.redirect_url);
        })
        .catch((error) => {
          toast.fire({
            icon: "error",
            title: error.response.data.message,
          });
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
        path: "/api/tenants/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
      await this.searchData();
    },


    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Delete Tenant"),
        text: this.$t("Are you sure you want to delete this tenant?"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Yes, delete it!"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/tenants/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted"),
                  this.$t("Tenant deleted successfully"),
                  "success"
                );
                this.getData();
              } else {
                Swal.fire(
                  this.$t("Failed"),
                  this.$t("Delete failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete data
    async ban(id) {
      axios
        .post(window.location.origin + "/api/tenants/" + id + "/ban")
        .then((response) => {
          if (response.data.success === true) {
            Swal.fire(
              this.$t(response.data.message),
              this.$t(response.data.message + "_success"),
              "success"
            );
            this.getData();
          } else {
            Swal.fire(
              this.$t("Failed"),
              this.$t("Ban failed"),
              "warning"
            );
          }
        });
    },
  },
};
</script>

<style scoped>
.table-custom .table td {
  vertical-align: middle;
}

.profile-area {
  display: flex;
  align-items: center;
}

.profile-area img {
  width: 50px;
  height: 50px;
  border-radius: 100%;
}

.profile-area a {
  display: block;
}
</style>
