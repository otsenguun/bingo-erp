<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row no-print">
      <div class="col-lg-12">
        <div class="card">
          <!-- form start -->
          <form role="form">
            <div class="card-body">
              <div class="row">
                <div v-if="items" class="form-group col-md-6">
                  <label for="category">{{ $t("Category Name") }}
                    <span class="required">*</span></label>
                  <v-select v-model="form.category" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('category') }" name="category"
                    :placeholder="$t('Select a category')" @input="getSubCategories" />
                  <has-error :form="form" field="category" />
                </div>
                <div v-if="items" class="form-group col-md-6">
                  <label for="subCategory">{{ $t("Sub Category Name")
                  }}<span class="required">*</span></label>
                  <v-select :disabled="form.category.id == 0" v-model="form.subCategory" :options="subCategories"
                    label="name" :class="{ 'is-invalid': form.errors.has('subCategory') }" name="subCategory"
                    :placeholder="$t('Select a category')" />
                  <has-error :form="form" field="subCategory" />
                </div>
              </div>
              <div class="col-12">
                <template :class="w - 100">
                  <date-range-picker :from="form.fromDate" :to="form.toDate" :panel="$route.query.panel"
                    @update="update" />
                </template>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="expenses && expenses.length > 0" class="row">
      <div class="col-lg-12">
        <div class="invoice p-3 mb-3">
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              <CompanyInfo />
            </div>
            <div class="col-sm-6 offset-sm-2 invoice-col float-right text-md-right">
              <h5>{{ $t("Expense Report") }}</h5>
              <strong>{{ $t("Date") }}:</strong>
              {{ date | moment("Do MMM, YYYY") }}<br />
              <strong>{{ $t("Category") }}:</strong>
              {{ form.category.name }}<br />
              <span v-if="form.subCategory"><strong>{{ $t("Sub Category") }}:</strong>
                {{ form.subCategory.name }}<br /></span>
              <strong>{{ $t("Date Range") }}:</strong>
              {{ form.fromDate | moment("Do MMM, YYYY") }} -
              {{ form.toDate | moment("Do MMM, YYYY") }} <br />
            </div>
          </div>
          <hr />
          <div class="row mt-5 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ $t("#") }}</th>
                    <th>{{ $t("Date") }}</th>
                    <th>{{ $t("Expense Reason") }}</th>
                    <th>{{ $t("Category") }}</th>
                    <th>{{ $t("Sub Category") }}</th>
                    <th>{{ $t("Amount") }}</th>
                    <th>{{ $t("Account") }}</th>
                    <th>{{ $t("Status") }}</th>
                    <th class="text-right">{{ $t("Created By") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(data, i) in expenses" :key="i">
                    <td>{{ ++i }}</td>
                    <td>
                      <span v-if="data.date">{{
                        data.date | moment("Do MMM, YYYY")
                      }}</span>
                    </td>
                    <td>{{ data.reason }}</td>
                    <td>
                      <span v-if="data.category">
                        {{ data.category.code | withPrefix(categoryPrefix) }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.subCategory">
                        {{
                          data.subCategory.code | withPrefix(subCategoryPrefix)
                        }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.transaction">{{
                        data.transaction.amount | withCurrency
                      }}</span>
                    </td>
                    <td>
                      <span v-if="data.account">
                        {{ data.account.accountNumber }}
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
                    <td class="text-right">{{ data.createdBy }}</td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-right">
                      <strong>{{ $t("Total") }}</strong>
                    </td>
                    <td colspan="4">
                      <strong>{{
                        calculateTotal(expenses) | withCurrency
                      }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- this row will not appear when printing -->
          <div class="row no-print mt-5">
            <div class="col-12">
              <router-link :to="{ name: 'inventory.index' }" class="btn btn-dark float-right">
                <i class="fas fa-long-arrow-alt-left" /> {{ $t("Back") }}
              </router-link>
              <a href="#" @click="printWindow" class="btn btn-default"><i class="fas fa-print"></i> {{ $t("Print")
              }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="expenses && expenses.length <= 0" class="row">
      <div class="col-lg-12 col-xl-10 offset-xl-1">
        <EmptyTable />
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import axios from "axios";
import { mapGetters } from "vuex";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Expense Report") };
  },
  data: () => ({
    breadcrumbsCurrent: "Expense Report",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Reports",
        url: "",
      },
      {
        name: "Expense Report",
        url: "",
      },
    ],
    form: new Form({
      fromDate: String(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)),
      toDate: String(new Date()),
      category: "",
      subCategory: "",
    }),
    subCategories: [],
    loading: false,
    expenses: "",
    date: new Date(),
    categoryPrefix: "",
    subCategoryPrefix: "",
  }),

  computed: {
    ...mapGetters("operations", ["items", "appInfo"]),
  },

  created() {
    this.getCatgories();
    this.categoryPrefix = this.appInfo.expCatPrefix;
    this.subCategoryPrefix = this.appInfo.expSubCatPrefix;
  },
  methods: {
    // get all categories
    async getCatgories() {
      await this.$store.dispatch("operations/allData", {
        path: "/api/all-expense-categories",
      });
      this.items.unshift({
        id: 0,
        name: "All Categories",
      });
    },

    // get sub categories for a category
    async getSubCategories() {
      this.subCategories = [];
      this.form.subCategory = "";
      if (this.form.category.id != 0) {
        let slug = this.form.category.slug;
        const { data } = await axios.get(
          window.location.origin + "/api/sub-categories-by-category/" + slug
        );
        this.subCategories = data.data;
        if (this.subCategories.length > 0) {
          this.subCategories.unshift({
            id: 0,
            name: "All Sub Categories",
          });
        }
      }
    },

    // get filtered data
    async update(values) {
      this.loading = true;
      this.form.fromDate = values.from;
      this.form.toDate = values.to;
      await this.form
        .post(window.location.origin + "/api/reports/expenses")
        .then((response) => {
          this.expenses = response.data.data;
          this.loading = false;
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("There was something wrong.") });
        });
    },

    // calculate total
    calculateTotal(expenses) {
      let total = expenses.reduce(
        (accumulator, current) =>
          Number(accumulator) + Number(current.transaction.amount),
        0
      );
      return total;
    },

    // print
    printWindow() {
      window.print();
    },
  },
};
</script>
