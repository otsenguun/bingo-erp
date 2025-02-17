<template>
    <div>
        <!--- SECTION TITLE FORM START --->
        <div class="card">
            <div class="card-header setings-header">
                <h3 class="card-title">{{ $t('All features') }}</h3>
            </div>
            <form
                class="form-horizontal"
                @submit.prevent="update"
                @keydown="form.onKeydown($event)"
            >
                <div class="card-body">
                    <div class="form-group">
                        <label for="all_features_section_tagline">
                            {{ $t('All Features Section Tagline') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.all_features_section_tagline"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'all_features_section_tagline'
                                ),
                            }"
                            id="all_features_section_tagline"
                            :placeholder="
                                $t(
                                    'Enter all features section tagline'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="all_features_section_tagline"
                        />
                    </div>
                    <div class="form-group">
                        <label for="all_features_section_title">
                            {{ $t('All Features Section Title') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.all_features_section_title"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'all_features_section_title'
                                ),
                            }"
                            id="all_features_section_title"
                            :placeholder="
                                $t(
                                    'Enter all features section title'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="all_features_section_title"
                        />
                    </div>

                    <div class="form-group col-12 d-flex flex-wrap">
                        <div class="pr-5">
                        <toggle-button 
                                v-model="form.is_show_extra_features_section"
                                :sync="true"
                            />
                            {{ $t("Show at landing page") }}
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <v-button :loading="form.busy" class="btn btn-primary">
                        <i class="fas fa-edit" />
                        {{ $t('Save changes') }}
                    </v-button>
                </div>
            </form>
        </div>
        <!--- SECTION TITLE FORM END --->

        <!--- SECTION DATA TABLE START --->
        <div class="row mt-5 mb-4">
            <div class="col-lg-12">
                <div class="card custom-card w-100">
                    <div class="card-header setings-header">
                        <div class="col-xl-4 col-4">
                            <h3 class="card-title">
                                {{ $t('All Feature Elements') }}
                            </h3>
                        </div>
                        <div class="col-xl-8 col-8 float-right text-right">
                            <div class="btn-group c-w-100">
                                <a
                                    @click="refreshTable()"
                                    href="#"
                                    v-tooltip="'Refresh'"
                                    class="btn btn-success"
                                >
                                    <i class="fas fa-sync"></i>
                                </a>
                                <button
                                    @click="showModal = !showModal"
                                    class="btn btn-primary"
                                >
                                    {{ $t('Create') }}
                                    <i
                                        class="fas fa-plus-circle d-none d-sm-inline-block"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body position-relative">
                        <div class="row">
                            <div class="col-6 col-xl-4 mb-2">
                                <search
                                    v-model="query"
                                    @reset-pagination="resetPagination()"
                                    @reload="reload"
                                />
                            </div>
                        </div>
                        <table-loading v-show="loading" />
                        <div
                            id="printMe"
                            class="table-responsive table-custom mt-3"
                        >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ $t('#') }}</th>
                                        <th>
                                            {{
                                                $t(
                                                    'Image'
                                                )
                                            }}
                                        </th>
                                        <th>
                                            {{
                                                $t(
                                                    'Title'
                                                )
                                            }}
                                        </th>
                                        <th>{{ $t('Status') }}</th>
                                        <th class="text-right no-print">
                                            {{ $t('Action') }}
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
                                            <span
                                                v-if="
                                                    pagination &&
                                                    pagination.current_page > 1
                                                "
                                            >
                                                {{
                                                    pagination.per_page *
                                                        (pagination.current_page -
                                                            1) +
                                                    (i + 1)
                                                }}
                                            </span>
                                            <span v-else>{{ i + 1 }}</span>
                                        </td>
                                        <td>
                                            <a v-if="data.image" href="#">
                                                <img
                                                    :src="data.image"
                                                    class="rounded preview-sm"
                                                    loading="lazy"
                                                />
                                            </a>
                                            <div
                                                v-else
                                                class="bg-secondary rounded no-preview-sm"
                                            >
                                                <small>{{
                                                    $t('No Preview')
                                                }}</small>
                                            </div>
                                        </td>
                                        <td>{{ data.title }}</td>
                                        <td>
                                            <span
                                                v-if="data.status === 1"
                                                class="badge bg-success"
                                                >{{ $t('Active') }}</span
                                            >
                                            <span
                                                v-else
                                                class="badge bg-danger"
                                                >{{
                                                    $t('Inactive')
                                                }}</span
                                            >
                                        </td>
                                        <td class="text-right no-print">
                                            <div class="btn-group">
                                                <a
                                                    href="#"
                                                    v-tooltip="
                                                        $t('Edit')
                                                    "
                                                    @click="editData(data.id)"
                                                    class="btn btn-info btn-sm"
                                                >
                                                    <i class="fas fa-edit" />
                                                </a>
                                                <a
                                                    v-tooltip="
                                                        $t('Delete')
                                                    "
                                                    href="#"
                                                    class="btn btn-danger btn-sm"
                                                    @click="deleteData(data.id)"
                                                >
                                                    <i class="fas fa-trash" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-show="!loading && !items.length">
                                        <td colspan="6">
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
                                    <select
                                        @change="updatePerPager"
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
                                v-if="pagination && pagination.last_page > 1"
                                :pagination="pagination"
                                :offset="5"
                                class="justify-flex-end"
                                @paginate="paginate"
                            />
                            <!-- pagination-end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--- SECTION DATA TABLE START --->

        <!-- use the modal component -->
        <Modal v-if="showModal" @close="closeModal">
            <h5 slot="header">{{ $t('All features') }}</h5>
            <div class="w-100 m-0" slot="body">
                <form
                    ref="form"
                    @submit.prevent="save"
                    @keydown="dataForm.onKeydown($event)"
                    class="row"
                >
                    <!-- title -->
                    <div class="form-group col-md-6">
                        <label for="title">
                            {{ $t('title') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="dataForm.title"
                            type="text"
                            class="form-control"
                            :class="{
                                'is-invalid': dataForm.errors.has('title'),
                            }"
                            name="title"
                            :placeholder="$t('title')"
                        />
                        <has-error :form="dataForm" field="title" />
                    </div>

                    <!-- status -->
                    <div class="form-group col-md-6">
                        <label for="status">
                            {{ $t('Status') }}
                            <span class="required">*</span>
                        </label>
                        <select
                            id="status"
                            v-model="dataForm.status"
                            class="form-control"
                            name="status"
                            :class="{
                                'is-invalid': dataForm.errors.has('status'),
                            }"
                        >
                            <option value="1">{{ $t('Active') }}</option>
                            <option value="0">
                                {{ $t('Inactive') }}
                            </option>
                        </select>
                        <has-error :form="dataForm" field="status" />
                    </div>

                    <!-- description -->
                    <div class="form-group col-md-12">
                        <label for="description">
                            {{ $t('Description') }}
                            <span class="required">*</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="dataForm.description"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    dataForm.errors.has('description'),
                            }"
                            name="description"
                            :placeholder="
                                $t(
                                    'Enter description'
                                )
                            "
                        />
                        <has-error :form="dataForm" field="description" />
                    </div>

                    <!-- image -->
                    <div class="form-group col-md-12">
                        <label for="image">
                            {{ $t('Image') }}
                            <span class="required">*</span>
                        </label>
                        <div class="custom-file">
                            <input
                                id="image"
                                type="file"
                                class="custom-file-input"
                                name="image"
                                :class="{
                                    'is-invalid': dataForm.errors.has('image'),
                                }"
                                @change="onFileChange"
                            />
                            <label class="custom-file-label" for="image">{{
                                $t('Choose file')
                            }}</label>
                        </div>
                        <has-error :form="dataForm" field="image" />
                        <div v-if="url" class="bg-light mt-4 w-25">
                            <img
                                class="img-fluid"
                                :alt="$t('Attached Image')"
                                :src="url"
                            />
                        </div>
                    </div>
                </form>
            </div>
            <div
                slot="modal-footer"
                class="d-flex w-100 justify-content-between p-3"
            >
                <button
                    @click="handleFormSubmit"
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-edit"></i> {{ $t('Save changes') }}
                </button>
                <button
                    type="button"
                    class="btn btn-danger"
                    @click="closeModal"
                >
                    {{ $t('Close') }}
                </button>
            </div>
        </Modal>
    </div>
</template>

<script>
import Form from 'vform';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return { title: this.$t('Landing Page Settings') };
    },
    data: () => ({
        breadcrumbsCurrent: 'Update Profile',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Update',
                url: '',
            },
        ],
        form: new Form({
            all_features_section_tagline: '',
            all_features_section_title: '',
            is_show_extra_features_section: false ,
        }),
        dataForm: new Form({
            title: '',
            status: 1,
            description: '',
            image: null,
            type: 'all_features',
        }),
        showModal: false,
        query: '',
        perPage: 10,
        url: null,
        typeName: 'type',
        typeValue: 'all_features',
        formType: 'create', // create or edit
        formUpdateId: null,
        isDemoMode: window.config.isDemoMode,
    }),

    // Map Getters
    computed: {
        ...mapGetters('operations', ['appInfo', 'items', 'loading', 'pagination']),
    },

    created() {
        this.getData();
        this.getTableData();
    },

    watch: {
        // watch search data
        query: function (newQ) {
            if (newQ === '') {
                this.getFeaturesData();
            } else {
                this.searchData();
            }
        },
    },

    methods: {
        // get the data
        async getData() {
            const { data } = await axios.get(
                window.location.origin + '/api/settings/all-features-settings'
            );
            this.user = data.data;
            this.form.all_features_section_tagline = data.data.all_features_section_tagline || '';
            this.form.all_features_section_title = data.data.all_features_section_title || '';
            this.form.is_show_extra_features_section = this.appInfo.is_show_extra_features_section;
        },

        // update the data
        async update() {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            await this.form
                .patch(
                    window.location.origin +
                        '/api/settings/all-features-settings'
                )
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Landing Page Settings updated successfully'),
                    });
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Opps...something went wrong'),
                    });
                });
        },

        /***************************************
         * --- new modal and table starts -----
         **************************************/
        clearEverythingReloadDataHideModalAfterSubmit() {
            this.formType = 'create'; // create or edit
            this.formUpdateId = null;
            this.showModal = false;
            this.dataForm.image = null;
            this.dataForm.reset();
            this.dataForm.clear();
            this.url = null;
        },

        // handle form submit
        handleFormSubmit() {
            if (this.formType === 'create') {
                this.createData();
            } else {
                this.updateData(this.formUpdateId);
            }
        },

        // create
        createData() {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            this.dataForm
                .post('/api/setting-images')
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Created successfully!'),
                    });
                    this.getTableData();
                    this.clearEverythingReloadDataHideModalAfterSubmit();
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Opps...something went wrong'),
                    });
                });
        },

        // update
        async updateData(id) {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            this.dataForm._method = 'PATCH';
            await this.dataForm
                .post('/api/setting-images/' + id)
                .then(() => {
                    toast.fire({
                        type: 'success',
                        title: this.$t('Page updated successfully'),
                    });
                    this.getTableData();
                    this.clearEverythingReloadDataHideModalAfterSubmit();
                })
                .catch(() => {
                    toast.fire({
                        type: 'error',
                        title: this.$t('Error!'),
                    });
                });
        },

        // vue file upload
        onFileChange(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            if (
                file.size < 2111775 &&
                (file.type === 'image/jpeg' ||
                    file.type === 'image/png' ||
                    file.type === 'image/gif')
            ) {
                this.dataForm.image = file;
                reader.readAsDataURL(file);
                this.url = URL.createObjectURL(file);
            } else {
                toast.fire(
                    this.$t('Error!'),
                    this.$t('Please select a valid thumbnail with size less than 2 MB'),
                    'error'
                );
            }
        },

        // get data
        async getTableData() {
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination
                ? this.pagination.current_page
                : 1;
            await this.$store.dispatch('operations/fetchDataByType', {
                path: '/api/setting-images?page=',
                currentPage: currentPage + '&perPage=' + this.perPage,
                typeName: this.typeName,
                typeValue: this.typeValue,
            });
        },

        // update per page count
        updatePerPager() {
            this.pagination.current_page = 1;
            this.query === '' ? this.getTableData() : this.searchData();
        },

        // search data
        async searchData() {
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination
                ? this.pagination.current_page
                : 1;
            await this.$store.dispatch('operations/searchDataByType', {
                term: this.query,
                path: '/api/setting-images/search',
                currentPage: currentPage + '&perPage=' + this.perPage,
                typeName: this.typeName,
                typeValue: this.typeValue,
            });
        },

        // Reset pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // Pagination
        async paginate() {
            this.query === ''
                ? await this.getTableData()
                : await this.searchData();
        },

        // Reload after search
        async reload() {
            this.query = '';
        },

        // refresh table
        refreshTable() {
            this.query = '';
            this.query === '' ? this.getTableData() : this.searchData();
        },

        closeModal() {
            this.clearEverythingReloadDataHideModalAfterSubmit();
        },

        // save
        async save() {
            this.handleFormSubmit();
        },

        // edit data
        async editData(id) {
            this.formType = 'edit';
            this.formUpdateId = id;
            const { data } = await axios.get(
                window.location.origin + '/api/setting-images/' + id
            );
            this.dataForm.fill(data.data);
            this.url = data.data.image;
            this.showModal = true;
        },

        // delete data
        async deleteData(id) {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
            Swal.fire({
                title: this.$t('Are you sure?'),
                text: this.$t('You will not be able to return to this!'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: this.$t(
                    'Delete'
                ),
            }).then((result) => {
                // Send request to the server
                if (result.value) {
                    this.$store
                        .dispatch('operations/deleteData', {
                            path: '/api/setting-images/',
                            slug: id,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t('Deleted!'),
                                    this.$t('Deleted successfully.'),
                                    'success'
                                );
                                this.getTableData();
                            } else {
                                Swal.fire(
                                    this.$t('Failed!'),
                                    this.$t('There was something wrong.'),
                                    'warning'
                                );
                            }
                        });
                }
            });
        },
    },
};
</script>

<style lang="scss" scoped></style>
