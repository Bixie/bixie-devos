<template>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <div class="uk-flex uk-flex-space-between">
        <div>
            <div class="uk-search" data-uk-search>
                <input class="uk-search-field" type="search" v-model="search">
            </div>
        </div>
        <div>
            <button class="uk-button" @click="editSender(0)"><i v-spinner="editloading" icon="plus"></i>Nieuwe afzender</button>
        </div>
    </div>

    <table class="uk-table" v-show="senders">
        <thead>
        <tr>
            <th>#</th>
            <th>Status</th>
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
                <i class="uk-icon-circle" :class="{
                    'uk-text-danger': sender.state == 0,
                    'uk-text-success': sender.state == 1
                }"></i>
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

</template>

<script>

    module.exports = {

        props: ['config'],

        data: function () {
            return {
                error: false,
                deleting: {},
                saving: {},
                editloading: false,
                sender: {
                    id: 0,
                    data: {
                        key: 'value'
                    }
                },
                senders: false,
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
                this.$http.get('/api/sender').then(function (res) {
                    if (res.data.senders !== undefined) {
                        this.$set('senders', res.data.senders);
                    }
                }, function (res) {
                    console.warn(res.data);
                });
            },
            saveSender: function () {
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
                    console.warn(res.data);
                });
            },
            editSender: function (id) {
                var sender = _.find(this.senders, 'id', id);
                if (!sender) {
                    this.editloading = true;
                    this.$http.get('/api/sender/' + id).then(function (res) {
                        if (res.data.id !== undefined) {
                            this.$set('sender', res.data);
                            this.$refs.editsendermodal.open();
                        }
                        this.editloading = false;
                    }, function (res) {
                        this.editloading = false;
                        console.warn(res.data);
                    });
                } else {
                    this.$set('sender', sender);
                    this.$refs.editsendermodal.open();
                }
            },
            deleteSender: function (id) {
                Vue.set(this.deleting, id, true);
                this.$http.delete('/api/sender/' + id).then(function () {
                    Vue.delete(this.senders, id);
                    this.deleting = {};
                }, function (res) {
                    this.deleting = {};
                    console.warn(res.data);
                });
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