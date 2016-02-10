<template>

    <h1>Afzenders</h1>

    <devos-senders :config="config" :data="data"></devos-senders>

</template>

<script>

    module.exports = {

        props: ['data', 'config'],

        components: {
            'devos-senders': require('../ui/senders.vue')
        }

    };


</script>