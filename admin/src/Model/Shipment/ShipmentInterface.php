<?php

namespace Bixie\Devos\Model\Shipment;


interface ShipmentInterface
{
    /**
     * @return int
     */
    function getId ();

    /**
     * @return int
     */

    function getSenderId ();

    /**
     * @return string
     */
    function getKlantnummer ();

    /**
     * @return float
     */
    function getWeight ();

    /**
     * @return string
     */
    function getName ();
    /**
     * @return string
     */
    function getCompanyName ();

    /**
     * @return string
     */
    function getAddress ();

    /**
     * @return string
     */
    function getCity ();

    /**
     * @return string
     */
    function getCountry ();

    /**
     * @return string
     */
    function getEmail ();

    /**
     * @return string
     */
    function getCustomerReference ();

    /**
     * @return string
     */
    function getPdfUrl ();

    /**
     * @return string
     */
    function getPngUrl ();

    /**
     * @param $basePath
     * @param $pdfString
     */
    function savePdfString ($basePath, $pdfString);

    /**
     * @param $basePath
     * @param $pngString
     */
    function savePngString ($basePath, $pngString);
    /**
     * @param $base
     * @return string
     */
    function filePath ($base);

    /**
     * @return mixed
     */
    function getStatusName ();

    /**
     * @return array
     */
    static function getStatuses ();
}