<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div v-if="message" class="uk-alert uk-alert-success">{{ message }}</div>

    <button class="uk-button" @click="editSettings"><i v-spinner="loading" icon="cogs"></i>Instellingen</button>


    <v-modal v-ref:editsettingsmodal :large="true">
        <div class="uk-modal-header"><h3>Instellingen</h3></div>

        <form class="uk-form" @submit.prevent="saveSettings">

            <div class="uk-grid">
                <div class="uk-width-medium-1-2 uk-form-horizontal">
                    <fields :config="$options.fields1" :model.sync="settings" template="formrow"></fields>
                </div>
                <div class="uk-width-medium-1-2 uk-form-horizontal">
                    <fields :config="$options.fields2" :model.sync="settings" template="formrow"></fields>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
                <button type="submit" class="uk-button uk-button-primary"><i v-spinner="saving" icon="save"></i>Opslaan</button>
            </div>


        </form>
    </v-modal>
</div>

</template>

<script>

    var defaultSettings = {
        'gls_customer_numbers': [],
        'glsserver': 'unibox.gls-netherlands.com',
        'glsport_live': 3033,
        'glsport_test': 3032,
        'gls_test': 0,
        'gls_tracking' : {
            encryptionCode: 123
        }
    };

    module.exports = {

        props: ['config', 'data'],

        data: function () {
            return {
                error: false,
                message: false,
                loading: false,
                saving: false,
                settings: {}
            }
        },

        methods: {
            saveSettings: function () {
                this.$set('error', '');
                this.saving = true;
                this.$http.post('/api/config', {
                    data: this.settings
                }).then(function (res) {
                    if (res.data) {
                        if (!_.isArray(res.data.gls_customer_numbers)) {
                            res.data.gls_customer_numbers = _.toArray(res.data.gls_customer_numbers);
                        }
                        this.$set('settings', res.data);
                        this.$set('message', 'Instellingen opgeslagen');
                        this.$refs.editsettingsmodal.close();
                    }
                    this.saving = false;
                }, function (res) {
                    this.saving = false;
                    this.$set('error', res.data.message || res.data);
                });
            },
            editSettings: function () {
                this.loading = true;
                this.$http.get('/api/config').then(function (res) {
                    if (res.data) {
                        if (!_.isArray(res.data.gls_customer_numbers)) {
                            res.data.gls_customer_numbers = _.toArray(res.data.gls_customer_numbers);
                        }
                        this.$set('settings', _.defaults(res.data, defaultSettings));
                        this.$refs.editsettingsmodal.open();
                    }
                    this.loading = false;
                }, function (res) {
                    this.loading = false;
                    this.$set('error', res.data.message || res.data);
                });
            }

        },

        fields1: {
            'glsUnibox': {
                type: 'title',
                title: 'GLS Unibox',
                attrs: {'class': 'uk-margin-remove'}
            },
            'gls_customer_numbers': {
                type: 'tags',
                label: 'GLS Klantnummers',
                style: 'list',
                attrs: {'class': 'uk-width-1-1'}
            },
            'gls_customer_number': {
                type: 'text',
                label: 'Standaard GLS Klantnummer *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'sap_number': {
                type: 'text',
                label: 'SAP nummer *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'glsserver': {
                type: 'text',
                label: 'GLS Server *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'glsport_live': {
                type: 'number',
                label: 'GLS port live',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'glsport_test': {
                type: 'number',
                label: 'GLS port test',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'gls_test': {
                type: 'select',
                label: 'Testmodus',
                options: {
                    'Ja': 1,
                    'Nee': 0
                },
                attrs: {'class': 'uk-form-width-medium'}
            }
        },

        fields2: {
            'glsTrackTrace': {
                type: 'title',
                title: 'GLS Track & Trace',
                attrs: {'class': 'uk-margin-remove'}
            },
            'gls_tracking.encryptionCode': {
                type: 'number',
                label: 'Encryptie Code *',
                attrs: {'class': 'uk-width-1-1', 'min': 100, 'max': 999, 'required': true}
            }

        }

    };


</script>