<template>

    <h1>Dashboard {{ config.user.bedrijfsnaam }}</h1>

    <h3>{{ config.user.name }}</h3>
    <h4>{{ config.user.klantnummer }}</h4>

</template>

<script>

    module.exports = {

        props: ['data', 'config']

    };


</script>