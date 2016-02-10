<template>

    <h1>Verzendingen</h1>

    <gls-shipment :config="config"></gls-shipment>

</template>

<script>

    module.exports = {

        props: ['data', 'config'],

        components: {
            'gls-shipment': require('../ui/gls-shipment.vue')
        }

    };


</script>