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
                {{ $t("Clients") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a @click="refreshTable()" href="#" v-tooltip="'Refresh'" class="btn btn-success">
                  <i class="fas fa-sync"></i>
                </a>
                <a href="#" @click="uploadCsvModalShow()" v-tooltip="'Import'" class="btn btn-dark">
                  <i class="fas fa-upload"></i>
                </a>
                <a :href="exportUrl" v-tooltip="$t('Export to Excel')" class="btn btn-info">
                  <i class="fa fa-arrow-circle-down"></i>
                </a>
                <a href="/clients/pdf" v-tooltip="$t('Export to PDF')" class="btn btn-secondary">
                  <i class="fas fa-file-export"></i>
                </a>
                <a @click="print" v-tooltip="$t('Print Table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link v-if="$can('client-create')" :to="{ name: 'clients.create' }" class="btn btn-primary">
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
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Image") }}</th>
                    <th>{{ $t("Client ID") }}</th>
                    <th>{{ $t("Name") }}</th>
                    <th>{{ $t("Contact Number") }}</th>
                    <th>{{ $t("Email") }}</th>
                    <th>{{ $t("Company Name") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th v-if="$can('client-edit') ||
                      $can('client-view') ||
                      $can('client-delete')
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
                      <a v-if="data.image" href="#" id="show-modal" @click="previewModal(data.image)">
                        <img :src="data.image" class="rounded preview-sm" loading="lazy" />
                      </a>
                      <div v-else class="bg-secondary rounded no-preview-sm">
                        <small>{{ $t("No Preview") }}</small>
                      </div>
                    </td>
                    <td>{{ data.clientID | withPrefix(clientPrefix) }}</td>
                    <td>
                      <router-link v-if="$can('client-view')" :to="{
                        name: 'clients.show',
                        params: { slug: data.slug },
                      }">
                        {{ data.name }}
                      </router-link>
                      <span v-else>{{ data.name }}</span>
                    </td>
                    <td>{{ data.phoneNumber }}</td>
                    <td>{{ data.email }}</td>
                    <td>{{ data.companyName }}</td>
                    <td>
                      <span v-if="data.status === 1" class="badge bg-success">{{
                        $t("Active")
                      }}</span>
                      <span v-else class="badge bg-danger">{{
                        $t("Inactive")
                      }}</span>
                    </td>
                    <td v-if="$can('client-edit') ||
                        $can('client-view') ||
                        $can('client-delete')
                        " class="text-right no-print">
                      <div class="btn-group">
                        <router-link v-if="$can('client-view')" v-tooltip="$t('View')" :to="{
                          name: 'clients.show',
                          params: { slug: data.slug },
                        }" class="btn btn-primary btn-sm">
                          <i class="fas fa-eye" />
                        </router-link>
                        <router-link v-if="$can('client-edit')" v-tooltip="$t('Edit')" :to="{
                          name: 'clients.edit',
                          params: { slug: data.slug },
                        }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-if="appInfo.defaultClientSlug != data.slug &&
                          $can('client-delete')
                          " v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm"
                          @click="deleteData(data.slug)">
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
    <Modal v-if="showModal" @close="previewModal()">
      <h5 slot="header">{{ $t("Attached Image Preview") }}</h5>
      <div class="w-100" slot="body">
        <img :src="imagePath" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>

    <!--csv modal-->
    <Modal class="import-modal" v-if="showUploadCsvModal" @close="showUploadCsvModal = false">
      <h5 slot="header">{{ $t("Upload Your CSV file") }}</h5>
      <form slot="body" role="form" @submit.prevent="saveCsv" @keydown="form.onKeydown($event)">
        <div class="w-100">
          <div class="row">
            <input :class="{ 'is-invalid': form.errors.has('file') }" type="file" id="file" @change="onFileChange"
              class="form-control" />
            <small id="downloadHelp" class="form-text text-muted"><a href="/demo-csv-file/demo.csv">{{ $t("download")
            }}</a>
              {{ $t("demo_file") }}</small>
            <span class="invalid-feedback" v-show="form.errors.has('file')">
              {{ form.errors.get("file") }}
            </span>
          </div>
          <div class="row mt-3">
            <v-button :loading="form.busy" class="btn btn-primary">
              <i class="fas fa-save" /> {{ $t("Save") }}
            </v-button>
            <button type="reset" class="btn btn-secondary ml-2" @click="form.reset()">
              {{ $t("Reset") }}
            </button>
          </div>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import moment from "moment";
import { mapGetters } from "vuex";
import i18n from "~/plugins/i18n";
import VButton from "../../components/Button";
import DateRangePicker from "vue2-daterange-picker";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Clients") };
  },
  components: {
    VButton,
    DateRangePicker,
  },
  data: () => ({
    form: new Form({
      file: "",
    }),
    file: "",
    breadcrumbsCurrent: "Clients",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Clients",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
    showModal: false,
    showUploadCsvModal: false,
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
      return `/clients/export/excel?start_date=${this.dateRange.startDate}&end_date=${this.dateRange.endDate}&term=${this.query}`;
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
    saveCsv() {
      this.form
        .post("/api/client-import")
        .then(() => {
          this.showUploadCsvModal = false;
          Swal.fire(
            this.$t("Success"),
            this.$t("CSV file imported successfully!"),
            "success"
          );
          this.getData();
          this.form.reset();
        })
        .catch(({ response }) => {
          // this.showUploadCsvModal = false
          if (response.data.row_number) {
            this.showUploadCsvModal = false;
            Swal.fire({
              title: "Error",
              text:
                "Row Number " +
                response.data.row_number +
                " has error. " +
                response.data.message,
              icon: "error",
              button: "Ok",
            });
            this.form.reset();
          }
        });
    },
    //show the modal
    uploadCsvModalShow() {
      this.form = new Form({
        file: "",
      });
      this.showUploadCsvModal = true;
    },
    // close the csv upload modal
    uploadCsvModalClose() {
      this.showUploadCsvModal = false;
    },
    onFileChange(e) {
      this.form.file = e.target.files[0];
    },
    // submit the csv file
    async submitCsvFile() {
      let newThis = this;
      let formData = new FormData();

      /*
          Add the form data we need to submit
      */
      formData.append("file", this.file);

      /*
        Make the request to the POST /single-file URL
      */
      await axios
        .post("/api/client-import", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(function () {
          // console.log(response.data);
          newThis.showUploadCsvModal = false;
          toast.fire({
            icon: "success",
            title: "Clients imported successfully",
          });
          newThis.getData();
        })
        .catch(function (e) {
          console.log(e.response.data);
          newThis.showUploadCsvModal = false;
          Swal.fire({
            title: "Error",
            text:
              "Row Number " +
              e.response.data.row_number +
              " has error. " +
              e.response.data.message,
            icon: "error",
            button: "Ok",
          });
        });
    },
    // handle the file upload
    handleFileUpload() {
      this.file = this.$refs.file.files[0];
    },
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
        path: "/api/clients?page=",
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
      await this.$store.dispatch("operations/searchData", {
        path: "/api/clients/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // reload after search
    async reload() {
      this.query = "";
      await this.searchData();
    },

    // display modal
    previewModal(image) {
      this.imagePath = image;
      if (this.showModal) {
        return (this.showModal = false);
      }
      return (this.showModal = true);
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
              path: "/api/clients/",
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
                  this.$t("Sorry you can't delete this client."),
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
