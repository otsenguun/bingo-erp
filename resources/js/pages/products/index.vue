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
                {{ $t("Items") }}
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
                <!--upload csv button end-->
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
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
              </div>
            </div>
            <table-loading v-show="loading" />
            <div id="printMe" v-if="!loading" class="table-responsive table-custom mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Type") }}</th>
                    <th>{{ $t("Image") }}</th>
                    <th>{{ $t("Category") }}</th>
                    <th>{{ $t("Code") }}</th>
                    <th>{{ $t("Name") }}</th>
                    <th>{{ $t("Item Model") }}</th>
                    <th>{{ $t("Unit") }}</th>
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
                    <td class="text-capitalize">{{ data.itemType }}</td>
                    <td>
                      <a v-if="data.image" href="#" id="show-modal" @click="previewModal(data.image)">
                        <img :src="data.image" class="rounded preview-sm" loading="lazy" />
                      </a>
                      <div v-else class="bg-secondary rounded no-preview-sm">
                        <small>{{ $t("No Preview") }}</small>
                      </div>
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
                    <td>{{ data.itemModel }}</td>
                    <td>{{ data.itemUnit.code }}</td>
                    <td>
                      <span v-if="data.discount > 0"><del>{{ data.regularPrice }}</del>
                        {{ data.sellingPrice | withCurrency }} ({{
                          data.discount
                        }}%)</span>
                      <span v-else>{{ data.regularPrice | withCurrency }}
                      </span>
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
    <Modal v-if="showModal" @close="previewModal()">
      <h5 slot="header">{{ $t("Attached Image Preview") }}</h5>
      <div class="w-100" slot="body">
        <img :src="imagePath" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>
    <!--csv modal start-->
    <Modal class="pay-modal" v-if="showUploadCsvModal" @close="showUploadCsvModal = false">
      <h5 slot="header">{{ $t("Upload Your CSV file") }}</h5>
      <div class="w-100" slot="body">
        <!-- Download All Template CSV -->
        <form role="form" @submit.prevent="saveCsv" @keydown="form.onKeydown($event)">
          <div class="row">
            <div class="alert alert-info">
              <h5>{{ $t("Instructions") }}</h5>
              <ul>
                <li>{{ $t("Download the zip folder.") }}</li>
                <li>{{ $t("Populate the products.csv with your product information.") }}</li>
                <li>{{ $t("You can get the ids related to product to their corresponding CSV file") }}</li>
                <li>{{ $t("Example: sub_cat_id in sub-categories.csv") }}</li>
              </ul>
            </div>
            <div class="form-group">
              <a @click="downloadTemplate" class="btn btn-primary">
                <i class="fas fa-download"></i> {{ $t("Download") }}
              </a>
            </div>
          </div>
          <div class="row">
            <input :class="{ 'is-invalid': form.errors.has('file') }" type="file" id="file" @change="onFileChange"
              class="form-control" />
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
        </form>
      </div>
    </Modal>
    <!--csv modal end-->
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Items") };
  },
  data: () => ({
    form: new Form({
      file: "",
    }),
    showUploadCsvModal: false,
    breadcrumbsCurrent: "Items",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Items",
        url: "",
      },
    ],
    showModal: false,
    perPage: 10,
    query: "",
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
    exportUrl() {
      // Create a dynamic export URL with query parameters
      return `/products/export/excel?term=${this.query}`;
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
  },
  created() {
    this.getData();
    this.prefix = this.appInfo.productPrefix;
    this.catPrefix = this.appInfo.proCatPrefix;
    this.subCatPrefix = this.appInfo.proSubCatPrefix;
  },
  methods: {
    // get the csv file to form
    onFileChange(e) {
      this.form.file = e.target.files[0];
    },

    // save the csv file on database
    saveCsv() {
      this.form
        .post("/api/product-import")
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
        path: "/api/products?page=",
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
      await this.$store.dispatch("operations/searchData", {
        path: "/api/products/search",
        term: this.query,
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
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

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
    },

    imageLocation(img) {
      return window.location.origin + "/images/products/" + img
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
    downloadTemplate() {
      window.open("/product-import-template");
      // const { data } = axios.get(
      //   window.location.origin + '/api/product-import-template'
      // )
      // console.log(data)
    },
  },
};
</script>
