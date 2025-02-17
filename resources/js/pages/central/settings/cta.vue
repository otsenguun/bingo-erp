<template>
    <div>
        <!--- SECTION TITLE FORM START --->
        <div class="card">
            <div class="card-header setings-header">
                <h3 class="card-title">
                    {{ $t('All Features And Get Started') }}
                </h3>
            </div>
            <form
                class="form-horizontal"
                @submit.prevent="update"
                @keydown="form.onKeydown($event)"
            >
                <div class="card-body">
                    <div class="form-group">
                        <label for="get_started_box_title">
                            {{ $t('Get Started Box Title') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.get_started_box_title"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'get_started_box_title'
                                ),
                            }"
                            id="get_started_box_title"
                            :placeholder="
                                $t('Enter get started box title')
                            "
                        />
                        <has-error :form="form" field="get_started_box_title" />
                    </div>

                    <div class="form-group">
                        <label for="get_started_box_description">
                            {{ $t('Get Started Box Description') }}
                            <span class="required">*</span>
                        </label>
                        <textarea
                            v-model="form.get_started_box_description"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'get_started_box_description'
                                ),
                            }"
                            id="get_started_box_description"
                            :placeholder="
                                $t(
                                    'Enter get started box description'
                                )
                            "
                        ></textarea>
                        <has-error
                            :form="form"
                            field="get_started_box_description"
                        />
                    </div>

                    <div class="form-group">
                        <label for="get_started_box_button_text">
                            {{ $t('Get Started Box Button Text') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.get_started_box_button_text"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'get_started_box_button_text'
                                ),
                            }"
                            id="get_started_box_button_text"
                            :placeholder="
                                $t(
                                    'Enter get started box button text'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="get_started_box_button_text"
                        />
                    </div>

                    <div class="form-group">
                        <label for="get_started_box_button_link">
                            {{ $t('Get Started Box Button Link') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.get_started_box_button_link"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'get_started_box_button_link'
                                ),
                            }"
                            id="get_started_box_button_link"
                            :placeholder="
                                $t(
                                    'Enter get started box button link'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="get_started_box_button_link"
                        />
                    </div>

                    <div class="form-group col-12 d-flex flex-wrap">
                        <div class="pr-5">
                        <toggle-button 
                                v-model="form.is_show_cta_section"
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
            get_started_box_title: '',
            get_started_box_description: '',
            get_started_box_button_text: '',
            get_started_box_button_link: '',
            is_show_cta_section: false ,
        }),
        dataForm: new Form({
            title: '',
            description: '',
            status: '',
            image: '',
        }),
        user: '',
        isDemoMode: window.config.isDemoMode,
    }),

    // Map Getters
    computed: {
        ...mapGetters('operations', ['appInfo', 'items', 'loading', 'pagination']),
    },

    created() {
        this.getData();
    },

    methods: {
        // get the user
        async getData() {
            const { data } = await axios.get(
                window.location.origin + '/api/settings/get-started-settings'
            );
            this.user = data.data;
            this.form.get_started_box_title = data.data.get_started_box_title || '';
            this.form.get_started_box_description = data.data.get_started_box_description || '';
            this.form.get_started_box_button_text = data.data.get_started_box_button_text || '';
            this.form.get_started_box_button_link = data.data.get_started_box_button_link || '';
            this.form.is_show_cta_section = this.appInfo.is_show_cta_section;
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
                        '/api/settings/get-started-settings'
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
