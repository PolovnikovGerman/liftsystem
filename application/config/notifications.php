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
$config['sr_artproof_smtp'] = getenv('SR_ARTPROOF_SMTP');
$config['sr_artproof_user'] = getenv('SR_ARTPROOF_USER');
$config['sr_artproof_pass'] = getenv('SR_ARTPROOF_PASS');
// Unpaid Orders notification
$config['sr_unpaid_smtp'] = getenv('SR_UNPAID_SMTP');
$config['sr_unpaid_user'] = getenv('SR_UNPAID_USER');
$config['sr_unpaid_pass'] = getenv('SR_UNPAID_PASS');
$config['sb_unpaid_smtp'] = getenv('SB_UNPAID_SMTP');
$config['sb_unpaid_user'] = getenv('SB_UNPAID_USER');
$config['sb_unpaid_pass'] = getenv('SB_UNPAID_PASS');
// Attempts report
$config['sb_attemptrep_smtp'] = getenv('SB_ATTEMPTREP_SMTP');
$config['sb_attemptrep_user'] = getenv('SB_ATTEMPTREP_USER');
$config['sb_attemptrep_pass'] = getenv('SB_ATTEMPTREP_PASS');
// Bonus Report
$config['sb_bonusreport_smtp'] = getenv('SB_BONUSREPORT_SMTP');
$config['sb_bonusreport_user'] = getenv('SB_BONUSREPORT_USER');
$config['sb_bonusreport_pass'] = getenv('SB_BONUSREPORT_PASS');
$config['sr_bonusreport_smtp'] = getenv('SR_BONUSREPORT_SMTP');
$config['sr_bonusreport_user'] = getenv('SR_BONUSREPORT_USER');
$config['sr_bonusreport_pass'] = getenv('SR_BONUSREPORT_PASS');
// Week Orders, Quotes, Leads
$config['quoteweek_smtp'] = getenv('QUOTEWEEK_SMTP');
$config['quoteweek_user'] = getenv('QUOTEWEEK_USER');
$config['quoteweek_pass'] = getenv('QUOTEWEEK_PASS');
// Order Items Price
$config['itemprice_smtp'] = getenv('ITEMPRICE_SMTP');
$config['itemprice_user'] = getenv('ITEMPRICE_USER');
$config['itemprice_pass'] = getenv('ITEMPRICE_PASS');
// Weekly search report
$config['searchreport_smtp'] = getenv('SEARCHREP_SMTP');
$config['searchreport_user'] = getenv('SEARCHREP_USER');
$config['searchreport_pass'] = getenv('SEARCHREP_PASS');
// User add code
$config['usercode_smtp'] = getenv('USERCODE_SMTP');
$config['usercode_user'] = getenv('USERCODE_USER');
$config['usercode_pass'] = getenv('USERCODE_PASS');