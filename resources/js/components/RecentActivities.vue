<template>
  <div
    v-if="
      (invoices && invoices.length) ||
      (purchases && purchases.length) ||
      (expenses && expenses.length) ||
      (transactions && transactions.length)
    "
    class="card"
  >
    <div class="card-header">
      <h3 class="card-title">{{ $t("Recent Activities") }}</h3>
    </div>
    <div class="card-body mb-3">
      <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
        <li class="nav-item">
          <a
            class="nav-link active"
            id="invoices-tab"
            data-toggle="pill"
            href="#invoices"
            role="tab"
            aria-controls="invoices-tab"
            aria-selected="true"
            >{{ $t("Invoices") }}</a
          >
        </li>
        <li class="nav-item">
          <a
            @click="getPurchases()"
            class="nav-link"
            id="purchases-tab"
            data-toggle="pill"
            href="#purchases"
            role="tab"
            aria-controls="purchases-tab"
            aria-selected="false"
            >{{ $t("Purchases") }}</a
          >
        </li>
        <li class="nav-item">
          <a
            @click="getExpenses()"
            class="nav-link"
            id="expenses-tab"
            data-toggle="pill"
            href="#expenses"
            role="tab"
            aria-controls="expenses-tab"
            aria-selected="false"
            >{{ $t("Expenses") }}</a
          >
        </li>
        <li class="nav-item">
          <a
            @click="getTransactions()"
            class="nav-link"
            id="transactions-tab"
            data-toggle="pill"
            href="#transactions"
            role="tab"
            aria-controls="transactions-tab"
            aria-selected="false"
            >{{ $t("Transactions") }}</a
          >
        </li>
      </ul>
      <div class="tab-content" id="custom-content-below-tabContent">
        <div
          class="tab-pane fade active show"
          id="invoices"
          role="tabpanel"
          aria-labelledby="invoices-tab"
        >
          <table-loading v-show="loading" />
          <div class="table-responsive table-custom">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>{{ $t("#") }}</th>
                  <th>{{ $t("Invoice No") }}</th>
                  <th>{{ $t("Invoice Date") }}</th>
                  <th>{{ $t("Client") }}</th>
                  <th>{{ $t("Subtotal") }}</th>
                  <th>{{ $t("Net Total") }}</th>
                  <th>{{ $t("Total Due") }}</th>
                  <th>{{ $t("Status") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(data, i) in invoices" :key="i">
                  <td>{{ ++i }}</td>
                  <td>
                    <router-link
                      :to="{
                        name: 'invoices.show',
                        params: { slug: data.slug },
                      }"
                    >
                      {{ data.invoiceNo | withPrefix(prefix) }}
                    </router-link>
                  </td>
                  <td>
                    <span v-if="data.invoiceDate">{{
                      data.invoiceDate | moment("Do MMM, YYYY")
                    }}</span>
                  </td>
                  <td>{{ data.client }}</td>
                  <td>{{ data.subTotal | withCurrency }}</td>
                  <td>{{ data.invoiceTotal | withCurrency }}</td>
                  <td>{{ data.due | withCurrency }}</td>
                  <td>
                    <span v-if="data.status === 1" class="badge bg-success">{{
                      $t("Active")
                    }}</span>
                    <span v-else class="badge bg-danger">{{
                      $t("Inactive")
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div
          class="tab-pane fade"
          id="purchases"
          role="tabpanel"
          aria-labelledby="purchases-tab"
        >
          <table-loading v-show="loading" />
          <div class="table-responsive table-custom">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>{{ $t("#") }}</th>
                  <th>{{ $t("Purchase No") }}</th>
                  <th>{{ $t("Date") }}</th>
                  <th>{{ $t("Supplier") }}</th>
                  <th>{{ $t("Subtotal") }}</th>
                  <th>{{ $t("Net Total") }}</th>
                  <th>{{ $t("Total Due") }}</th>
                  <th>{{ $t("Status") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(data, i) in purchases" :key="i">
                  <td>{{ ++i }}</td>
                  <td>
                    <router-link
                      :to="{
                        name: 'purchases.show',
                        params: { slug: data.slug },
                      }"
                    >
                      {{ data.code | withPrefix(prefix) }} </router-link
                    ><br />
                  </td>
                  <td>
                    <span v-if="data.purchaseDate">{{
                      data.purchaseDate | moment("Do MMM, YYYY")
                    }}</span>
                  </td>
                  <td>{{ data.supplierName }}</td>
                  <td>{{ data.subTotal | withCurrency }}</td>
                  <td>{{ data.purchaseTotal | withCurrency }}</td>
                  <td>{{ data.due | withCurrency }}</td>
                  <td>
                    <span v-if="data.status === 1" class="badge bg-success">{{
                      $t("Active")
                    }}</span>
                    <span v-else class="badge bg-danger">{{
                      $t("Inactive")
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div
          class="tab-pane fade"
          id="expenses"
          role="tabpanel"
          aria-labelledby="expenses-tab"
        >
          <table-loading v-show="loading" />
          <div class="table-responsive table-custom">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>{{ $t("#") }}</th>
                  <th>{{ $t("Sub Category") }}</th>
                  <th>{{ $t("Expense Reason") }}</th>
                  <th>{{ $t("Amount") }}</th>
                  <th>{{ $t("Account") }}</th>
                  <th>{{ $t("Date") }}</th>
                  <th>{{ $t("Status") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(data, i) in expenses" :key="i">
                  <td>{{ ++i }}</td>
                  <td>
                    <span v-if="data.subCategory">
                      {{ data.subCategory.name }} [{{
                        data.subCategory.code | withPrefix(subCatPrefix)
                      }}]
                    </span>
                  </td>
                  <td>
                    <router-link
                      :to="{
                        name: 'expenses.show',
                        params: { slug: data.slug },
                      }"
                    >
                      {{ data.reason }}
                    </router-link>
                  </td>
                  <td>
                    <span v-if="data.transaction">{{
                      data.transaction.amount | withCurrency
                    }}</span>
                  </td>
                  <td>
                    <span v-if="data.account">{{ data.account.label }} </span>
                  </td>
                  <td>
                    <span v-if="data.date">{{
                      data.date | moment("Do MMM, YYYY")
                    }}</span>
                  </td>
                  <td>
                    <span v-if="data.status === 1" class="badge bg-success">{{
                      $t("Active")
                    }}</span>
                    <span v-else class="badge bg-danger">{{
                      $t("Inactive")
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div
          class="tab-pane fade"
          id="transactions"
          role="tabpanel"
          aria-labelledby="transactions-tab"
        >
          <table-loading v-show="loading" />
          <div class="table-responsive table-custom">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>{{ $t("#") }}</th>
                  <th>{{ $t("Reason") }}</th>
                  <th>{{ $t("Date") }}</th>
                  <th>{{ $t("Type") }}</th>
                  <th>{{ $t("Account") }}</th>
                  <th>{{ $t("Amount") }}</th>
                  <th class="text-right">{{ $t("Status") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(data, i) in transactions" :key="i">
                  <td>{{ ++i }}</td>
                  <td>{{ data.reason }}</td>
                  <td>
                    <span v-if="data.transactionDate">{{
                      data.transactionDate | moment("Do MMM, YYYY")
                    }}</span>
                  </td>
                  <td>
                    <span v-if="data.type === 1" class="badge bg-success">{{
                      $t("Credit")
                    }}</span>
                    <span v-else class="badge bg-danger">{{
                      $t("Debit")
                    }}</span>
                  </td>
                  <td v-if="data.account">{{ data.account.label }}</td>
                  <td>{{ data.amount | withCurrency }}</td>
                  <td class="text-right">
                    <span v-if="data.status === 1" class="badge bg-success">{{
                      $t("Active")
                    }}</span>
                    <span v-else class="badge bg-danger">{{
                      $t("Inactive")
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  name: "RecentActivities",
  data: () => ({
    invoices: "",
    purchases: "",
    expenses: "",
    transactions: "",
    prefix: "",
    subCatPrefix: "",
    loading: false,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },
  created() {
    this.getInvoices();
    this.prefix = this.appInfo ? this.appInfo.productPrefix : "";
    this.subCatPrefix = this.appInfo.expSubCatPrefix;
  },
  methods: {
    // get invoices
    async getInvoices() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/recent-invoices"
      );
      this.invoices = data.data;
      this.loading = false;
    },
    // get purchases
    async getPurchases() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/recent-purchases"
      );
      this.purchases = data.data;
      this.loading = false;
    },
    // get expenses
    async getExpenses() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/recent-expenses"
      );
      this.expenses = data.data;
      this.loading = false;
    },
    // get transactions
    async getTransactions() {
      this.loading = true;
      const { data } = await axios.get(
        window.location.origin + "/api/dashboard/recent-transactions"
      );
      this.transactions = data.data;
      this.loading = false;
    },
  },
};
</script>
