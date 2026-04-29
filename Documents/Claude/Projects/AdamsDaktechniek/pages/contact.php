<?php
/**
 * contact.php — Adams Daktechniek
 * Verwerkt het contactformulier en stuurt e-mail naar info@adamstechniek.nl
 */

// ─── Configuratie ────────────────────────────────────────────────────────────
define('ONTVANGER',   'info@adamstechniek.nl');
define('AFZENDER',    'noreply@adamstechniek.nl');
define('SITE_NAAM',   'Adams Daktechniek');
define('SITE_URL',    'https://adamstechniek.nl/pages/contact.html');
define('BEDANKT_URL', 'https://adamstechniek.nl/pages/bedankt.html');

// ─── Helpers ─────────────────────────────────────────────────────────────────
function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function is_valid_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_phone(string $phone): bool {
    // Belgische nummers: +32... / 0... / spaties/koppeltekens toegestaan
    return (bool) preg_match('/^[\+0][0-9 \-\.\/]{7,19}$/', $phone);
}

// ─── Alleen POST accepteren ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL);
    exit;
}

// ─── CSRF / honeypot controle ────────────────────────────────────────────────
$honeypot = isset($_POST['website']) ? trim($_POST['website']) : '';
if ($honeypot !== '') {
    // Bot ingevuld — stil negeren
    header('Location: ' . BEDANKT_URL);
    exit;
}

// ─── Invoer verzamelen & opschonen ───────────────────────────────────────────

// Diensten (verplicht — minstens één)
$dienst_allowed = [
    'ruitenwassen'         => 'Ruitenwassen',
    'zonnepanelen'         => 'Zonnepanelen reinigen',
    'dakgoten'             => 'Dakgoten reinigen',
    'dakgoten & corniches' => 'Dakgoten & corniches',
    'gevelreiniging'       => 'Gevelreiniging',
    'verandareiniging'     => 'Verandareiniging',
    'veranda'              => 'Verandareiniging',
    'terrasreiniging'      => 'Terrasreiniging',
    'terrassen'            => 'Terrasreiniging',
    'rolluiken'            => 'Rolluiken reinigen',
    'koepels'              => 'Koepels reinigen',
    'dakontmossen'         => 'Dak ontmossen',
    'dakontmossing'        => 'Dak ontmossen',
];
$dienst_raw_input = $_POST['dienst'] ?? [];
// Backwards compat: ondersteun zowel array (nieuwe form) als string (oudere of fallback)
if (!is_array($dienst_raw_input)) {
    $dienst_raw_input = $dienst_raw_input !== '' ? [$dienst_raw_input] : [];
}
$diensten = [];
foreach ($dienst_raw_input as $d) {
    $key = strtolower(trim((string)$d));
    if (isset($dienst_allowed[$key]) && !in_array($key, array_column($diensten, 'key'), true)) {
        $diensten[] = ['key' => $key, 'label' => $dienst_allowed[$key]];
    }
}

// Extra diensten (checkboxes, optioneel) — sleutel = lowercase form-value
$extra_allowed = [
    'koepels reinigen'     => 'Koepels reinigen',
    'rolluiken reinigen'   => 'Rolluiken reinigen',
    'dak ontmossen'        => 'Dak ontmossen',
    'terrassen & opritten' => 'Terrassen & opritten',
    'verandareiniging'     => 'Verandareiniging',
];
$extra_raw = isset($_POST['extra']) && is_array($_POST['extra']) ? $_POST['extra'] : [];
$extra     = [];
foreach ($extra_raw as $e) {
    $key = strtolower(trim((string)$e));
    if (isset($extra_allowed[$key]) && !in_array($extra_allowed[$key], $extra, true)) {
        $extra[] = clean($extra_allowed[$key]);
    }
}

// Methode (optioneel)
$methode_allowed = ['osmose', 'traditioneel', 'advies'];
$methode_raw     = isset($_POST['methode']) ? strtolower(trim($_POST['methode'])) : 'advies';
$methode         = in_array($methode_raw, $methode_allowed, true) ? clean($methode_raw) : 'advies';

// Adres
$straat   = clean($_POST['straat']   ?? '');
$postcode = clean($_POST['postcode'] ?? '');
$gemeente = clean($_POST['gemeente'] ?? '');

// Contactgegevens (verplicht)
$naam     = clean($_POST['naam']     ?? '');
$telefoon = clean($_POST['telefoon'] ?? '');
$email    = clean($_POST['email']    ?? '');

// B2B (optioneel)
$bedrijf     = clean($_POST['bedrijf']     ?? '');
$btw         = clean($_POST['btw']         ?? '');
$contact_rol = clean($_POST['contact_rol'] ?? '');

// Bericht (optioneel)
$bericht = clean($_POST['bericht'] ?? '');

// Consent (verplicht)
$consent = isset($_POST['consent']) && $_POST['consent'] === '1';

// ─── Validatie ────────────────────────────────────────────────────────────────
$fouten = [];

if (empty($diensten)) {
    $fouten[] = 'Selecteer minstens één dienst.';
}
if (empty($naam)) {
    $fouten[] = 'Uw naam is verplicht.';
}
if (empty($telefoon)) {
    $fouten[] = 'Uw telefoonnummer is verplicht.';
} elseif (!is_valid_phone($telefoon)) {
    $fouten[] = 'Voer een geldig Belgisch telefoonnummer in.';
}
if (empty($email)) {
    $fouten[] = 'Uw e-mailadres is verplicht.';
} elseif (!is_valid_email($email)) {
    $fouten[] = 'Voer een geldig e-mailadres in.';
}
if (!$consent) {
    $fouten[] = 'U dient akkoord te gaan met het privacybeleid.';
}

// Bij fouten: terug naar formulier met foutmelding in session of query string
if (!empty($fouten)) {
    $fout_str = urlencode(implode(' | ', $fouten));
    header('Location: ' . SITE_URL . '?fout=' . $fout_str);
    exit;
}

// ─── Labels / weergavewaarden ─────────────────────────────────────────────────
$methode_labels = [
    'osmose'       => 'Osmose (zuiver water)',
    'traditioneel' => 'Traditioneel',
    'advies'       => 'Laat mij adviseren',
];

// Hoofd-diensten (kan meerdere bevatten)
$dienst_labels_arr = array_map(fn($d) => $d['label'], $diensten);
$dienst_label      = implode(', ', $dienst_labels_arr);
$dienst_first      = $dienst_labels_arr[0] ?? '';
$methode_label     = $methode_labels[$methode] ?? ucfirst($methode);
$extra_labels      = !empty($extra) ? implode(', ', array_map('ucfirst', $extra)) : '—';

// ─── E-mail opbouwen (plain-text + HTML) ─────────────────────────────────────
$onderwerp_dienst = count($diensten) > 1
    ? $dienst_first . ' +' . (count($diensten) - 1) . ' andere'
    : $dienst_first;
$onderwerp = '🧹 Nieuwe offerte-aanvraag: ' . $onderwerp_dienst
           . ($gemeente ? ' in ' . $gemeente : '')
           . ' — ' . SITE_NAAM;

// ── Plain-text versie ──
$adres_str = trim("$straat, $postcode $gemeente");
if ($adres_str === ',') $adres_str = '—';

$txt  = "Nieuwe aanvraag via het contactformulier op adamstechniek.nl\n";
$txt .= str_repeat('=', 60) . "\n\n";
$txt .= "GEVRAAGDE DIENST(EN)\n";
if (count($diensten) > 1) {
    $txt .= "  Hoofd:\n";
    foreach ($diensten as $d) {
        $txt .= "    • " . $d['label'] . "\n";
    }
} else {
    $txt .= "  Hoofd: $dienst_label\n";
}
$txt .= "  Extra: $extra_labels\n";
$txt .= "  Methode: $methode_label\n\n";
$txt .= "ADRES\n";
$txt .= "  " . (empty(trim("$straat $postcode $gemeente")) ? '—' : trim("$straat, $postcode $gemeente")) . "\n\n";
$txt .= "CONTACTGEGEVENS\n";
$txt .= "  Naam:     $naam\n";
$txt .= "  Telefoon: $telefoon\n";
$txt .= "  E-mail:   $email\n\n";

if ($bedrijf || $btw || $contact_rol) {
    $txt .= "B2B INFORMATIE\n";
    if ($bedrijf)     $txt .= "  Bedrijf:  $bedrijf\n";
    if ($btw)         $txt .= "  BTW-nr:   $btw\n";
    if ($contact_rol) $txt .= "  Rol:      $contact_rol\n";
    $txt .= "\n";
}

if ($bericht) {
    $txt .= "BERICHT\n  $bericht\n\n";
}

$txt .= str_repeat('-', 60) . "\n";
$txt .= "Ontvangen op: " . date('d/m/Y \o\m H:i') . "\n";
$txt .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'onbekend') . "\n";

// ── HTML versie ──
function row(string $label, string $value): string {
    if (empty(trim($value)) || $value === '—') return '';
    return "<tr>
        <td style='padding:6px 12px 6px 0;color:#666;font-size:14px;white-space:nowrap;vertical-align:top'><strong>$label</strong></td>
        <td style='padding:6px 0;font-size:14px;color:#111'>$value</td>
    </tr>\n";
}

$adres_html = trim("$straat, $postcode $gemeente");
if (trim($adres_html, ', ') === '') $adres_html = '—';

$html  = '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f6fa;font-family:Arial,sans-serif">';
$html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fa;padding:32px 0">';
$html .= '<tr><td align="center">';
$html .= '<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">';

// Header
$html .= '<tr><td style="background:linear-gradient(135deg,#0b3d91,#1a73e8);padding:28px 32px">';
$html .= '<h1 style="margin:0;color:#fff;font-size:22px">🧹 Nieuwe offerte-aanvraag</h1>';
$html .= '<p style="margin:6px 0 0;color:#c8ddf5;font-size:14px">' . SITE_NAAM . ' — ' . date('d/m/Y \o\m H:i') . '</p>';
$html .= '</td></tr>';

// Body
$html .= '<tr><td style="padding:28px 32px">';

// Dienst block
$diensten_html = '';
if (count($diensten) > 1) {
    $chips = [];
    foreach ($diensten as $d) {
        $chips[] = '<span style="display:inline-block;background:#e8f0fe;color:#0b3d91;padding:3px 10px;border-radius:12px;font-size:13px;font-weight:600;margin:2px 4px 2px 0">' . htmlspecialchars($d['label'], ENT_QUOTES, 'UTF-8') . '</span>';
    }
    $diensten_html = implode('', $chips);
} else {
    $diensten_html = $dienst_label;
}
$dienst_label_kop = count($diensten) > 1 ? 'Gevraagde diensten' : 'Gevraagde dienst';
$dienst_row_label = count($diensten) > 1 ? 'Hoofd (' . count($diensten) . '):' : 'Hoofd:';
$html .= '<h2 style="margin:0 0 16px;font-size:16px;color:#0b3d91;border-bottom:2px solid #e8f0fe;padding-bottom:8px">' . $dienst_label_kop . '</h2>';
$html .= '<table cellpadding="0" cellspacing="0">';
$html .= row($dienst_row_label, $diensten_html);
$html .= row('Extra:', $extra_labels);
$html .= row('Methode:', $methode_label);
$html .= '</table>';

// Adres block
$html .= '<h2 style="margin:20px 0 16px;font-size:16px;color:#0b3d91;border-bottom:2px solid #e8f0fe;padding-bottom:8px">Adres</h2>';
$html .= '<table cellpadding="0" cellspacing="0">';
$html .= row('Straat:', $straat ?: '—');
$html .= row('Postcode:', $postcode ?: '—');
$html .= row('Gemeente:', $gemeente ?: '—');
$html .= '</table>';

// Contact block
$html .= '<h2 style="margin:20px 0 16px;font-size:16px;color:#0b3d91;border-bottom:2px solid #e8f0fe;padding-bottom:8px">Contactgegevens</h2>';
$html .= '<table cellpadding="0" cellspacing="0">';
$html .= row('Naam:', $naam);
$html .= row('Telefoon:', '<a href="tel:' . preg_replace('/[^0-9\+]/', '', $telefoon) . '" style="color:#1a73e8">' . $telefoon . '</a>');
$html .= row('E-mail:', '<a href="mailto:' . $email . '" style="color:#1a73e8">' . $email . '</a>');
$html .= '</table>';

// B2B block (alleen tonen als ingevuld)
if ($bedrijf || $btw || $contact_rol) {
    $html .= '<h2 style="margin:20px 0 16px;font-size:16px;color:#0b3d91;border-bottom:2px solid #e8f0fe;padding-bottom:8px">B2B informatie</h2>';
    $html .= '<table cellpadding="0" cellspacing="0">';
    $html .= row('Bedrijf:', $bedrijf);
    $html .= row('BTW-nummer:', $btw);
    $html .= row('Rol:', $contact_rol);
    $html .= '</table>';
}

// Bericht block (alleen tonen als ingevuld)
if ($bericht) {
    $html .= '<h2 style="margin:20px 0 16px;font-size:16px;color:#0b3d91;border-bottom:2px solid #e8f0fe;padding-bottom:8px">Bericht</h2>';
    $html .= '<p style="font-size:14px;color:#333;background:#f8faff;border-left:4px solid #1a73e8;padding:12px 16px;margin:0;border-radius:0 6px 6px 0">'
           . nl2br($bericht) . '</p>';
}

// CTA knop
$html .= '<div style="margin:28px 0 8px;text-align:center">';
$html .= '<a href="mailto:' . $email . '" style="display:inline-block;background:#1a73e8;color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:bold;font-size:15px">Beantwoord deze aanvraag</a>';
$html .= '</div>';

$html .= '</td></tr>';

// Footer
$html .= '<tr><td style="background:#f4f6fa;padding:16px 32px;text-align:center;font-size:12px;color:#999">';
$html .= SITE_NAAM . ' · info@adamstechniek.nl · adamstechniek.nl<br>';
$html .= 'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'onbekend');
$html .= '</td></tr>';

$html .= '</table></td></tr></table></body></html>';

// ─── Mailer opties (multipart/alternative) ───────────────────────────────────
$boundary = '----=_Part_' . md5(uniqid('', true));

$headers  = "From: " . SITE_NAAM . " <" . AFZENDER . ">\r\n";
$headers .= "Reply-To: $naam <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";
$headers .= "X-Priority: 1\r\n";

$body  = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= $txt . "\r\n";
$body .= "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= $html . "\r\n";
$body .= "--$boundary--";

// ─── Versturen ────────────────────────────────────────────────────────────────
$verzonden = mail(ONTVANGER, $onderwerp, $body, $headers);

// ─── Auto-reply naar klant ────────────────────────────────────────────────────
if ($verzonden) {
    $ar_onderwerp = 'Uw aanvraag bij ' . SITE_NAAM . ' is ontvangen';
    $ar_headers   = "From: " . SITE_NAAM . " <" . AFZENDER . ">\r\n";
    $ar_headers  .= "MIME-Version: 1.0\r\n";
    $ar_headers  .= "Content-Type: text/html; charset=UTF-8\r\n";

    $ar_html  = '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"></head>';
    $ar_html .= '<body style="font-family:Arial,sans-serif;background:#f4f6fa;padding:32px 0;margin:0">';
    $ar_html .= '<table width="600" align="center" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">';
    $ar_html .= '<tr><td style="background:linear-gradient(135deg,#0b3d91,#1a73e8);padding:28px 32px">';
    $ar_html .= '<h1 style="margin:0;color:#fff;font-size:20px">Bedankt voor uw aanvraag, ' . $naam . '!</h1>';
    $ar_html .= '</td></tr>';
    $ar_html .= '<tr><td style="padding:28px 32px;font-size:15px;color:#333;line-height:1.7">';
    $ar_html .= '<p>We hebben uw aanvraag voor <strong>' . $dienst_label . '</strong> goed ontvangen.</p>';
    $ar_html .= '<p>Ons team neemt <strong>binnen 24 uur</strong> contact met u op via telefoon of e-mail om een vrijblijvende offerte te bezorgen.</p>';
    $ar_html .= '<p>Heeft u dringende vragen? Bel ons gerust op <a href="tel:+31683396082" style="color:#1a73e8">06-83 33 96 082</a>.</p>';
    $ar_html .= '<p style="margin-top:28px">Met vriendelijke groeten,<br><strong>' . SITE_NAAM . '</strong></p>';
    $ar_html .= '</td></tr>';
    $ar_html .= '<tr><td style="background:#f4f6fa;padding:16px 32px;text-align:center;font-size:12px;color:#999">';
    $ar_html .= SITE_NAAM . ' · info@adamstechniek.nl · adamstechniek.nl';
    $ar_html .= '</td></tr></table></body></html>';

    mail($email, $ar_onderwerp, $ar_html, $ar_headers);
}

// ─── Doorsturen naar bedankt-pagina (of foutpagina) ──────────────────────────
if ($verzonden) {
    header('Location: ' . BEDANKT_URL);
} else {
    header('Location: ' . SITE_URL . '?fout=' . urlencode('Er is een technisch probleem opgetreden. Bel ons op 06-83 33 96 082.'));
}
exit;
