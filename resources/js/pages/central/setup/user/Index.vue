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
                <div class="card">
                    <div class="card-header setings-header">
                        <div class="col-xl-4 col-4">
                            <h3 class="card-title">
                                {{
                                    $t(
                                        'Users'
                                    )
                                }}
                            </h3>
                        </div>
                        <div class="col-xl-8 col-8 float-right text-right">
                            <div class="btn-group">
                                <a
                                    @click="print"
                                    v-tooltip="$t('Print Table')"
                                    class="btn btn-info"
                                >
                                    <i class="fas fa-print"></i>
                                </a>
                                <router-link
                                    :to="{ name: 'user.create' }"
                                    class="btn btn-primary"
                                >
                                    {{ $t('Create') }}
                                    <i
                                        class="fas fa-plus-circle d-none d-sm-inline-block"
                                    />
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table-loading v-show="loading" />
                        <div
                            class="table-responsive table-custom mt-3"
                            id="printMe"
                        >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ $t('#') }}</th>
                                        <th>{{ $t('Name') }}</th>
                                        <th>{{ $t('Email') }}</th>
                                        <th>{{ $t('Role') }}</th>
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
                                            <div class="profile_wrap">
                                                <img
                                                    :src="data.photo_url"
                                                    :alt="data.name"
                                                    class=""
                                                />
                                                {{ data.name }}
                                            </div>
                                        </td>
                                        <td>
                                            <a :href="`mailto:${data.email}`">{{
                                                data.email
                                            }}</a>
                                        </td>
                                        <td v-if="data.roles">
                                            {{ data.roles[0] }}
                                        </td>
                                        <td class="text-right no-print">
                                            <div
                                                v-if="
                                                    developer ||
                                                    (data.slug != 'developer' &&
                                                        data.roles !=
                                                            'super-admin')
                                                "
                                                class="btn-group"
                                            >
                                                <router-link
                                                    v-if="data && data.slug"
                                                    v-tooltip="
                                                        $t('Edit')
                                                    "
                                                    :to="{
                                                        name: 'user.edit',
                                                        params: {
                                                            slug: data.slug,
                                                        },
                                                    }"
                                                    class="btn btn-info btn-sm"
                                                >
                                                    <i class="fas fa-edit" />
                                                </router-link>
                                                <a v-if="data.roles[0] != activeUser.roles[0]" v-tooltip="
                                                        $t('Delete')
                                                    "
                                                    href="#"
                                                    class="btn btn-danger btn-sm"
                                                    @click="
                                                        deleteData(data.slug)
                                                    "
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
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import SettingsSidebar from '~/components/central/SettingsSidebar';

export default {
    layout: 'central',
    middleware: ['auth', 'check-permissions'],
    metaInfo() {
        return {
            title: this.$t('Users'),
        };
    },
    components: {
        SettingsSidebar,
    },
    data: () => ({
        breadcrumbsCurrent:
            'Users',
        breadcrumbs: [
            {
                name: 'Dashboard',
                url: 'home',
            },
            {
                name: 'Setup',
                url: 'setup.index',
            },
            {
                name: 'Users',
                url: '',
            },
        ],
        activeUser: '',
        query: '',
        perPage: 10,
        developer: false,
        isDemoMode: window.config.isDemoMode,
    }),

    // Map Getters
    computed: {
        ...mapGetters('operations', ['items', 'loading', 'pagination']),
    },

    watch: {
        // watch search data
        query: function (newQ) {
            if (newQ === '') {
                this.getData();
            } else {
                this.searchData();
            }
        },
    },

    created() {
        this.getData();
        this.getActiveUser();
        Fire.$on('AfterDelete', () => {
            this.getData();
        });
    },

    methods: {
        // get data
        async getData() {
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination
                ? this.pagination.current_page
                : 1;
            await this.$store.dispatch('operations/fetchData', {
                path: '/api/users?page=',
                currentPage: currentPage + '&perPage=' + this.perPage,
            });
        },

         // get the active user 
         async getActiveUser() {
            const { data } = await axios.get(
                window.location.origin + '/api/user'
            );
            this.activeUser = data.data;
        },

        // search data
        async searchData() {
            this.$store.state.operations.loading = true;
            let currentPage = this.pagination
                ? this.pagination.current_page
                : 1;
            await this.$store.dispatch('operations/searchData', {
                term: this.query,
                path: '/api/user/search/',
                currentPage: currentPage + '&perPage=' + this.perPage,
            });
        },

        // Pagination
        async paginate() {
            this.query === '' ? this.getData() : this.searchData();
        },

        // Reset pagination
        async resetPagination() {
            this.pagination.current_page = 1;
        },

        // Reload after search
        async reload() {
            this.query = '';
        },

        // update per page count
        updatePerPager() {
            this.pagination.current_page = 1;
            this.query === '' ? this.getData() : this.searchData();
        },

        // print table
        async print() {
            await this.$htmlToPaper('printMe');
        },

        // delete data
        async deleteData(slug) {
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
                confirmButtonText: this.$t('Confirm'),
            }).then((result) => {
                if (result.value) {
                    this.$store
                        .dispatch('operations/deleteData', {
                            path: '/api/user/',
                            slug: slug,
                        })
                        .then((response) => {
                            if (response === true) {
                                Swal.fire(
                                    this.$t('Deleted!'),
                                    this.$t('Deleted successfully.'),
                                    'success'
                                );
                                Fire.$emit('AfterDelete');
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

<style scoped>
.profile_wrap {
    display: flex;
    align-items: center;
}

.profile_wrap img {
    width: 40px;
    height: 40px;
    border-radius: 100%;
    margin-right: 10px;
}

.table th,
.table td {
    vertical-align: middle;
}
</style>
