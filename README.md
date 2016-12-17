# XenForo-SessionCache

Allows the setup of a dedicated session cache distinct from the normal XF cache

Defines a "sessionCache" section in addition to of a "cache" section. Takes all the same options, except $config['cache']['cacheSessions']


Note; 
- if no 'sessionCache' section is defined,  MySQL session storage will be used unless $config['cache']['cacheSessions'] is true.
- if sessionCache' section is defined, but is disabled; MySQL session storage will be used.
