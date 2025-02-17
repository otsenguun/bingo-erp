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
                {{ $t("All Subscriptions") }}
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
                    <th>{{ $t('ID') }}</th>
                    <th>{{ $t('Tenant') }}</th>
                    <th>{{ $t('Transaction ID') }}</th>
                    <th>{{ $t('Document Path') }}</th>
                    <th>{{ $t('Plan Name') }}</th>
                    <th>{{ $t('Plan Price') }}</th>
                    <th>{{ $t('Quantity') }}</th>
                    <th>{{ $t('Status') }}</th>
                    <th>{{ $t('Updated By') }}</th>
                    <th>{{ $t('Created At') }}</th>
                    <th class="text-right no-print">
                      {{ $t("Action") }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(subscriptionRequest, i) in items" :key="i">
                    <td>{{ subscriptionRequest.id }}</td>
                    <td v-if="subscriptionRequest.tenant">
                      <a :href="subscriptionRequest.tenant.domain_url">
                        {{ subscriptionRequest.tenant.name }}
                      </a>
                    </td>
                    <td>{{ subscriptionRequest.transaction_id ?? $t('Not Available') }}</td>
                    <td>
                      <a v-if="subscriptionRequest.document_path" :href="subscriptionRequest.document_url"
                        target="_blank">
                        {{ $t('Download') }}
                      </a>
                      <div v-else>
                        {{ $t('Not Available') }}
                      </div>
                    </td>
                    <td>{{ subscriptionRequest.plan.name }}</td>
                    <td>{{ subscriptionRequest.plan.amount | withCurrency}}</td>
                    <td>{{ subscriptionRequest.quantity }}</td>
                    <td v-html="subscriptionRequest.status_html" class="text-center"></td>
                    <td>
                      <span v-if="subscriptionRequest.status_updated_by"
                        v-tooltip="subscriptionRequest.status_updated_by.email">
                        {{ subscriptionRequest.status_updated_by.name }}
                      </span>
                      <span v-else>
                        {{ $t('Not updated yet!') }}
                      </span>
                    </td>
                    <td>{{ subscriptionRequest.created_at | moment('Do MMM, YYYY') }}</td>
                    <td class="text-right no-print">
                      <div v-if="subscriptionRequest.id" class="btn-group">
                        <div class="dropdown show">
                          <a class="btn btn-secondary dropdown-toggle" href="!#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $t("Action") }}
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <button :disabled="subscriptionRequest.status === STATUS_ACCEPTED" v-tooltip="$t('Accepted')"
                              href="#" class="btn btn-success btn-sm dropdown-item"
                              @click.prevent="update(subscriptionRequest.id, STATUS_ACCEPTED)">
                              <i class="fas fa-link" />
                              {{ $t("Accepted") }}
                            </button>

                            <button
                              :disabled="subscriptionRequest.status === STATUS_REJECTED || subscriptionRequest.status === STATUS_ACCEPTED"
                              v-tooltip="$t('Rejected')" href="#" class="btn btn-danger btn-sm dropdown-item"
                              @click.prevent="update(subscriptionRequest.id, STATUS_REJECTED)">
                              <i class="fas fa-times" />
                              {{ $t("Rejected") }}
                            </button>

                            <button :disabled="subscriptionRequest.status !== STATUS_PENDING"
                              v-tooltip="$t('Delete')" href="#" class="btn btn-danger btn-sm dropdown-item"
                              @click.prevent="deleteData(subscriptionRequest.id)">
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
  middleware: ["auth"],
  metaInfo() {
    return { title: this.$t("All Subscription Requests") };
  },
  data: () => ({
    STATUS_PENDING: 0,
    STATUS_ACCEPTED: 1,
    STATUS_REJECTED: 2,

    breadcrumbsCurrent: "All Subscription Requests",
    breadcrumbs: [
      {
        name: "Subscriptions",
        url: "home",
      },
      {
        name: "All Subscription Requests",
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
        path: "/api/subscription-requests?page=",
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

    // refresh table
    refreshTable() {
      this.query = "";
      this.query === "" ? this.getData() : this.searchData();
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
            .patch("/api/subscription-requests/" + slug, {
              status: status,
            })
            .then(() => {
              Swal.fire(
                this.$t("Updated successfully!"),
                this.$t("Updated successfully."),
                "success"
              );
              this.getData();
              location.reload();
            })
            .catch((e) => {
              Swal.fire(
                this.$t("Failed!"),
                this.$t("There was something wrong."),
                "warning"
              );

              console.log(e)
            });
        }
      });
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("Are you sure?"),
        text: this.$t("subscription-requests.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("Confirm"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/subscription-requests/",
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
                  this.$t("subscription-requests.index.delete_failed"),
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
