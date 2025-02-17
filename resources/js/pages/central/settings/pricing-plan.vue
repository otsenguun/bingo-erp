<template>
    <div>
        <!--- SECTION TITLE FORM START --->
        <div class="card">
            <div class="card-header setings-header">
                <h3 class="card-title">{{ $t('Pricing Plan') }}</h3>
            </div>
            <form
                class="form-horizontal"
                @submit.prevent="update"
                @keydown="form.onKeydown($event)"
            >
                <div class="card-body">
                    <div class="form-group">
                        <label for="pricing_plan_section_tagline">
                            {{ $t('Pricing Plan Section Tagline') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.pricing_plan_section_tagline"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'pricing_plan_section_tagline'
                                ),
                            }"
                            id="pricing_plan_section_tagline"
                            :placeholder="
                                $t(
                                    'Enter pricing plan section tagline'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="pricing_plan_section_tagline"
                        />
                    </div>

                    <div class="form-group">
                        <label for="pricing_plan_section_title">
                            {{ $t('Pricing Plan Section Title') }}
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.pricing_plan_section_title"
                            class="form-control"
                            :class="{
                                'is-invalid': form.errors.has(
                                    'pricing_plan_section_title'
                                ),
                            }"
                            id="pricing_plan_section_title"
                            :placeholder="
                                $t(
                                    'Enter pricing plan section title'
                                )
                            "
                        />
                        <has-error
                            :form="form"
                            field="pricing_plan_section_title"
                        />
                    </div>

                    <div class="form-group col-12 d-flex flex-wrap">
                        <div class="pr-5">
                        <toggle-button 
                                v-model="form.is_show_pricing_section"
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
            pricing_plan_section_tagline: '',
            pricing_plan_section_title: '',
            is_show_pricing_section: false ,
        }),
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
                window.location.origin + '/api/settings/pricing-plan-settings'
            );
            this.user = data.data;
            this.form.pricing_plan_section_tagline = data.data.pricing_plan_section_tagline || '';
            this.form.pricing_plan_section_title = data.data.pricing_plan_section_title || '';
            this.form.is_show_pricing_section = this.appInfo.is_show_pricing_section;
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
                        '/api/settings/pricing-plan-settings'
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
