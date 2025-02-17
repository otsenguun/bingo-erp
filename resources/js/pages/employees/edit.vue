<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t('Edit Employee') }}
            </h3>
            <router-link :to="{ name: 'employees.index' }" class="btn btn-dark float-right">
              <i class="fas fa-long-arrow-alt-left" /> {{ $t('Back') }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form role="form" @submit.prevent="updateEmployee" @keydown="form.onKeydown($event)">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="employeeName">{{ $t('Employee Name') }}
                    <span class="required">*</span></label>
                  <input id="employeeName" v-model="form.employeeName" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('employeeName') }" name="employeeName" :placeholder="$t('Enter an employee name')
                      " />
                  <has-error :form="form" field="employeeName" />
                </div>
                <div class="form-group col-md-4">
                  <label for="department">{{ $t('Department') }}
                    <span class="required">*</span></label>
                  <v-select v-if="items" v-model="form.department" :options="items" label="name"
                    :class="{ 'is-invalid': form.errors.has('department') }" name="department"
                    :placeholder="$t('Department')" />
                  <has-error :form="form" field="department" />
                </div>
                <div class="form-group col-md-4">
                  <label for="designation">{{ $t('Designation') }}
                    <span class="required">*</span></label>
                  <input id="designation" v-model="form.designation" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('designation') }" name="designation"
                    :placeholder="$t('Enter a designation')" />
                  <has-error :form="form" field="designation" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label for="mobileNumber">{{ $t('Contact Number') }}
                    <span class="required">*</span></label>
                  <input id="mobileNumber" v-model="form.mobileNumber" type="text" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mobileNumber') }" name="mobileNumber"
                    :placeholder="$t('Enter a contact number')" />
                  <has-error :form="form" field="mobileNumber" />
                </div>
                <div class="form-group col-md-4">
                  <label for="salary">{{ $t('Salary') }}
                    <span class="required">*</span></label>
                  <input id="salary" v-model="form.salary" type="number" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('salary') }" name="salary"
                    :placeholder="$t('Enter a salary')" min="0" />
                  <has-error :form="form" field="salary" />
                </div>
                <div class="form-group col-md-4">
                  <label for="commission">{{ $t('Commission') }}(%)
                  </label>
                  <input id="commission" v-model="form.commission" type="number" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('commission') }" name="commission"
                    :placeholder="$t('Enter a commission')" max="100" />
                  <has-error :form="form" field="commission" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-3">
                  <label for="birthDate">{{
                    $t('Birth Date')
                  }}</label>
                  <input id="reabirthDateson" v-model="form.birthDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('birthDate') }" name="birthDate" />
                  <has-error :form="form" field="birthDate" />
                </div>
                <div class="form-group col-md-3">
                  <label for="gender">{{ $t('Gender') }}
                    <span class="required">*</span></label>
                  <select v-model="form.gender" class="form-control" :class="{ 'is-invalid': form.errors.has('gender') }"
                    name="gender">
                    <option value="" selected disabled>
                      {{ $t('Select a gender') }}
                    </option>
                    <option value="Male">
                      {{ $t('Male') }}
                    </option>
                    <option value="Female">
                      {{ $t('Female') }}
                    </option>
                    <option value="Transgender">
                      {{ $t('Transgender') }}
                    </option>
                    <option value="Other">
                      {{ $t('Other') }}
                    </option>
                  </select>
                  <has-error :form="form" field="gender" />
                </div>
                <div class="form-group col-md-3">
                  <label for="bloodGroup">{{
                    $t('Blood Group')
                  }}</label>
                  <select v-model="form.bloodGroup" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('bloodGroup') }" name="bloodGroup">
                    <option value="" selected disabled>
                      {{ $t('Select a blood group') }}
                    </option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                  </select>
                  <has-error :form="form" field="bloodGroup" />
                </div>
                <div class="form-group col-md-3">
                  <label for="religion">{{
                    $t('Religion')
                  }}</label>
                  <select v-model="form.religion" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('religion') }" name="religion">
                    <option value="" selected disabled>
                      {{ $t('Select a religion') }}
                    </option>
                    <option value="Islam">
                      {{ $t('Islam') }}
                    </option>
                    <option value="Hinduism">
                      {{ $t('Hinduism') }}
                    </option>
                    <option value="Buddhists">
                      {{ $t('Buddhists') }}
                    </option>
                    <option value="Christians">
                      {{ $t('Christians') }}
                    </option>
                    <option value="Animists">
                      {{ $t('Animists') }}
                    </option>
                    <option value="Other">
                      {{ $t('Other') }}
                    </option>
                  </select>
                  <has-error :form="form" field="religion" />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="appointmentDate">{{
                    $t('Appointment Date')
                  }}</label>
                  <input id="appointmentDate" v-model="form.appointmentDate" type="date" class="form-control" :class="{
                    'is-invalid': form.errors.has('appointmentDate'),
                  }" name="appointmentDate" />
                  <has-error :form="form" field="appointmentDate" />
                </div>
                <div class="form-group col-md-6">
                  <label for="joiningDate">{{
                    $t('Join Date')
                  }}</label>
                  <input id="joiningDate" v-model="form.joiningDate" type="date" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('joiningDate') }" name="joiningDate" />
                  <has-error :form="form" field="joiningDate" />
                </div>
              </div>
              <div class="form-group">
                <label for="address">{{ $t('Address') }}</label>
                <textarea id="address" v-model="form.address" class="form-control"
                  :class="{ 'is-invalid': form.errors.has('note') }" :placeholder="$t('Enter an address')" />
                <has-error :form="form" field="address" />
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="status">{{ $t('Status') }}</label>
                  <select id="status" v-model="form.status" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('status') }">
                    <option value="1">{{ $t('Active') }}</option>
                    <option value="0">{{ $t('Inactive') }}</option>
                  </select>
                  <has-error :form="form" field="status" />
                </div>
                <div class="form-group col-md-6">
                  <label for="image">{{ $t('Profile Picture') }}</label>
                  <div class="custom-file">
                    <input id="image" type="file" class="custom-file-input" name="image"
                      :class="{ 'is-invalid': form.errors.has('image') }" @change="onFileChange" />
                    <label class="custom-file-label" for="image">{{
                      $t('Choose file')
                    }}</label>
                  </div>
                  <has-error :form="form" field="image" />
                  <div class="bg-light mt-4 w-25">
                    <img v-if="url" :src="url" class="img-fluid" :alt="$t('Attached Image')" />
                  </div>
                </div>
              </div>
              <div class="form-check">
                <input v-model="form.allowLogin" type="checkbox" class="form-check-input" id="allowLogin" />
                <label class="form-check-label" for="allowLogin">{{
                  $t('Allow Employee Login')
                }}</label>
              </div>
              <div v-if="form.allowLogin" class="row mt-3">
                <div class="form-group col-md-4">
                  <label for="email">{{ $t('Email') }}
                    <span class="required">*</span></label>
                  <input id="email" v-model="form.email" type="email" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }" name="email"
                    :placeholder="$t('Enter your email address')" />
                  <has-error :form="form" field="email" />
                </div>
                <div class="form-group col-md-4">
                  <label for="password">{{ $t('Password') }} </label>
                  <input id="password" v-model="form.password" type="password" class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password') }" name="password"
                    placeholder="Enter a password" />
                  <has-error :form="form" field="password" />
                </div>
                <div class="form-group col-md-4">
                  <label for="role">{{ $t('Role')
                  }}<span class="required">*</span></label>
                  <v-select v-if="roles" v-model="form.role" :options="roles" label="name"
                    :class="{ 'is-invalid': form.errors.has('role') }" name="role"
                    :placeholder="$t('Select a role')" />
                  <has-error :form="form" field="role" />
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t('Save changes') }}
              </v-button>
              <button type="reset" class="btn btn-secondary float-right" @click="form.reset()">
                <i class="fas fa-power-off" /> {{ $t('Reset') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Edit Employee') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Edit Employee',
    breadcrumbs: [
      {
        name: 'Dashboard',
        url: 'home',
      },
      {
        name: 'Employees',
        url: 'employees.index',
      },
      {
        name: 'Edit',
        url: '',
      },
    ],
    form: new Form({
      employeeName: '',
      department: '',
      designation: '',
      salary: '',
      commission: '',
      mobileNumber: '',
      gender: '',
      birthDate: '',
      bloodGroup: '',
      religion: '',
      appointmentDate: '',
      joiningDate: '',
      address: '',
      status: 1,
      image: '',
      allowLogin: false,
      email: '',
      password: '',
      role: '',
    }),
    options: [],
    roles: '',
    url: null,
  }),
  computed: {
    ...mapGetters('operations', ['items']),
  },
  created() {
    this.getDepartments()
    this.getRoles()
    this.getEmployee()
  },
  methods: {
    // get all departments
    async getDepartments() {
      await this.$store.dispatch('operations/allData', {
        path: '/api/all-departments',
      })
    },

    // get roles
    async getRoles() {
      const { data } = await axios.get(
        window.location.origin + '/api/all-roles'
      )
      this.roles = data.data
    },

    // get employee
    async getEmployee() {
      const { data } = await axios.get(
        window.location.origin + '/api/employees/' + this.$route.params.slug
      )
      this.form.employeeName = data.data.name
      this.form.department = data.data.department
      this.form.designation = data.data.designation
      this.form.salary = data.data.salary
      this.form.commission = data.data.commission
      this.form.mobileNumber = data.data.mobileNumber
      this.form.gender = data.data.gender
      this.form.birthDate = data.data.birthDate
      this.form.bloodGroup = data.data.bloodGroup
      this.form.religion = data.data.religion
      this.form.appointmentDate = data.data.appointmentDate
      this.form.joiningDate = data.data.joiningDate
      this.form.address = data.data.address
      this.form.status = data.data.status
      this.form.allowLogin = data.data.allowLogin
      this.form.email = data.data.email
      this.form.role = data.data.role
      this.url = data.data.image
    },

    // vue file upload
    onFileChange(e) {
      const file = e.target.files[0]
      const reader = new FileReader()
      if (
        file.size < 2111775 &&
        (file.type === 'image/jpeg' ||
          file.type === 'image/png' ||
          file.type === 'image/gif')
      ) {
        reader.onloadend = () => {
          this.form.image = reader.result
        }
        reader.readAsDataURL(file)
        this.url = URL.createObjectURL(file)
      } else {
        Swal.fire(
          this.$t('Error!'),
          this.$t('Please select a valid thumbnail with size less than 2 MB'),
          'error'
        )
      }
    },

    // update employee
    async updateEmployee() {
      await this.form
        .patch(
          window.location.origin + '/api/employees/' + this.$route.params.slug
        )
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('Employee updated successfully'),
          })
          this.$router.push({ name: 'employees.index' })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('Opps...something went wrong'),
          })
        })
    },
  },
}
</script>
