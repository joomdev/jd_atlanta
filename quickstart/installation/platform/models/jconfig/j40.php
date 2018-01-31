<?php
class J40Config
{
	/* Site Settings */
	public $offline = '0';
	public $offline_message = 'This site is down for maintenance.<br> Please check back again soon.';
	public $display_offline_message = '1';
	public $offline_image = '';
	public $sitename = 'Joomla!';            // Name of Joomla site
	public $editor = 'tinymce';
	public $captcha = '0';
	public $list_limit = '20';
	public $access = '1';
	public $frontediting = '1';

	/* Database Settings */
	public $dbtype = 'mysqli';               // Normally mysqli
	public $host = 'localhost';              // This is normally set to localhost
	public $user = '';                       // DB username
	public $password = '';                   // DB password
	public $db = '';                         // DB database name
	public $dbprefix = 'jos_';               // Do not change unless you need to!

	/* Server Settings */
	public $secret = 'FBVtggIk5lAzEU9H';     // Change this to something more secure
	public $gzip = '0';
	public $error_reporting = 'default';
	public $helpurl = 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '0';
	public $tmp_path = '/tmp';                // Please check with your host that this is the correct path to the temp directory. This path needs to be writable by Joomla!
	public $log_path = '/var/logs';           // Please check with your host that this is the correct path to the logs directory. This path needs to be writable by Joomla!
	public $live_site = '';                   // Optional, full URL to Joomla install.
	public $force_ssl = 0;                    // Force areas of the site to be SSL ONLY.  0 = None, 1 = Administrator, 2 = Both Site and Administrator

	/* Locale Settings */
	public $offset = 'UTC';

	/* Session settings */
	public $lifetime = '15';                  // Session time
	public $session_handler = 'database';
	public $shared_session = '0';
	public $session_memcache_server_host = 'localhost';
	public $session_memcache_server_port = '11211';
	public $session_memcached_server_host = 'localhost';
	public $session_memcached_server_port = '11211';


	/* Mail Settings */
	public $mailonline = '1';
	public $mailer      = 'mail';
	public $mailfrom    = '';
	public $fromname    = '';
	public $massmailoff = '0';
	public $replyto     = '';
	public $replytoname = '';
	public $sendmail    = '/usr/sbin/sendmail';
	public $smtpauth    = '0';
	public $smtpuser    = '';
	public $smtppass    = '';
	public $smtphost    = 'localhost';

	/* Cache Settings */
	public $caching = '0';
	public $cachetime = '15';
	public $cache_handler = 'file';
	public $cache_platformprefix = '0';
	public $memcache_persist = '1';
	public $memcache_compress = '0';
	public $memcache_server_host = 'localhost';
	public $memcache_server_port = '11211';
	public $memcached_persist = '1';
	public $memcached_compress = '0';
	public $memcached_server_host = 'localhost';
	public $memcached_server_port = '11211';
	public $redis_persist = '1';
	public $redis_server_host = 'localhost';
	public $redis_server_port = '6379';
	public $redis_server_auth = '';
	public $redis_server_db = '0';

	/* Proxy Settings */
	public $proxy_enable = '0';
	public $proxy_host = '';
	public $proxy_port = '';
	public $proxy_user = '';
	public $proxy_pass = '';

	/* Debug Settings */
	public $debug = '0';
	public $debug_lang = '0';

	/* Meta Settings */
	public $MetaDesc = 'Joomla! - the dynamic portal engine and content management system';
	public $MetaKeys = 'joomla, Joomla';
	public $MetaTitle = '1';
	public $MetaAuthor = '1';
	public $MetaVersion = '0';
	public $MetaRights = '';
	public $robots = '';
	public $sitename_pagetitles = '0';

	/* SEO Settings */
	public $sef = '1';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $unicodeslugs = '0';

	/* Feed Settings */
	public $feed_limit = 10;
	public $feed_email = 'none';

	/* Cookie Settings */
	public $cookie_domain = '';
	public $cookie_path = '';

	/* Miscellaneous Settings */
	public $asset_id = '1';
}
