<template>

    <div v-if="error" class="uk-alert uk-alert-danger">{{ error }}</div>

    <button class="uk-button" @click="editShipment(0)">Nieuwe GLS verzending</button>

    <table class="uk-table" v-show="shipments">
        <thead>
        <tr>
            <th>Pakket</th>
            <th>Ontvanger</th>
            <th>Verzender</th>
            <th>Status</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="4">
                paginatie
            </td>
        </tr>
        </tfoot>
        <tbody>
        <tr v-for="shipment in shipments | count">
            <td>
                lokaal ID: {{ shipment.id }}<br>
                NL pakket nummer: {{ shipment.domestic_parcel_number_nl }}<br>
                GLS pakket nummer: {{ shipment.gls_parcel_number }}<br>
                Gewicht: {{ shipment.parcel_weight }}<br>
                {{ shipment.date_of_shipping | date }}
            </td>
            <td>
                {{ shipment.receiver_contact }}<br>
                {{ shipment.receiver_phone }}<br>
                {{ shipment.receiver_name_1 }}<br>
                {{ shipment.receiver_name_2 }}<br>
                {{ shipment.receiver_name_3 }}<br>
                {{ shipment.receiver_street }}<br>
                {{ shipment.receiver_zip_code }}<br>
                {{ shipment.receiver_place }}<br>
            </td>
            <td>
                {{ shipment.sender_name_1 }}<br>
                {{ shipment.sender_name_2 }}<br>
                {{ shipment.sender_street }}<br>
                {{ shipment.sender_zip }}<br>
                {{ shipment.sender_city }}<br>
                {{ shipment.sender_country }}<br>

            </td>
            <td>
                {{ shipment.state }}<br>
                {{ shipment.data.inbound_country_code }}<br>

                <button class="uk-button" @click="editShipment(shipment.id)">Bewerken</button><br>
                <button v-if="shipment.gls_parcel_number == 0" class="uk-button uk-button-primary" @click="sendShipment(shipment.id)">Verzenden</button>
                <button v-else class="uk-button uk-button-primary" @click="getLabel(shipment.id)">Label maken</button>
            </td>
        </tr>
        <tr v-show="!count">
            <td colspan="4" class="uk-text-center">
                <div class="uk-alert">Geen verzendingen gevonden.</div>
            </td>
        </tr>
        </tbody>
    </table>


    <v-modal v-ref:editshipmentmodal :large="true">
        <div class="uk-modal-header"><h3>GLS verzending</h3></div>

        <form class="uk-form uk-form-stacked" @submit.prevent="saveShipment">

            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-1-2">
                    <fields :config="$options.fields1" :model.sync="shipment" template="formrow"></fields>
                </div>
                <div class="uk-width-medium-1-2">
                    <fields :config="$options.fields2" :model.sync="shipment" template="formrow"></fields>
                </div>
            </div>

            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-1-2">
                    <fields :config="$options.fields3" :model.sync="shipment" template="formrow"></fields>
                </div>
                <div class="uk-width-medium-1-2">
                    <fields :config="$options.fields4" :model.sync="shipment" template="formrow"></fields>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-margin-small-right uk-modal-close">Sluiten</button>
                <button type="submit" class="uk-button uk-button-primary">Opslaan</button>
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
                shipment: {
                    id: 0,
                    data: {
                        key: 'value'
                    }
                },
                shipments: false,
                count: 0,
                total: 0,
                page: 1
            }
        },

        ready: function () {
            this.load(this.page);
        },

        filters: {
            count: function (shipments) {
                this.count = _.size(shipments);
                return shipments;
            }
        },

        methods: {
            load: function (page) {
                this.$http.get('/shipmentgls', {page: this.page}).then(function (res) {
                    if (res.data.shipments !== undefined) {
                        this.$set('shipments', res.data.shipments);
                        this.$set('total', res.data.total);
                        this.$set('page', res.data.page);
                    }
                }, function (res) {
                    console.warn(res.data);
                });
            },
            saveShipment: function () {
                this.$http.post('/shipmentgls/save', {
                    data: this.shipment
                }).then(function (res) {
                    if (res.data.shipment) {
                        this.shipments[data.shipment.id] = res.data.shipment;
                        this.$refs.editshipmentmodal.close();
                    }

                }, function (res) {
                    console.warn(res.data);
                });
            },
            sendShipment: function (id) {
                this.$set('error', '');
                this.$http.post('/shipmentgls/send/' + id).then(function (res) {
                    if (res.data.error) {
                        this.$set('error', res.data.error);
                    }
                    if (res.data.shipment) {
                        this.shipments[res.data.shipment.id] = res.data.shipment;
                    }

                }, function (res) {
                    console.warn(res.data);
                });
            },
            getLabel: function (id) {
                this.$set('error', '');
                this.$http.post('/shipmentgls/label/' + id).then(function (res) {
                    if (res.data.error) {
                        this.$set('error', res.data.error);
                    }
                    if (res.data.shipment) {
                        this.shipments[res.data.shipment.id] = res.data.shipment;
                    }

                }, function (res) {
                    console.warn(res.data);
                });
            },
            editShipment: function (id) {
                var shipment = _.find(this.shipments, 'id', id);
                if (!shipment) {
                    this.$http.get('/shipmentgls/' + id).then(function (res) {
                        if (res.data.id !== undefined) {
                            this.$set('shipment', res.data);
                            this.$refs.editshipmentmodal.open();
                        }
                    }, function (res) {
                        console.warn(res.data);
                    });
                } else {
                    this.$set('shipment', shipment);
                    this.$refs.editshipmentmodal.open();
                }
            }

        },

        fields1: {
            'product_short_description': {
                type: 'select',
                label: 'Product type *',
                options: {
                    'Business parcel': 'BP',
                    'Express parcel': 'EP',
                    'Euro business parcel': 'EBP'
                },
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'data.label_template': {
                type: 'select',
                label: 'Print template',
                options: {
                    'GLS standaard': 'gls_default'
                },
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'data.inbound_country_code': {
                type: 'select',
                label: 'Land verzending *',
                options: {
                    'Nederland': 'NL'
                },
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'data.express_flag': {
                type: 'radio',
                label: 'Express pakket',
                options: {
                    'Geen express': '',
                    'Volgende dag 9.00 uur': 'T9',
                    'Volgende dag 12.00 uur': 'T12'
                },
                attrs: {}
            },
            'data.express_service_flag': {
                type: 'checkbox',
                label: 'Express service',
                optionlabel: 'Geef express service aan',
                attrs: {}
            },
            'parcel_weight': {
                type: 'number',
                label: 'Gewicht pakket',
                attrs: {'class': 'uk-form-width-medium uk-text-right', 'min': '0', 'step': '0.1', 'required': true}
            },
            'receiver_zip_code': {
                type: 'text',
                label: 'Postcode ontvanger *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'receiver_contact': {
                type: 'text',
                label: 'Contact ontvanger',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'receiver_phone': {
                type: 'text',
                label: 'Telefoon ontvanger',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'customer_reference': {
                type: 'text',
                label: 'Klantreferentie',
                attrs: {'class': 'uk-form-width-medium'}
            }
        },

        fields2: {
            'receiver_name_1': {
                type: 'text',
                label: 'Ontvanger naam 1 *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'receiver_name_2': {
                type: 'text',
                label: 'Ontvanger naam 2',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'receiver_name_3': {
                type: 'text',
                label: 'Ontvanger naam 3',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'receiver_street': {
                type: 'text',
                label: 'Ontvanger adres *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'receiver_place': {
                type: 'text',
                label: 'Ontvanger plaats *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'additional_text_1': {
                type: 'textarea',
                label: 'Aanvullende tekst 1',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'additional_text_2': {
                type: 'textarea',
                label: 'Aanvullende tekst 2',
                attrs: {'class': 'uk-form-width-medium'}
            }
        },

        fields3: {
            'parcel_sequence': {
                type: 'number',
                label: 'Pakket reeks',
                attrs: {'class': 'uk-form-width-small uk-text-right', 'min': '0'}
            },
            'parcel_quantity': {
                type: 'number',
                label: 'Pakket aantal',
                attrs: {'class': 'uk-form-width-small uk-text-right', 'min': '0'}
            },
            'state': {
                type: 'select',
                label: 'Actief',
                options: {
                    'Ja': 1,
                    'Nee': 0
                },
                attrs: {'class': 'uk-form-width-medium'}
            },
            'date_of_shipping': {
                type: 'text',
                label: 'Verzenddatum',
                attrs: {'class': 'uk-form-width-medium uk-form-blank', 'readonly': true}
            },
            'gls_parcel_number': {
                type: 'number',
                label: 'GLS pakket nummer',
                attrs: {'class': 'uk-form-width-medium uk-form-blank', 'readonly': true}
            },
            'domestic_parcel_number_nl': {
                type: 'text',
                label: 'Domestic pakket nummer NL',
                attrs: {'class': 'uk-form-width-medium uk-form-blank', 'readonly': true}
            },
            'sap_number': {
                type: 'text',
                label: 'SAP nummer',
                attrs: {'class': 'uk-form-width-medium uk-form-blank', 'readonly': true}
            }
        },

        fields4: {
            'sender_name_1': {
                type: 'text',
                label: 'Afzender naam 1 *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'sender_name_2': {
                type: 'text',
                label: 'Afzender naam 2',
                attrs: {'class': 'uk-form-width-medium'}
            },
            'sender_street': {
                type: 'text',
                label: 'Afzender adres *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'sender_zip': {
                type: 'text',
                label: 'Afzender postcode *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'sender_city': {
                type: 'text',
                label: 'Afzender plaats *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            },
            'sender_country': {
                type: 'text',
                label: 'Afzender land *',
                attrs: {'class': 'uk-form-width-medium', 'required': true}
            }
        }
    };


</script>