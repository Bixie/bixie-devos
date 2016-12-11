<template>

    <div>
        <h1>Adressen</h1>

        <devos-addresses :config="config" :data="data"></devos-addresses>
    </div>


</template>

<script>

    module.exports = {

        props: ['data', 'config'],

        components: {
            'devos-addresses': require('../ui/addresses.vue')
        }

    };


</script>