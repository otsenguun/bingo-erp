<template>
    <div>
        <!--- SECTION DATA TABLE START --->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card custom-card w-100">
                    <div class="card-header setings-header">
                        <div class="col-xl-4 col-4">
                            <h3 class="card-title">
                                {{ $t('Explorer Elements') }}
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
                                        <th>
                                            {{
                                                $t(
                                                    'Description'
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
                                            {{
                                                data.description
                                                    ? data.description.substring(
                                                          0,
                                                          25
                                                      )
                                                    : ''
                                            }}
                                        </td>
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

                   <div class="form-group col-12 d-flex flex-wrap mt-3">
                        <div class="pr-5">
                        <toggle-button 
                                v-model="is_show_business_need_section"
                                :sync="true"
                                @change="onChangeShowHideToggleButton"
                            />
                            {{ $t("Show at landing page") }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--- SECTION DATA TABLE START --->

        <!-- use the modal component -->
        <Modal v-if="showModal" @close="closeModal">
            <h5 slot="header">{{ $t('Explorers') }}</h5>
            <div class="w-100 m-0" slot="body">
                <form
                    ref="form"
                    @submit.prevent="save"
                    @keydown="dataForm.onKeydown($event)"
                    class="row"
                >
                    <!-- title -->
                    <div class="form-group col-md-12">
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

                    <!-- image align left -->
                    <div class="form-group col-md-6">
                        <label for="image_align_left">
                            {{ $t('Align Left') }}
                            <span class="required">*</span>
                        </label>
                        <select
                            id="image_align_left"
                            v-model="dataForm.image_align_left"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    dataForm.errors.has('image_align_left'),
                            }"
                            name="image_align_left"
                        >
                            <option value="1">{{ $t('True') }}</option>
                            <option value="0">{{ $t('False') }}</option>
                        </select>
                        <has-error :form="dataForm" field="image_align_left" />
                    </div>

                    <!-- points -->
                    <div class="form-group col-md-12">
                        <label for="points">
                            {{ $t('Points') }}
                            <span class="required">*</span>
                        </label>
                        <input-tag
                            v-model="dataForm.points"
                            :class="{
                                'is-invalid': dataForm.errors.has('points'),
                            }"
                            id="points"
                            :placeholder="$t('Points')"
                            name="points"
                        />
                        <has-error :form="dataForm" field="points" />
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
                    Close
                </button>
            </div>
        </Modal>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Form from 'vform';
import axios from 'axios';

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

        dataForm: new Form({
            title: '',
            status: 1,
            description: '',
            image_align_left: 1,
            button_text: '',
            button_link: '',
            image: null,
            points: [],
            type: 'explorers',
        }),
        showModal: false,
        query: '',
        perPage: 10,
        url: null,
        typeName: 'type',
        typeValue: 'explorers',
        formType: 'create', // create or edit
        formUpdateId: null,
        isDemoMode: window.config.isDemoMode,
        is_show_business_need_section: false,
    }),

    // Map Getters
    computed: {
        ...mapGetters('operations', ['appInfo', 'items', 'loading', 'pagination']),
    },

    created() {
        this.is_show_business_need_section = this.appInfo.is_show_business_need_section;
        this.getTableData();
    },

    watch: {
        // watch search data
        query: function (newQ) {
            if (newQ === '') {
                this.getTableData();
            } else {
                this.searchData();
            }
        },
    },

    methods: {
        clearEverythingReloadDataHideModalAfterSubmit() {
            this.formType = 'create'; // create or edit
            this.formUpdateId = null;
            this.showModal = false;
            this.dataForm.image = null;
            this.dataForm.reset();
            this.dataForm.clear();
            this.url = null;
        },

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

        async onChangeShowHideToggleButton(newValue){
            try {
                await axios.post(window.location.origin + '/api/settings/update-show-hide-section', {
                    key: 'is_show_business_need_section',
                    value: newValue.value
                });
                toast.fire({
                type: 'success',
                title: this.$t('Updated successfully!'),
            })
            } catch (error) {
                toast.fire({ type: 'error', title: this.$t('Opps...something went wrong') })
            }
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

        async editData(id) {
            // disable for demo
            if (this.isDemoMode) {
                return toast.fire({
                    type: 'warning',
                    title: this.$t(
                        'You are not allowed to do this in demo version.'
                    ),
                });
            }
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
