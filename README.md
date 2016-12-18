# XenForo-SessionCache

Allows the setup of a dedicated session cache distinct from the normal XF cache

Defines a "sessionCache" section in addition to of a "cache" section. Takes all the same options, except $config['cache']['cacheSessions']


Note; 
- if no 'sessionCache' section is defined or it is disabled, falls back on existing cache/mysql storage

Provided scripts:

- For copying MySQL sessions to a seperate cache: copy_mysql_sessions_to_cache.php
 - Edit the line:
  ```
  $fileDir = dirname(__FILE__) . '/html';
  ```
  To point to the webroot. 
 - Install add-on, configure sessionCache but disable.
 - Run migration script.
 - Configure sessionCache to be enabled
 - Run migration script again (it will only copy older sessions).