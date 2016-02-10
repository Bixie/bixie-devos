<template>
<div>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between">
        <div>
            <div class="uk-search" data-uk-search>
                <input class="uk-search-field" type="search" v-model="search">
            </div>
        </div>
        <div>
            <button class="uk-button" @click="editSender(0)"><i class="uk-icon-plus uk-margin-small-right"></i>Nieuwe afzender</button>
        </div>
    </div>

    <table class="uk-table" v-show="senders">
        <thead>
        <tr>
            <th>#</th>
            <th></th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Postcode</th>
            <th>Plaats</th>
            <th>Logo</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="sender in senders | filterBy search | count">
            <td class="uk-text-center">{{ sender.id }}</td>
            <td class="uk-text-center">
                <a :title="getStateName(sender.state)" class="uk-icon-circle uk-margin-small-right" :class="{
                    'uk-text-danger': sender.state == 0,
                    'uk-text-success': sender.state == 1
                }" @click="toggleState(sender)" data-uk-tooltip="{delay: 200}"></a>
                <a :title="sender.def == 1 ? 'Standaard afzender' : ''" v-spinner="settingdefault" icon="star" :class="{
                    'uk-text-primary': sender.def == 1,
                    'uk-text-muted': sender.def == 0
                }" @click="setDefault(sender)" data-uk-tooltip="{delay: 200}"></a>
            </td>
            <td>
                <strong>{{ sender.sender_name_1 }}</strong>
                <div v-if="sender.sender_name_2">{{ sender.sender_name_2 }}</div>
            </td>
            <td>{{ sender.sender_street }}</td>
            <td>{{ sender.sender_zip }}</td>
            <td>{{ sender.sender_city }} ({{ sender.sender_country }})</td>
            <td> logo</td>
            <td>
                <a @click="editSender(sender.id)" v-spinner="saving[sender.id]" icon="edit" class="uk-icon-hover uk-margin-small-right"></a>
                <a v-spinner="deleting[sender.id]" icon="trash-o" class="uk-icon-hover"
                        v-confirm="'Wilt u deze afzender verwijderen?'"
                        @click="deleteSender(sender.id)"></a>
            </td>
        </tr>
        <tr v-show="!count">
            <td colspan="7" class="uk-text-center">
                <div class="uk-alert">Geen afzenders gevonden.</div>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-else class="uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></div>


    <v-modal v-ref:editsendermodal :large="true">
        <div class="uk-modal-header"><h3>Afzender</h3></div>

        <form class="uk-form" @submit.prevent="saveSender">

            <div class="uk-grid">
                <div class="uk-width-medium-2-3 uk-form-horizontal">
                    <fields :config="$options.fields1" :model.sync="sender" template="formrow"></fields>
                </div>
                <div class="uk-width-medium-1-3 uk-form-stacked">
                    image
                    <fields :config="$options.fields2" :model.sync="sender" template="formrow"></fields>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
                <button type="submit" class="uk-button uk-button-primary"><i v-spinner="saving[sender.id]" icon="save"></i>Opslaan</button>
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
                settingdefault: false,
                editloading: false,
                sender: {
                    id: 0,
                    data: {}
                },
                senders: false,
                search: '',
                count: 0
            }
        },

        ready: function () {
            this.load(this.page);
        },

        filters: {
            count: function (senders) {
                this.count = _.size(senders);
                return senders;
            }
        },

        methods: {
            load: function (page) {
                this.$set('error', '');
                this.$http.get('/api/sender', {inactive: 1}).then(function (res) {
                    this.$set('senders', res.data.senders);
                }, function (res) {
                    this.$set('error', res.data.message || res.data);
                });
            },
            saveSender: function () {
                this.$set('error', '');
                Vue.set(this.saving, this.sender.id, true);
                this.$http.post('/api/sender/save', {
                    data: this.sender
                }).then(function (res) {
                    if (res.data.sender) {
                        Vue.set(this.senders, res.data.sender.id, res.data.sender);
                        this.$refs.editsendermodal.close();
                    }
                    this.saving = {};
                }, function (res) {
                    this.saving = {};
                    this.$set('error', res.data.message || res.data);
                });
            },
            editSender: function (id) {
                var sender = _.find(this.senders, 'id', id);
                this.$set('error', '');
                if (!sender) {
                    this.$set('sender', {
                        user_id: this.config.user.id,
                        sender_country: 'NL',
                        state: 1,
                        def: 0,
                        data: {}
                    });
                } else {
                    this.$set('sender', sender);
                }
                this.$refs.editsendermodal.open();
            },
            deleteSender: function (id) {
                this.$set('error', '');
                Vue.set(this.deleting, id, true);
                this.$http.delete('/api/sender/' + id).then(function () {
                    Vue.delete(this.senders, id);
                    this.deleting = {};
                }, function (res) {
                    this.deleting = {};
                    this.$set('error', res.data.message || res.data);
                });
            },
            getStateName: function (state) {
                return this.data.sender_states[state];
            },
            setDefault: function (sender) {
                this.$set('error', '');
                if (Number(sender.state) == 0) {
                    this.$set('error', 'Standaard afzender moet gepubliceerd zijn');
                    return;
                }
                this.settingdefault = true;
                this.$http.post('/api/sender/setdefault', {
                    id: sender.id
                }).then(function (res) {
                    this.$set('senders', res.data.senders);
                    this.settingdefault = false;
                }, function (res) {
                    this.settingdefault = false;
                    this.$set('error', res.data.message || res.data);
                });
            },
            toggleState: function (sender) {
                this.$set('error', '');
                if (Number(sender.def) === 1 && Number(sender.state) === 1) {
                    this.$set('error', 'Standaard afzender moet gepubliceerd zijn');
                    return;
                }
                sender.state = Number(sender.state) === 1 ? 0 : 1;
                this.sender = sender;
                this.saveSender();
            }

        },

        fields1: {
            'sender_name_1': {
                type: 'text',
                label: 'Naam 1 *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'sender_name_2': {
                type: 'text',
                label: 'Naam 2',
                attrs: {'class': 'uk-width-1-1'}
            },
            'sender_street': {
                type: 'text',
                label: 'Adres *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'sender_zip': {
                type: 'text',
                label: 'Postcode *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'sender_city': {
                type: 'text',
                label: 'Stad *',
                attrs: {'class': 'uk-width-1-1', 'required': true}
            },
            'sender_country': {
                type: 'select',
                label: 'Land *',
                options: {
                    'Nederland': 'NL',
                    'België': 'BE',
                    'Duitsland': 'DE'
                },
                attrs: {'class': 'uk-form-width-large', 'required': true}
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
        }

    };


</script>