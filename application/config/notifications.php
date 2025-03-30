<?php
$config['sb_smtp_host'] = getenv('SB_SMTP_HOST');
$config['sb_smtp_port'] = getenv('SB_SMTP_PORT');
$config['sb_smtp_crypto'] = 'ssl';
$config['sb_quote_smtp'] = getenv('SB_QUOTE_SMTP');
$config['sb_quote_user'] = getenv('SB_QUOTE_USER');
$config['sb_quote_pass'] = getenv('SB_QUOTE_PASS');
/*PO Notification */
$config['ponotification_smtp'] = getenv('PONTOITION_SMTP');
$config['ponotification_user'] = getenv('PONTOITION_USER');
$config['ponotification_pass'] = getenv('PONTOITION_PASS');
