<template>

    <h1>Dashboard {{ config.user.bedrijfsnaam }}</h1>

    <h5>Uw gebruikersnaam:  {{ config.user.name }}</h5>
    <h5>Uw klantnummer:     {{ config.user.klantnummer }}</h5>

    <div class="uk-text-center">
        <p>Om een pakket aan te melden begint u met het aanmaken van een afzender. Vervolgens kunt u een verzending aanmelden.</p>
        <a href="/beheer#afzenders" class="uk-button">Afzender aanmaken</a>
        <a href="/beheer#verzendingen" class="uk-button">Verzending aanmelden</a>
    </div>

    <div class="uk-panel uk-panel-box uk-margin-top">
        <div class="uk-panel-badge uk-badge">Let op</div>
        <h3 class="uk-panel-title">Pakketinformatie</h3>
        <p>
            Maximale lengte: 2 meter <br />
            Maximale omvang: 3 meter (De omvang is 1x lengte + 2 keer breedte + 2x hoogte) <br />
            Maximale aansprakelijkheid: € 500,00 met een eigen risico van € 50,00
        </p>

        <p>Voor vragen over de werking van deze applicatie, het doorgeven van
            foutmeldingen of het indienen van een reclamatie-aanvraag kunt u hier tercht: <br />
        </p>
        <div class="uk-text-right"><a class="uk-button uk-button-primary" href="/mijn-account/pakket-support">Support</a></div>
    </div>

</template>

<script>

    module.exports = {

        props: ['data', 'config']

    };


</script>