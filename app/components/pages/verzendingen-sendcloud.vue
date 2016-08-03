<template>

    <div>
        <h1>Verzendingen Sendcloud</h1>

        <sendcloud-shipment :config="config"></sendcloud-shipment>
    </div>

</template>

<script>

    module.exports = {

        props: ['data', 'config'],

        components: {
            'sendcloud-shipment': require('../ui/sendcloud-shipment.vue')
        }

    };


</script>