<?php
App::import('Vendor', 'Iggy.datetime_format');
App::import('Vendor', 'Iggy.get_status_name');
App::import('Vendor', 'Iggy.timeout');

// ログインフォーム表示
Configure::write('Mds.login', true);
// Mds.loginがfalseでも、このIPが一致したクライアントはログインフォームを表示
Configure::write('Mds.ClientIP', '127.0.0.1');

Configure::write('Mds.startYearMonthTime', strtotime('2012/02/01 00:00:00'));

Configure::write('Mds.mail_confirm_lock_time', 10 * 60); // 10分間

Configure::write('Mds.Information.show_count.new', 5);
Configure::write('Mds.Information.show_count.regular', 5);

Configure::write('Mds.reportCommentLength', 15);

Configure::write('Mds.sendmail.config', null);
Configure::write('Mds.sendmail.from', 'liangkj@cit.co.jp');
Configure::write('Mds.sendmail.bccOnErr', 'liangkj@cit.co.jp');

define('TIMEOUT_HOUR', 8);
define('MAX_MAIL_COUNT_30MINUTES', 50);

Configure::write('Mds.loginRedirect.guest', '/information/index/');
Configure::write('Mds.loginRedirect.mps', '/information/index/');
Configure::write('Mds.loginRedirect.sa', '/information/index/');
Configure::write('Mds.loginRedirect.admin', '/adm_documents/index/0/');

