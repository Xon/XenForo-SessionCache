<?php

$startTime = microtime(true);
$fileDir = dirname(__FILE__) . '/html';

@set_time_limit(0);
ignore_user_abort(true);

require($fileDir . '/library/XenForo/Autoloader.php');
XenForo_Autoloader::getInstance()->setupAutoloader($fileDir . '/library');

XenForo_Application::initialize($fileDir . '/library', $fileDir);
XenForo_Application::set('page_start_time', $startTime);

$dependencies = new XenForo_Dependencies_Public();
$dependencies->preLoadData();

$db = XenForo_Application::get('db');
$class = XenForo_Application::resolveDynamicClass('XenForo_Session');
$session =  new $class();
if (!is_callable(array($session, 'getSessionCache')))
{
  echo "Please install SessionCache add-on\n";
  return;
}

$appCache = XenForo_Application::getCache();
if (empty($appCache))
{
  echo "no app cache object\n";
  return;
}

$credis = false;
$config = XenForo_Application::getConfig();
if ($config->cache->enabled && $config->cache->cacheSessions)
{
    // check for redis setup to copy from
    $registry = XenForo_Model::create('XenForo_Model_DataRegistry');
    if (method_exists($registry, 'getCredis'))
    {
        $credis = $registry->getCredis($appCache);
        echo "Found redis session cache to copy from\n";
    }
}

$cache = $session->getSessionCache(true);
if (empty($cache) || $cache == $appCache)
{
  echo "no session cache object\n";
  return;
}

if ($credis)
{
    $sessions = array();
    $pattern = Cm_Cache_Backend_Redis::PREFIX_KEY . $appCache->getOption('cache_id_prefix') . 'session_';

    // indicate to the redis instance would like to process X items at a time.
    $count = 100;
    // prevent looping forever
    $loopGuard = 10000;
    // find indexes matching the pattern
    $cursor = null;
    do
    {
        $keys = $credis->scan($cursor, $pattern ."*", $count);
        $loopGuard--;
        if ($keys === false)
        {
            break;
        }
        foreach($keys as $key)
        {
           $session = array();
           $session['session_id'] = str_replace($pattern, '', $key);
           $session['session_data'] = $credis->hget($key,"d");
           $session['expiry_date'] = XenForo_Application::$time + $credis->ttl($key);
           $sessions[] = $session;
        }
    }
    while($loopGuard > 0 && !empty($cursor));

}
else
{
    $sessions = $db->fetchAll("
    select *
    from xf_session;
    ");
}

echo "Found ".count($sessions)." sessions to migrate\n";
$sessionCount = 0;
foreach($sessions as $session)
{
    if ($session['expiry_date'] > XenForo_Application::$time)
    {
        $ret = $cache->save(
            $session['session_data'],
            'session_' . $session['session_id'],
            array(), $session['expiry_date'] - XenForo_Application::$time
        );
        if ($ret)
        {
            $sessionCount += 1;
        }
   }
}
echo "Migrated {$sessionCount} sessions\n";


