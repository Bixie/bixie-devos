<?php
/**
 * @var $data
 * $var $user
 */

?>

<h2>Melding pakketverzending</h2>

<p>Er is een pakket aangemeld op de website met de volgende gegevens:</p>

<dl>
    <dt>Klantreferentie</dt>
    <dd><?= $data['customer_reference'] ?></dd>
    <dt>Tijdstip aanmelding</dt>
    <dd><?= \JHtml::_('date', $data['date_of_shipping'], JText::_('DATE_FORMAT_LC2')) ?></dd>
</dl>

<h3>Afzender</h3>
<dl>
    <dt>De Vos klantnummer</dt>
    <dd><?= $data['klantnummer'] ?></dd>
    <dt>Klant naam</dt>
    <dd><?= $user['name'] ?></dd>
    <dt>Afzender naam</dt>
    <dd><?= $data['sender_name_1'] ?></dd>
    <dt>Adres</dt>
    <dd><?= $data['sender_street'] ?></dd>
    <dd><?= $data['sender_zip'] ?> <?= $data['sender_city'] ?></dd>
    <dt>Emailadres</dt>
    <dd><?= $data['sender_email'] ?: '-' ?></dd>
    <dt>Telefoon</dt>
    <dd><?= $data['data']['sender_phone'] ?: '-' ?></dd>
</dl>

<h3>Geadresseerde</h3>
<dl>
    <dt>Naam</dt>
    <dd><?= $data['receiver_name_1'] ?></dd>
    <dt>Adres</dt>
    <dd><?= $data['receiver_street'] ?></dd>
    <dd><?= $data['receiver_zip_code'] ?> <?= $data['receiver_place'] ?></dd>
    <dt>Email</dt>
    <dd><?= !empty($data['receiver_email']) ? $data['receiver_email'] : '-' ?></dd>
</dl>
