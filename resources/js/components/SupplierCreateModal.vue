<template>
  <div>
    <VModal v-model="showSupplierCreateModal" @close="showSupplierCreateModal = false">
      <template v-slot:title>{{ $t("Create Supplier") }}</template>
      <div class="w-100">
        <!-- form start -->
        <form role="form" class="w-100" @keydown="form.onKeydown($event)">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="name">{{ $t("Name") }} <span class="required">*</span></label>
              <input id="name" v-model="form.name" type="text" class="form-control"
                :class="{ 'is-invalid': form.errors.has('name') }" name="name"
                :placeholder="$t('Enter a name')" />
              <has-error :form="form" field="name" />
            </div>
            <div class="form-group col-md-6">
              <label for="companyName">{{ $t("Company Name") }}</label>
              <input id="companyName" v-model="form.companyName" type="companyName" class="form-control"
                :class="{ 'is-invalid': form.errors.has('companyName') }" name="companyName"
                :placeholder="$t('Enter a company name')" />
              <has-error :form="form" field="companyName" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="phoneNumber">{{ $t("Contact Number") }}
                <span class="required">*</span></label>
              <vue-tel-input :class="{ 'is-invalid': form.errors.has('phoneNumber') }" v-model="form.phoneNumber"
                :inputOptions="{
                  showDialCode: true,
                }"></vue-tel-input>
              <has-error :form="form" field="phoneNumber" />
            </div>
            <div class="form-group col-md-6">
              <label for="email">{{ $t("Email") }}</label>
              <input id="email" v-model="form.email" type="email" class="form-control"
                :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                :placeholder="$t('Enter your email address')" />
              <has-error :form="form" field="email" />
            </div>
          </div>
          <div class="form-group">
            <label for="address">{{ $t("Address") }}</label>
            <textarea id="address" v-model="form.address" class="form-control"
              :class="{ 'is-invalid': form.errors.has('address') }" :placeholder="$t('Enter an address')" />
            <has-error :form="form" field="address" />
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label for="image">{{ $t("Image") }}</label>
              <div class="custom-file">
                <input id="image" type="file" class="custom-file-input" name="image"
                  :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                <label class="custom-file-label" for="image">{{
                  $t("Choose file")
                }}</label>
              </div>
              <has-error :form="form" field="image" />
              <div class="bg-light mt-4 w-25">
                <img v-if="url" :src="url" class="img-fluid" :alt="$t('Attached Image')" />
              </div>
            </div>
            <div class="form-group col-md-6">
              <label for="status">{{ $t("Status") }}</label>
              <select id="status" v-model="form.status" class="form-control"
                :class="{ 'is-invalid': form.errors.has('status') }">
                <option value="1">{{ $t("Active") }}</option>
                <option value="0">{{ $t("Inactive") }}</option>
              </select>
              <has-error :form="form" field="status" />
            </div>
            <div class="form-group col-12 d-flex flex-wrap">
              <div class="pr-5">
                <toggle-button v-model="form.isSendEmail" :disabled="isDemoMode"/>
                {{ $t("Send To Email") }}
              </div>
            </div>
            <div class="form-group col-12 d-flex flex-wrap">
              <div class="pr-5">
                <toggle-button v-model="form.isSendSMS" :disabled="isDemoMode"/>
                {{ $t("Send To SMS") }}
              </div>
            </div>
          </div>
        </form>
      </div>
      <div slot="modal-footer">
        <button @click="submitItem($event)" :loading="form.busy" class="btn btn-primary">
          <i class="fas fa-save" /> {{ $t("Save") }}
        </button>
      </div>
    </VModal>
    <a @click="toggleModal" class="create-button">
      <slot></slot>
    </a>
  </div>
</template>

<script>
import Form from "vform";
import { VueTelInput } from "vue-tel-input";
import { ToggleButton } from "vue-js-toggle-button";

export default {
  name: "SupplierCreateModal",
  middleware: ["auth", "check-permissions"],
  components: {
    VueTelInput,
    ToggleButton,
  },

  data: () => ({
    form: new Form({
      name: "",
      email: "",
      phoneNumber: "",
      companyName: "",
      address: "",
      image: "",
      status: 1,
      isSendEmail: false,
      isSendSMS: false,
    }),
    isDemoMode: window.config.isDemoMode,
    loading: true,
    url: null,
    showSupplierCreateModal: false,
  }),
  methods: {
    toggleModal() {
      this.showSupplierCreateModal = !this.showSupplierCreateModal;
    },

    submitItem(evt) {
      evt.preventDefault();
      this.saveSupplier();
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0];
      const reader = new FileReader();
      if (
        file.size < 2111775 &&
        (file.type === "image/jpeg" ||
          file.type === "image/png" ||
          file.type === "image/gif")
      ) {
        reader.onloadend = () => {
          this.form.image = reader.result;
        };
        reader.readAsDataURL(file);
        this.url = URL.createObjectURL(file);
      } else {
        Swal.fire(
          this.$t("Error!"),
          this.$t("Please select a valid thumbnail with size less than 2 MB"),
          "error"
        );
      }
    },

    // save supplier
    async saveSupplier() {
      await this.form
        .post(window.location.origin + "/api/suppliers")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("Supplier added successfully"),
          });
          this.form.reset();
          this.showSupplierCreateModal = false;
          this.$emit("reloadSuppliers");
        })
        .catch(() => {
          toast.fire({ type: "error", title: this.$t("Opps...something went wrong") });
        });
    },
  },
};
</script>
<style src="vue-tel-input/dist/vue-tel-input.css"></style>
<style scoped>
.create-button {
  text-decoration: none;
  cursor: pointer;
}

.vue-tel-input {
  padding: 3px;
}

.ti__dropdown-list {
  z-index: 2;
}
</style>
