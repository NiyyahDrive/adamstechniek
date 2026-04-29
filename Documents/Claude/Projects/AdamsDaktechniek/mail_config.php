<?php
/**
 * SMTP credentials voor Adams Daktechniek contactformulier.
 * Dit bestand is via .htaccess afgeschermd voor directe HTTP-toegang.
 * NOOIT committen naar publieke git of delen via plain channels.
 */

if (!defined('FOURSEASONS_INTERNAL')) {
    http_response_code(403);
    exit('Forbidden');
}

return [
    'host'       => 'smtp.mijndomein.nl',
    'port'       => 587,
    'encryption' => 'tls',           // STARTTLS op 587
    'username'   => 'no-reply@adamstechniek.nl',
    'password'   => 'Neveragain26!!',
    'from_email' => 'no-reply@adamstechniek.nl',
    'from_name'  => 'Adams Daktechniek',
    'to_email'   => 'info@adamstechniek.nl',
    'to_name'    => 'Adams Daktechniek',
];
