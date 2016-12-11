<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between">
        <div>
            <div class="uk-form-icon">
                <i class="uk-icon-search"></i>
                <input class="uk-margin-remove uk-form-width-medium" type="search" v-model="search" debounce="500">
            </div>
        </div>
        <div>
            <button class="uk-button" @click="editAddress(0)"><i class="uk-icon-plus uk-margin-small-right"></i>Nieuw adres</button>
        </div>
    </div>

    <table class="uk-table" v-show="addresses">
        <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Email/Telefoon</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="address in addresses">
            <td class="uk-text-center">{{ address.id }}</td>
            <td class="uk-text-center">
                <a :title="getStateName(address.state)" class="uk-icon-circle uk-margin-small-right" :class="{
                    'uk-text-danger': address.state == 0,
                    'uk-text-success': address.state == 1
                }" @click="toggleState(address)" data-uk-tooltip="{delay: 200}"></a>
            </td>
            <td>
                <strong>{{ address.name_1 }}</strong>
                <div v-if="address.address_name_2">{{ address.name_2 }}</div>
            </td>
            <td>
                {{ address.street }}<br/>
                {{ address.zip }}<br/>
                {{ address.city }} ({{ address.country }})
            </td>
            <td>
                {{ address.email }}<br/>
                {{ address.contact }}<br/>
                {{ address.phone | nrquotes }}
            </td>
            <td>
                <a @click="editAddress(address.id)" v-spinner="saving[address.id]" icon="edit" class="uk-icon-hover uk-margin-small-right"></a>
                <a v-spinner="deleting[address.id]" icon="trash-o" class="uk-icon-hover"
                        v-confirm="'Wilt u dit adres verwijderen?'"
                        @click="deleteAddress(address.id)"></a>
            </td>
        </tr>
        <tr v-show="!total">
            <td colspan="7" class="uk-text-center">
                <div class="uk-alert">Geen adressen gevonden.</div>
            </td>
        </tr>
        </tbody>
        <tfoot v-show="pages > 1">
        <tr>
            <td colspan="7">
                <v-pagination :page.sync="page" :pages="pages"></v-pagination>
            </td>
        </tr>
        </tfoot>
    </table>
    <div v-else class="uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></div>


    <v-modal v-ref:editaddressmodal :large="true">
        <div class="uk-modal-header"><h3>Adres</h3></div>

        <form class="uk-form" @submit.prevent="saveAddress">

            <div class="uk-grid">
                <div class="uk-width-medium-2-3 uk-form-horizontal">
                    <fields :config="$options.fields1" :model.sync="address" template="formrow"></fields>

                </div>
                <div class="uk-width-medium-1-3 uk-form-stacked">

                    <fields :config="$options.fields2" :model.sync="address" template="formrow"></fields>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
                <button type="submit" class="uk-button uk-button-primary"><i v-spinner="saving[address.id]" icon="save"></i>Opslaan</button>
            </div>


        </form>
    </v-modal>
</div>
</template>

<script>

    module.exports = {

        props: ['config', 'data'],

        data: function () {
            return {
                error: false,
                deleting: {},
                saving: {},
                editloading: false,
                address: {
                    id: 0,
                    data: {}
                },
                addresses: false,
                search: '',
                total: 0,
                pages: 0,
                page: 0,
            }
        },

        ready: function () {
            this.load();
            this.$watch('page + search', function () {
                this.load();
            })
        },

        methods: {
            load: function () {
                this.$set('error', '');
                this.$http.get('/api/address', {filter: {state: 1, search: this.search}, page: this.page}).then(function (res) {
                    this.addresses = res.data.addresses;
                    this.total = res.data.total;
                    this.pages = res.data.pages;
                    this.page = Number(res.data.page);
                }, function (res) {
                    this.$set('error', res.data.message || res.data);
                });
            },
            saveAddress: function () {
                this.$set('error', '');
                Vue.set(this.saving, this.address.id, true);
                this.$http.post('/api/address/save', {
                    data: this.address
                }).then(function (res) {
                    if (res.data.address) {
                        Vue.set(this.addresses, res.data.address.id, res.data.address);
                        this.$refs.editaddressmodal.close();
                    }
                    this.saving = {};
                }, function (res) {
                    this.saving = {};
                    this.$set('error', res.data.message || res.data);
                });
            },
            editAddress: function (id) {
                var address = _.find(this.addresses, 'id', id);
                this.$set('error', '');
                if (!address) {
                    this.$set('address', {
                        user_id: this.config.user.id,
                        country: 'NL',
                        state: 1,
                        data: {
                        }
                    });
                } else {
                    address.phone = String(address.phone).replace(new RegExp('"', 'g'), '');
                    this.$set('address', address);
                }
                this.$refs.editaddressmodal.open();
            },
            deleteAddress: function (id) {
                this.$set('error', '');
                Vue.set(this.deleting, id, true);
                this.$http.delete('/api/address/' + id).then(function () {
                    Vue.delete(this.addresses, id);
                    this.deleting = {};
                }, function (res) {
                    this.deleting = {};
                    this.$set('error', res.data.message || res.data);
                });
            },
            getStateName: function (state) {
                return this.data.address_states[state];
            },
            toggleState: function (address) {
                this.$set('error', '');
                address.state = Number(address.state) === 1 ? 0 : 1;
                this.address = address;
                this.saveAddress();
            }

        },

        fields1: {
            'name_1': {
                type: 'text',
                label: 'Naam 1 *',
                attrs: {'class': 'uk-width-1-1', 'maxlength': 30}
            },
            'name_2': {
                type: 'text',
                label: 'Naam 2',
                attrs: {'class': 'uk-width-1-1', 'maxlength': 30}
            },
            'street': {
                type: 'text',
                label: 'Adres',
                attrs: {'class': 'uk-width-1-1', 'maxlength': 30}
            },
            'zip': {
                type: 'text',
                label: 'Postcode',
                attrs: {'class': 'uk-width-1-1'}
            },
            'city': {
                type: 'text',
                label: 'Stad',
                attrs: {'class': 'uk-width-1-1', 'maxlength': 30}
            },
            'country': {
                type: 'select',
                label: 'Land',
                options: {
                    'Nederland': 'NL',
                    'België': 'BE',
                    'Duitsland': 'DE'
                },
                attrs: {'class': 'uk-form-width-large'}
            },
            'email': {
                type: 'email',
                label: 'Email',
                attrs: {'class': 'uk-width-1-1'}
            },
            'contact': {
                type: 'text',
                label: 'Contactpersoon',
                attrs: {'class': 'uk-width-1-1'}
            },
            'phone': {
                type: 'text',
                label: 'Telefoon',
                attrs: {'class': 'uk-width-1-1'}
            },
            'additional_text': {
                type: 'textarea',
                label: 'Extra informatie',
                attrs: {'class': 'uk-width-1-1', 'rows': 4}
            }
        },

        fields2: {
            'state': {
                type: 'radio',
                label: 'Actief',
                options: {
                    'Inactief': 0,
                    'Actief': 1
                },
                attrs: {}
            }
        },

        components: {
            'form-upload': require('./upload.vue')
        }


    };


</script>