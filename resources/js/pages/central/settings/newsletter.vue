<template>
    <div class="card">
        <div class="card-header setings-header">
            <h3 class="card-title">
                {{ $t('Newsletter Section Title') }}
            </h3>
        </div>
        <form
            class="form-horizontal"
            @submit.prevent="update"
            @keydown="form.onKeydown($event)"
        >
            <div class="card-body">
                <div class="form-group">
                    <label for="newsletter_section_title">
                        {{ $t('Newsletter Section Title') }}
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.newsletter_section_title"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'newsletter_section_title'
                            ),
                        }"
                        id="newsletter_section_title"
                        :placeholder="
                            $t('Enter newsletter section title')
                        "
                    />
                    <has-error :form="form" field="newsletter_section_title" />
                </div>
                <div class="form-group">
                    <label for="newsletter_section_description">
                        {{ $t('Newsletter Section Description') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        v-model="form.newsletter_section_description"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'newsletter_section_description'
                            ),
                        }"
                        id="newsletter_section_description"
                        :placeholder="
                            $t(
                                'Enter newsletter section description'
                            )
                        "
                    ></textarea>
                    <has-error
                        :form="form"
                        field="newsletter_section_description"
                    />
                </div>

                <div class="form-group col-12 d-flex flex-wrap">
                    <div class="pr-5">
                    <toggle-button 
                            v-model="form.is_show_newsletter_section"
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
            newsletter_section_title: '',
            newsletter_section_description: '',
            is_show_newsletter_section: false ,
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
                window.location.origin + '/api/settings/newsletter-settings'
            );
            this.user = data.data;
            this.form.newsletter_section_title = data.data.newsletter_section_title || '';
            this.form.newsletter_section_description = data.data.newsletter_section_description || '';
            this.form.is_show_newsletter_section = this.appInfo.is_show_newsletter_section;
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
                    window.location.origin + '/api/settings/newsletter-settings'
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
