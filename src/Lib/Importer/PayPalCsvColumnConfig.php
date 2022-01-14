<?php

namespace App\Lib\Importer;

class PayPalCsvColumnConfig
{
    const CSV_DATABASE_COMPARE_KEY = 'transaktionscode';
    const DB_MAX_STRING_LENGTH = 255;

    public static array $columnConfig = [
        [
            'paypalExportColumn' => 'datum',
            'columnIndex' => false,
            'mandatory' => true,
            'associatedEntityColumn' => 'bookingDate',
            'formatCallback' => 'parseDate',
        ],
        [
            'paypalExportColumn' => 'uhrzeit',
            'columnIndex' => false,
            'mandatory' => true,
            'associatedEntityColumn' => 'bookingTime',
            'formatCallback' => 'parseTime',
        ],
        [
            'paypalExportColumn' => 'brutto',
            'columnIndex' => false,
            'mandatory' => true,
            'associatedEntityColumn' => 'amount',
            'formatCallback' => 'parseAmount',
        ],
        [
            'paypalExportColumn' => self::CSV_DATABASE_COMPARE_KEY,
            'columnIndex' => false,
            'mandatory' => true,
            'associatedEntityColumn' => 'transactionCode',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'typ',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'type',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'name',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'name',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'empfänger e-mail-adresse',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'recipient',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'artikelbezeichnung',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'articleDescription',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'artikelnummer',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'articleNumber',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'zugehöriger transaktionscode',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'associatedTransactionCode',
            'formatCallback' => 'parseGenericString',
        ],
        [
            'paypalExportColumn' => 'rechnungsnummer',
            'columnIndex' => false,
            'mandatory' => false,
            'associatedEntityColumn' => 'invoiceNumber',
            'formatCallback' => 'parseGenericString',
        ],
    ];
}