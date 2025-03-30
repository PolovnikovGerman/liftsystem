<?php
$config['sb_smtp_host'] = getenv('SB_SMTP_HOST');
$config['sb_smtp_port'] = getenv('SB_SMTP_PORT');
$config['sb_smtp_crypto'] = 'ssl';
$config['sb_quote_smtp'] = getenv('SB_QUOTE_SMTP');
$config['sb_quote_user'] = getenv('SB_QUOTE_USER');
$config['sb_quote_pass'] = getenv('SB_QUOTE_PASS');
// PO Notification
$config['ponotification_smtp'] = getenv('PONTOITION_SMTP');
$config['ponotification_user'] = getenv('PONTOITION_USER');
$config['ponotification_pass'] = getenv('PONTOITION_PASS');
// Artproof Notification
$config['sb_artproof_smtp'] = getenv('SB_ARTPROOF_SMTP');
$config['sb_artproof_user'] = getenv('SB_ARTPROOF_USER');
$config['sb_artproof_pass'] = getenv('SB_ARTPROOF_PASS');
$config['sr_artproof_smtp'] = getenv('SB_ARTPROOF_SMTP');
$config['sr_artproof_user'] = getenv('SB_ARTPROOF_USER');
$config['sr_artproof_pass'] = getenv('SB_ARTPROOF_PASS');
// Unpaid Orders notification
$config['sr_unpaid_smtp'] = getenv('SR_UNPAID_SMTP');
$config['sr_unpaid_user'] = getenv('SR_UNPAID_USER');
$config['sr_unpaid_pass'] = getenv('SR_UNPAID_PASS');
$config['sb_unpaid_smtp'] = getenv('SB_UNPAID_SMTP');
$config['sb_unpaid_user'] = getenv('SB_UNPAID_USER');
$config['sb_unpaid_pass'] = getenv('SB_UNPAID_PASS');