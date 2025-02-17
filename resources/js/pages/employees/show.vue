<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row no-print mb-2">
      <div class="w-100 text-right float-right">
        <div class="d-flex justify-content-between" v-if="allData">
          <div class="btn-group">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a
                  class="nav-link active"
                  href="#details"
                  data-toggle="tab"
                  @click="loadInitialData"
                >
                  <i class="fa fa-info"></i>
                  {{ $t("Details") }}</a
                >
              </li>
              <li class="nav-item">
                <a
                  @click="getActivity"
                  class="nav-link"
                  href="#activity-log"
                  data-toggle="tab"
                >
                  <i class="nav-icon fa fa-bell" aria-hidden="true"></i>
                  {{ $t("Activity log") }}</a
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content">
      <div class="tab-pane active" id="details">
        <div class="row">
          <div
            class="col-md-12 m-auto"
            :class="
              !$can('payroll-list') && !$can('increment-list')
                ? 'col-lg-6'
                : 'col-lg-3'
            "
          >
            <!-- Profile Image -->
            <div class="card">
              <div class="card-body box-profile">
                <div class="text-center mb-2">
                  <a
                    v-if="allData.image"
                    href="#"
                    id="show-modal"
                    @click="previewModal(allData.image)"
                  >
                    <img
                      :src="allData.image"
                      class="profile-user-img img-fluid img-circle"
                      loading="lazy"
                    />
                  </a>
                  <div v-else class="bg-secondary no-preview-lg">
                    <small>{{ $t("No Preview") }}</small>
                  </div>
                </div>
                <h3 class="profile-username text-center">{{ allData.name }}</h3>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <strong>{{ $t("Emp ID") }}</strong>
                    <span class="float-right">{{
                      allData.empID | withPrefix(employeePrefix)
                    }}</span>
                  </li>
                  <li v-if="allData.department" class="list-group-item">
                    <strong>{{ $t("Department") }}</strong>
                    <span class="float-right">{{
                      allData.department.name
                    }}</span>
                  </li>
                  <li v-if="allData.designation" class="list-group-item">
                    <strong>{{ $t("Designation") }}</strong>
                    <span class="float-right">{{ allData.designation }}</span>
                  </li>
                  <li v-if="allData.mobileNumber" class="list-group-item">
                    <strong>{{ $t("Contact Number") }}</strong>
                    <span class="float-right">{{ allData.mobileNumber }}</span>
                  </li>
                  <li v-if="allData.email" class="list-group-item">
                    <strong>{{ $t("Email") }}</strong>
                    <span class="float-right">{{ allData.email }}</span>
                  </li>
                  <li v-if="allData.salary" class="list-group-item">
                    <strong>{{ $t("Basic Salary") }}</strong>
                    <span class="float-right">{{
                      allData.salary | withCurrency
                    }}</span>
                  </li>
                  <li v-if="allData.totalSalary" class="list-group-item">
                    <strong>{{ $t("Current Salary") }}</strong>
                    <span class="float-right">{{
                      allData.totalSalary | withCurrency
                    }}</span>
                  </li>
                  <li v-if="allData.commission" class="list-group-item">
                    <strong>{{ $t("Commission") }}</strong>
                    <span class="float-right">{{ allData.commission }}%</span>
                  </li>

                  <li v-if="allData.gender" class="list-group-item">
                    <strong>{{ $t("Gender") }}</strong>
                    <span class="float-right">{{ allData.gender }}</span>
                  </li>
                  <li v-if="allData.bloodGroup" class="list-group-item">
                    <strong>{{ $t("Blood Group") }}</strong>
                    <span class="float-right">{{ allData.bloodGroup }}</span>
                  </li>
                  <li v-if="allData.religion" class="list-group-item">
                    <strong>{{ $t("Religion") }}</strong>
                    <span class="float-right">{{ allData.religion }}</span>
                  </li>
                  <li v-if="allData.birthDate" class="list-group-item">
                    <strong>{{ $t("Birth Date") }}</strong>
                    <span class="float-right">{{
                      allData.birthDate | moment("Do MMM, YYYY")
                    }}</span>
                  </li>
                  <li v-if="allData.joiningDate" class="list-group-item">
                    <strong>{{ $t("Join Date") }}</strong>
                    <span class="float-right">{{
                      allData.joiningDate | moment("Do MMM, YYYY")
                    }}</span>
                  </li>
                  <li v-if="allData.appointmentDate" class="list-group-item">
                    <strong>{{
                      $t("Appointment Date")
                    }}</strong>
                    <span class="float-right">{{
                      allData.appointmentDate | moment("Do MMM, YYYY")
                    }}</span>
                  </li>
                  <li v-if="allData.address" class="list-group-item">
                    <strong>{{ $t("Address") }}</strong>
                    <span class="float-right">{{ allData.address }}</span>
                  </li>
                  <li class="list-group-item">
                    <strong>{{ $t("Allow Employee Login") }}</strong>
                    <span v-if="allData.user" class="float-right">{{
                      $t("Yes")
                    }}</span>
                    <span v-else class="float-right">{{
                      $t("No")
                    }}</span>
                  </li>
                  <li
                    v-if="allData.user && allData.user.role"
                    class="list-group-item"
                  >
                    <strong>{{ $t("Role") }}</strong>
                    <span class="float-right">{{
                      allData.user.role.name
                    }}</span>
                  </li>
                </ul>
                <span
                  v-if="allData.status === 1"
                  class="btn-block btn bg-success"
                  >{{ $t("Active") }}</span
                >
                <span v-else class="badge bg-danger">{{
                  $t("Inactive")
                }}</span>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div
            v-if="$can('payroll-list') || $can('increment-list')"
            class="col-md-12 col-lg-9"
          >
            <div class="card">
              <div class="card-header p-2">
                <div class="row">
                  <div class="col-md-8">
                    <ul class="nav nav-pills">
                      <li v-if="$can('payroll-list')" class="nav-item">
                        <a
                          class="nav-link active"
                          href="#payroll"
                          data-toggle="tab"
                          >{{ $t("Payroll") }}</a
                        >
                      </li>
                      <li v-if="$can('increment-list')" class="nav-item">
                        <a
                          @click="getEmployeeSalIncrements"
                          class="nav-link"
                          href="#increment"
                          data-toggle="tab"
                          >{{ $t("Increment History") }}</a
                        >
                      </li>
                    </ul>
                  </div>
                  <div class="col-md-4">
                    <router-link
                      :to="{ name: 'employees.index' }"
                      class="btn btn-dark float-right"
                    >
                      <i class="fas fa-long-arrow-alt-left" />
                      {{ $t("Back") }}
                    </router-link>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div
                    v-if="$can('payroll-list')"
                    class="tab-pane active"
                    id="payroll"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
                        <div class="col-md-12 large-serach-box">
                          <search
                            v-model="query"
                            @reset-pagination="resetPagination"
                            @reload="reload"
                          />
                        </div>
                        <table-loading v-show="loading" />
                        <div class="table-responsive table-custom mt-3">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>{{ $t("#") }}</th>
                                <th>{{ $t("Salary Month") }}</th>
                                <th>{{ $t("Salary Date") }}</th>
                                <th>{{ $t("Account") }}</th>
                                <th>{{ $t("Total Paid") }}</th>
                                <th>{{ $t("Status") }}</th>
                                <th
                                  v-if="
                                    $can('payroll-edit') ||
                                    $can('payroll-view') ||
                                    $can('payroll-delete')
                                  "
                                  class="text-right no-print"
                                >
                                  {{ $t("Action") }}
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-show="items.length"
                                v-for="(data, i) in items"
                                :key="i"
                              >
                                <td>
                                  <span v-if="pagination.current_page > 1">
                                    {{
                                      pagination.per_page *
                                        (pagination.current_page - 1) +
                                      (i + 1)
                                    }}
                                  </span>
                                  <span v-else>{{ i + 1 }}</span>
                                </td>
                                <td>{{ data.salaryMonth }}</td>
                                <td>
                                  <span v-if="data.salaryDate">{{
                                    data.salaryDate | moment("Do MMM, YYYY")
                                  }}</span>
                                </td>
                                <td>
                                  <span
                                    v-if="
                                      data.transaction &&
                                      data.transaction.cashbook_account
                                    "
                                    >{{
                                      data.transaction.cashbook_account
                                        .account_number
                                    }}</span
                                  >
                                </td>
                                <td>
                                  <span v-if="data.transaction">{{
                                    data.transaction.amount | withCurrency
                                  }}</span>
                                </td>
                                <td>
                                  <span
                                    v-if="data.status === 1"
                                    class="badge bg-success"
                                    >{{ $t("Active") }}</span
                                  >
                                  <span v-else class="badge bg-danger">{{
                                    $t("Inactive")
                                  }}</span>
                                </td>
                                <td
                                  v-if="
                                    $can('payroll-edit') ||
                                    $can('payroll-view') ||
                                    $can('payroll-delete')
                                  "
                                  class="text-right"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('payroll-view')"
                                      v-tooltip="$t('View')"
                                      :to="{
                                        name: 'payroll.show',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-primary btn-sm"
                                    >
                                      <i class="fas fa-eye" />
                                    </router-link>
                                    <router-link
                                      v-if="$can('payroll-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'payroll.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('payroll-delete')"
                                      v-tooltip="$t('Delete')"
                                      href="#"
                                      class="btn btn-danger btn-sm"
                                      @click="deletePayroll(data.slug)"
                                    >
                                      <i class="fas fa-trash" />
                                    </a>
                                  </div>
                                </td>
                              </tr>
                              <tr v-show="!loading && !items.length">
                                <td colspan="8">
                                  <EmptyTable />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- NEW PAGINATION -->
                      <div
                        v-if="pagination && pagination.total > 0"
                        class="dtable-footer"
                      >
                        <div class="form-group row display-per-page">
                          <label>{{ $t("per_page") }} </label>
                          <div>
                            <select
                              @change="updatePerPager('payroll')"
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="option in options"
                                :value="option.value"
                              >
                                {{ option.text }}
                              </option>
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
                      <!-- NEW PAGINATION END -->
                    </div>
                  </div>
                  <div
                    v-if="$can('increment-list')"
                    class="tab-pane"
                    id="increment"
                  >
                    <div>
                      <div class="card-body p-0 position-relative">
                        <div class="col-md-12 large-serach-box">
                          <search
                            v-model="salIncreQuery"
                            @reset-pagination="resetSalIncrePagination"
                            @reload="salIncreReload"
                          />
                        </div>
                        <table-loading v-show="salIncreLoading" />
                        <div class="table-responsive table-custom mt-3">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>{{ $t("#") }}</th>
                                <th>
                                  {{ $t("Increment Reason") }}
                                </th>
                                <th>
                                  {{ $t("Basic Salary") }}
                                </th>
                                <th>
                                  {{ $t("Increment Amount") }}
                                </th>
                                <th>
                                  {{ $t("Present Salary") }}
                                </th>
                                <th>
                                  {{ $t("Increment Date") }}
                                </th>
                                <th>{{ $t("Status") }}</th>
                                <th
                                  v-if="
                                    $can('increment-edit') ||
                                    $can('increment-view') ||
                                    $can('increment-delete')
                                  "
                                  class="text-right no-print"
                                >
                                  {{ $t("Action") }}
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-show="allIncrements.length"
                                v-for="(data, i) in allIncrements"
                                :key="i"
                              >
                                <td>
                                  <span
                                    v-if="
                                      salIncrePagination &&
                                      salIncrePagination.current_page > 1
                                    "
                                  >
                                    {{
                                      salIncrePagination.per_page *
                                        (salIncrePagination.current_page - 1) +
                                      (i + 1)
                                    }}
                                  </span>
                                  <span v-else>{{ i + 1 }}</span>
                                </td>
                                <td>
                                  <router-link
                                    :to="{
                                      name: 'increments.show',
                                      params: { slug: data.slug },
                                    }"
                                  >
                                    {{ data.reason }}
                                  </router-link>
                                </td>
                                <td>
                                  <span v-if="data.employee"
                                    >{{ data.employee.salary | withCurrency }}
                                  </span>
                                </td>
                                <td>
                                  {{ data.incrementAmount | withCurrency }}
                                </td>
                                <td>
                                  <span v-if="data.employee">
                                    {{
                                      (data.employee.salary +
                                        data.incrementAmount)
                                        | withCurrency
                                    }}
                                  </span>
                                </td>
                                <td>
                                  <span v-if="data.incrementDate">{{
                                    data.incrementDate | moment("Do MMM, YYYY")
                                  }}</span>
                                </td>
                                <td>
                                  <span
                                    v-if="data.status === 1"
                                    class="badge bg-success"
                                    >{{ $t("Active") }}</span
                                  >
                                  <span v-else class="badge bg-danger">{{
                                    $t("Inactive")
                                  }}</span>
                                </td>
                                <td
                                  v-if="
                                    $can('increment-edit') ||
                                    $can('increment-view') ||
                                    $can('increment-delete')
                                  "
                                  class="text-right"
                                >
                                  <div class="btn-group">
                                    <router-link
                                      v-if="$can('increment-view')"
                                      v-tooltip="$t('View')"
                                      :to="{
                                        name: 'increments.show',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-primary btn-sm"
                                    >
                                      <i class="fas fa-eye" />
                                    </router-link>
                                    <router-link
                                      v-if="$can('increment-edit')"
                                      v-tooltip="$t('Edit')"
                                      :to="{
                                        name: 'increments.edit',
                                        params: { slug: data.slug },
                                      }"
                                      class="btn btn-info btn-sm"
                                    >
                                      <i class="fas fa-edit" />
                                    </router-link>
                                    <a
                                      v-if="$can('increment-delete')"
                                      v-tooltip="$t('Delete')"
                                      href="#"
                                      class="btn btn-danger btn-sm"
                                      @click="deleteIncrement(data.slug)"
                                    >
                                      <i class="fas fa-trash" />
                                    </a>
                                  </div>
                                </td>
                              </tr>
                              <tr v-show="!loading && !allIncrements.length">
                                <td colspan="8">
                                  <EmptyTable />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!--                  <div
                    v-if="salIncrePagination.last_page > 1"
                    class="mt-3 clearfix"
                  >
                    <pagination
                      :pagination="salIncrePagination"
                      :offset="5"
                      class="justify-flex-end"
                      @paginate="salIncrePaginate"
                    />
                  </div>-->
                      <!-- NEW PAGINATION -->
                      <div
                        v-if="
                          salIncrePagination && salIncrePagination.total > 0
                        "
                        class="dtable-footer"
                      >
                        <div class="form-group row display-per-page">
                          <label>{{ $t("per_page") }} </label>
                          <div>
                            <select
                              @change="updatePerPager('increment-history')"
                              v-model="perPage"
                              class="form-control form-control-sm ml-1"
                            >
                              <!-- options component -->
                              <option
                                v-for="option in options"
                                :value="option.value"
                              >
                                {{ option.text }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <!-- pagination-start -->
                        <pagination
                          v-if="
                            salIncrePagination &&
                            salIncrePagination.last_page > 1
                          "
                          :pagination="salIncrePagination"
                          :offset="5"
                          class="justify-flex-end"
                          @paginate="salIncrePaginate"
                        />
                        <!-- pagination-end -->
                      </div>
                      <!-- NEW PAGINATION END -->
                    </div>
                  </div>
                </div>
                <!-- /.tab-content -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
      </div>

      <!--  activity logs -->
      <div class="tab-pane" id="activity-log">
        <div class="card custom-card w-100 mt-5 no-print">
          <div class="card-header setings-header">
            <div class="col-xl-4 col-4">
              <h3 class="card-title">
                {{ $t("Activity log") }}
              </h3>
            </div>
            <div class="col-xl-8 col-8 float-right text-right">
              <div class="btn-group c-w-100">
                <a
                  @click="refreshActivityTable()"
                  href="#"
                  v-tooltip="'Refresh'"
                  class="btn btn-success"
                >
                  <i class="fas fa-sync"></i>
                </a>
                <a
                  @click="printActivityLog"
                  v-tooltip="$t('Print Table')"
                  class="btn btn-info"
                >
                  <i class="fas fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <table-loading v-show="activityLoading" />
          <div class="card-body position-relative">
            <div class="row">
              <div class="col-6 col-xl-4 mb-2">
                <search
                  v-model="activitySearchQuery"
                  @reset-pagination="resetActivityPagination()"
                  @reload="activityReload"
                />
              </div>
            </div>
            <div id="printMe" class="table-responsive table-custom mt-3">
              <div
                v-show="allActivityLog.length > 0"
                v-for="(data, i) in allActivityLog"
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
                        <div class="row">
                          <div class="col-12">
                            <p class="text-bold mb-0">{{ data.causer_name }}</p>
                          </div>
                          <div class="col-12">
                            <p class="mb-0">{{ data.description }}</p>
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
              <div
                class="text-center"
                v-show="!activityLoading && !allActivityLog.length"
              >
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
                    @change="updateActivityPerPager"
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
                v-if="
                  allActivityLogPagination &&
                  allActivityLogPagination.last_page > 1
                "
                :pagination="
                  allActivityLog
                    ? allActivityLogPagination
                    : { current_page: 1 }
                "
                :offset="5"
                class="justify-flex-end"
                @paginate="activityPaginate"
              />
              <!-- pagination-end -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- use the modal component, pass in the prop -->
    <Modal v-if="showModal" @close="previewModal()">
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
    return { title: this.$t("Employee Details") };
  },
  data: () => ({
    breadcrumbsCurrent: "Employee Details",
    breadcrumbs: [
      {
        name: "Dashboard",
        url: "home",
      },
      {
        name: "Employees",
        url: "employees.index",
      },
      {
        name: "Details",
        url: "",
      },
    ],
    allIncrements: "",
    salIncreLoading: false,
    salIncrePagination: "",
    url: null,
    showModal: false,
    allData: "",
    query: "",
    salIncreQuery: "",
    employeePrefix: "",
    activitySearchQuery: "",
    allActivityLog: "",
    allActivityLogPagination: "",
    activityLoading: false,
    perPage: 10,
    options: [
      { value: "10", text: "10" },
      { value: "25", text: "25" },
      { value: "50", text: "50" },
      { value: "100", text: "100" },
    ],
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
  },
  watch: {
    // watch invoice search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        this.getEmployeePayroll();
      } else {
        this.searchEmployeePayroll();
      }
    },
    // watch salary increment search data
    salIncreQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getEmployeeSalIncrements();
      } else {
        this.searchEmployeeSalIncrements();
      }
    },

    // watch activitySearchQuery data
    activitySearchQuery: function (newQ, oldQ) {
      if (newQ === "") {
        this.getActivity();
      } else {
        this.searchActivity();
      }
    },
  },

  created() {
    this.getEmployee();
    this.getEmployeePayroll();
    this.employeePrefix = this.appInfo.employeePrefix;
    Fire.$on("AfterDelete", () => {
      this.getEmployeePayroll();
      this.getEmployeeSalIncrements();
    });
  },
  methods: {
    async loadInitialData() {
      this.getEmployee();
      this.getEmployeePayroll();
    },

    // get the employee
    async getEmployee() {
      const { data } = await axios.get(
        window.location.origin + "/api/employees/" + this.$route.params.slug
      );
      this.allData = data.data;
    },

    // update per page count
    updatePerPager(tabName) {
      this.pagination.current_page = 1;
      this.salIncrePagination.hasOwnProperty("current_page")
        ? (this.salIncrePagination.current_page = 1)
        : "";

      switch (tabName) {
        case "payroll":
          this.query === ""
            ? this.getEmployeePayroll()
            : this.searchEmployeePayroll();
          break;
        case "increment-history":
          this.query === ""
            ? this.getEmployeeSalIncrements()
            : this.searchEmployeeSalIncrements();
          break;
      }
    },

    // get the employee payroll
    async getEmployeePayroll() {
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/employee-payroll/" + this.$route.params.slug + "?page=",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
      });
    },

    // search employee payroll
    async searchEmployeePayroll() {
      this.$store.state.operations.loading = true;
      await this.$store.dispatch("operations/searchData", {
        term: this.query,
        path: "/api/employee-payroll/" + this.$route.params.slug + "/search/",
        currentPage: this.pagination.current_page + "&perPage=" + this.perPage,
      });
    },

    // pagination
    async paginate() {
      this.query === ""
        ? this.getEmployeePayroll()
        : this.searchEmployeePayroll();
    },

    // reset purchase pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // reload purchases after search
    async reload() {
      this.query = "";
    },

    // get the employee salary increments
    async getEmployeeSalIncrements() {
      this.salIncreLoading = true;
      let currentPage = this.allIncrements
        ? this.salIncrePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/employee-increments/" +
          this.$route.params.slug +
          "?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allIncrements = data.data;
      this.salIncrePagination = data.meta;
      this.salIncreLoading = false;
    },

    // search employee salary increments
    async searchEmployeeSalIncrements() {
      this.salIncreLoading = true;
      let currentPage = this.allIncrements
        ? this.salIncrePagination.current_page
        : 1;
      const { data } = await axios.get(
        window.location.origin +
          "/api/employee-increments/" +
          this.$route.params.slug +
          "/search/" +
          this.salIncreQuery +
          "?page=" +
          currentPage +
          "&perPage=" +
          this.perPage
      );
      this.allIncrements = data.data;
      this.salIncrePagination = data.meta;
      this.salIncreLoading = false;
    },

    // salary increments pagination
    async salIncrePaginate() {
      this.salIncreQuery === ""
        ? this.getEmployeeSalIncrements()
        : this.searchEmployeeSalIncrements();
    },

    // reset increments pagination
    async resetSalIncrePagination() {
      this.salIncrePagination.current_page = 1;
    },

    // Reload increments after search
    async salIncreReload() {
      this.salIncreQuery = "";
    },

    // print
    printWindow() {
      window.print();
    },

    // print table
    async printActivityLog() {
      await this.$htmlToPaper("printMe");
    },

    // get activity logs
    async getActivity() {
      this.activityLoading = true;
      let currentPage = this.allActivityLog
        ? this.allActivityLogPagination.current_page
        : 1;
      let slug = this.$route.params.slug;
      let modelName = "Employee";

      const baseUrl = `${window.location.origin}/api/activity-log-specific`;
      const queryParams = `?page=${currentPage}&perPage=${this.perPage}&slug=${slug}&modelName=${modelName}`;
      const url = baseUrl + queryParams;

      const { data } = await axios.get(url);

      this.allActivityLog = data.data;
      this.allActivityLogPagination = data.meta;
      this.activityLoading = false;
    },

    // search data
    async searchActivity() {
      this.activityLoading = true;
      let currentPage = this.allActivityLog
        ? this.allActivityLogPagination.current_page
        : 1;
      let term = this.activitySearchQuery;
      let slug = this.$route.params.slug;
      let modelName = "Employee";

      const baseUrl = `${window.location.origin}/api/activity-log-specific`;
      const queryParams = `?page=${currentPage}&perPage=${this.perPage}&term=${term}&slug=${slug}&modelName=${modelName}`;
      const url = baseUrl + queryParams;

      const { data } = await axios.get(url);

      this.allActivityLog = data.data;
      this.allActivityLogPagination = data.meta;
      this.activityLoading = false;
    },

    // activity pagination
    async activityPaginate() {
      this.getActivity();
    },

    // update activity per page count
    updateActivityPerPager() {
      this.allActivityLogPagination.current_page = 1;
      this.activitySearchQuery === ""
        ? this.getActivity()
        : this.searchActivity();
    },

    // reload after search
    async activityReload() {
      this.activitySearchQuery = "";
    },

    // refresh activity table
    refreshActivityTable() {
      this.activitySearchQuery = "";
      this.activitySearchQuery === ""
        ? this.getActivity()
        : this.searchActivity();
    },

    // reset activity pagination
    async resetActivityPagination() {
      this.allActivityLogPagination.current_page = 1;
    },

    // dispaly modal
    previewModal(image) {
      this.imagePath = image;
      if (this.showModal) {
        return (this.showModal = false);
      }
      return (this.showModal = true);
    },

    // calcualte total paid
    calculateTotalPaid() {
      let totalPaid = 0;
      this.allData.loans.forEach(function (loan) {
        totalPaid += Number(loan.totalPaid);
      });
      return totalPaid;
    },

    // delete payroll
    async deletePayroll(slug) {
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
              path: "/api/payroll/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                Fire.$emit("AfterDelete");
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

    // delete increment
    async deleteIncrement(slug) {
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
              path: "/api/increments/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("Deleted!"),
                  this.$t("Deleted successfully."),
                  "success"
                );
                Fire.$emit("AfterDelete");
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
  },
};
</script>

<style scoped>
.nav-pills .nav-item {
  background: #ddd;
  margin: 2px;
  border-radius: 0.25rem;
}
</style>
