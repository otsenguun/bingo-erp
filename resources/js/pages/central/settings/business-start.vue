<template>
    <div class="card">
        <div class="card-header setings-header">
            <h3 class="card-title">{{ $t('Business Start') }}</h3>
        </div>
        <form
            class="form-horizontal"
            @submit.prevent="update"
            @keydown="form.onKeydown($event)"
        >
            <div class="card-body">
                <div class="form-group">
                    <label for="business_start_section_tagline">
                        {{ $t('Business Start Section Tagline') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.business_start_section_tagline"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'business_start_section_tagline'
                            ),
                        }"
                        id="business_start_section_tagline"
                        :placeholder="
                            $t(
                                'Enter business start section tagline'
                            )
                        "
                    />
                    <has-error
                        :form="form"
                        field="business_start_section_tagline"
                    />
                </div>

                <div class="form-group">
                    <label for="business_start_section_title">
                        {{ $t('Business Start Section Title') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.business_start_section_title"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'business_start_section_title'
                            ),
                        }"
                        id="business_start_section_title"
                        :placeholder="
                            $t(
                                'Enter business start section title'
                            )
                        "
                    />
                    <has-error
                        :form="form"
                        field="business_start_section_title"
                    />
                </div>

                <div class="form-group">
                    <label for="business_start_section_description">
                        {{ $t('Business Start Section Description') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.business_start_section_description"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'business_start_section_description'
                            ),
                        }"
                        id="business_start_section_description"
                        :placeholder="
                            $t(
                                'Enter business start section description'
                            )
                        "
                    ></textarea>
                    <has-error
                        :form="form"
                        field="business_start_section_description"
                    />
                </div>

                <div class="form-group">
                    <label for="business_start_support_list">
                        {{ $t('Business Start Support List') }}
                        <span class="required">*</span>
                    </label>
                    <input-tag
                        v-model="form.business_start_support_list"
                        :class="{
                            'is-invalid': form.errors.has(
                                'business_start_support_list'
                            ),
                        }"
                        id="business_start_support_list"
                        :placeholder="
                            $t('Business Start Support List')
                        "
                    ></input-tag>
                    <has-error
                        :form="form"
                        field="business_start_support_list"
                    />
                </div>

                <div class="form-group col-12 d-flex flex-wrap">
                    <div class="pr-5">
                    <toggle-button 
                            v-model="form.is_show_business_start_section"
                            :sync="true"
                        />
                        {{ $t("Show at landing page") }}
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <v-button :loading="form.busy" class="btn btn-primary">
                    <i class="fas fa-edit" /> {{ $t('Save changes') }}
                </v-button>
            </div>
        </form>
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
        form: new Form({
            business_start_section_tagline: '',
            business_start_section_title: '',
            business_start_section_description: '',
            business_start_support_list: [],
            is_show_business_start_section: false ,
        }),
        loading: true,
        user: '',
        isDemoMode: window.config.isDemoMode,
    }),

    computed: mapGetters({
        appInfo: 'operations/appInfo',
    }),

    created() {
        this.getData();
    },
    methods: {
        // get the user
        async getData() {
            const { data } = await axios.get(
                window.location.origin + '/api/settings/business-start-settings'
            );
            this.user = data.data;
            this.form.business_start_section_tagline = data.data.business_start_section_tagline || '';
            this.form.business_start_section_title = data.data.business_start_section_title || '';
            this.form.business_start_section_description = data.data.business_start_section_description || '';
            this.form.business_start_support_list = data.data.business_start_support_list || '';
            this.form.is_show_business_start_section = this.appInfo.is_show_business_start_section;
        },

        // update
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
                        '/api/settings/business-start-settings'
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
    },
};
</script>

<style lang="scss" scoped></style>
