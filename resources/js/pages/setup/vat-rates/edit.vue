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
        <form role="form" @submit.prevent="updateVATRate" @keydown="form.onKeydown($event)">
          <div class="card">
            <div class="card-header setings-header">
              <div class="col-xl-4 col-4">
                <h3 class="card-title">
                  {{ $t("Edit Tax rate") }}
                </h3>
              </div>
              <div class="col-xl-8 col-8 float-right text-right">
                <router-link :to="{ name: 'vatRates.index' }" class="btn btn-dark float-right">
                  <i class="fas fa-long-arrow-alt-left" />
                  {{ $t("Back") }}
                </router-link>
              </div>
            </div>
            <div class="card-body">

              <div class="form-group">
                <label for="isGroupTax">{{ $t("Is Group Tax?") }}</label>
                <select
                  id="isGroupTax"
                  v-model="form.isGroupTax"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('isGroupTax') }"
                >
                  <option value="1">{{ $t("Yes") }}</option>
                  <option value="0">{{ $t("No") }}</option>
                </select>
                <has-error :form="form" field="isGroupTax" />
              </div>

              <div class="form-group">
                <label for="name">{{ $t("Name") }}
                  <span class="required">*</span></label>
                <input id="name" v-model="form.name" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                  :placeholder="$t('Enter a name')" />
                <has-error :form="form" field="name" />
              </div>
              <div class="form-group">
                <label for="code">{{ $t("Short Code") }}
                  <span class="required">*</span></label>
                <input id="code" v-model="form.code" type="text" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('code') }" name="code"
                  :placeholder="$t('Enter a code')" />
                <has-error :form="form" field="code" />
              </div>

              <div v-if="form.isGroupTax == true">
                <div v-if="taxes" class="form-group">
                  <label for="groupTaxItem"
                    >{{ $t("Select Tax") }}
                    <span class="required">*</span></label
                  >
                  <v-select
                    multiple
                    v-model="form.groupTaxItem"
                    :options="taxes"
                    label="code"
                    :class="{ 'is-invalid': form.errors.has('groupTaxItem') }"
                    name="groupTaxItem"
                    :placeholder="$t('Select a tax')"
                  />
                  <has-error :form="form" field="groupTaxItem" />
                </div>
              </div>

              <div class="form-group">
                <label for="rate">{{ $t("Rate(Calculated in %)") }}
                  <span class="required">*</span></label>
                <input id="rate" v-model="form.rate" type="number" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('rate') }" name="rate"
                  :placeholder="$t('Enter a rate')" :disabled="form.isGroupTax == true" />
                <has-error :form="form" field="rate" />
              </div>

              <div class="form-group">
                <label for="status">{{ $t("Status") }}</label>
                <select id="status" v-model="form.status" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('status') }">
                  <option value="1">{{ $t("Active") }}</option>
                  <option value="0">{{ $t("Inactive") }}</option>
                </select>
                <has-error :form="form" field="status" />
              </div>

              <div class="form-group">
                <label for="note">{{ $t("Note") }}</label>
                <textarea id="note" v-model="form.note" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Write your note here!')" />
                <has-error :form="form" field="note" />
              </div>
            </div>
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t("Save changes") }}
              </v-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Form from "vform";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("Edit Tax Rate") };
  },

  watch: {
    "form.groupTaxItem": {
      handler(newVal) {
        if (!this.initialLoad && this.form.isGroupTax) {
          this.calculateGroupTaxRate();
        }
      },
      deep: true,
    },
    "form.isGroupTax": {
      handler(newVal) {
        if (!this.initialLoad) {
          if (newVal) {
            this.form.rate = "";
            this.form.groupTaxItem = [];
          }
        }else{
          this.initialLoad = false;
        }
      },
    },
  },

  data: () => ({
    breadcrumbsCurrent: "Edit Tax Rate",
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
        name: "Vat Rates",
        url: "vatRates.index",
      },
      {
        name: "Edit",
        url: "",
      },
    ],
    form: new Form({
      name: "",
      note: "",
      groupTaxItem: [],
      isGroupTax: "",
      code: "",
      rate: "",
      status: 1,
    }),
    taxes: [],
    initialLoad: true,
    loading: true,
  }),

  mounted() {
    this.getTaxes();
  },
  methods: {
    // get category
    async getVATRate() {
      const { data } = await axios.get(
        window.location.origin + "/api/vat-rates/" + this.$route.params.slug
      );
      this.form.name = data.data.name;
      this.form.code = data.data.code;
      this.form.rate = data.data.rate;
      this.form.note = data.data.note;
      this.form.isGroupTax = data.data.isGroupTax;
      this.form.status = data.data.status;

      if (data.data.isGroupTax && data.data.groupTaxIds) {
          this.form.groupTaxItem = data.data.groupTaxIds.map(id => {
            return this.taxes.find(tax => tax.id === id);
          });
      }
    },

    // get all taxes
    async getTaxes() {
      const { data } = await axios.get(
        window.location.origin + "/api/all-vat-rates"
      );
      this.taxes = data.data;
      this.getVATRate();
    },

    calculateGroupTaxRate() {
      if (this.form.groupTaxItem.length > 0) {
        this.form.rate = this.form.groupTaxItem.reduce((total, tax) => {
          return total + tax.rate;
        }, 0);
      } else {
        this.form.rate = "";
      }
    },

    // update category
    async updateVATRate() {
      await this.form
        .patch(
          window.location.origin + "/api/vat-rates/" + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Tax Rate updated successfully"),
          });
          this.$router.push({ name: "vatRates.index" });
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("Opps...something went wrong"),
          });
        });
    },
  },
};
</script>
