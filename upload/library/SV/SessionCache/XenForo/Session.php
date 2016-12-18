<?php

class SV_SessionCache_XenForo_Session extends XFCP_SV_SessionCache_XenForo_Session
{
    protected $sessionCache = null;

    public function getSessionCache($ignoreEnabled = false)
    {
        if ($this->sessionCache !== null)
        {
            return $this->sessionCache;
        }
        $config = XenForo_Application::getConfig();
        if ($config && $config->sessionCache)
        {
            // setup some defaults
            $defaultConfig = new Zend_Config(array(
                'sessionCache' => array(
                    'enabled' => false,
                    'frontend' => 'core',
                    'frontendOptions' => array(
                        'caching' => true,
                        'cache_id_prefix' => 'xf_'
                    ),
                    'backend' => 'file',
                    'backendOptions' => array(
                        'file_name_prefix' => 'xf_'
                    )
                )));            
			$outputConfig = new Zend_Config(array(), true);
			$outputConfig->merge($defaultConfig)
			             ->merge($config);
            if ($ignoreEnabled && !$outputConfig->sessionCache->enabled)
            {
                $outputConfig->sessionCache->enabled = true;
            }
            $this->sessionCache = XenForo_Application::getInstance()->loadCache($outputConfig->sessionCache);
        }

        if (!$this->sessionCache)
        {
            $this->sessionCache = $this->_cache ? $this->_cache : false;
        }
        return $this->sessionCache;
    }

    public function getSessionFromSource($sessionId)
    {
        $oldCache = $this->_cache;
        $this->_cache = $this->getSessionCache();
        try
        {
            return parent::getSessionFromSource($sessionId);
        }
        finally
        {
            $this->_cache = $oldCache;
        }
    }

    public function saveSessionToSource($sessionId, $isUpdate)
    {
        $oldCache = $this->_cache;
        $this->_cache = $this->getSessionCache();
        try
        {
            parent::saveSessionToSource($sessionId, $isUpdate);
        }
        finally
        {
            $this->_cache = $oldCache;
        }
    }

    public function deleteSessionFromSource($sessionId)
    {
        $oldCache = $this->_cache;
        $this->_cache = $this->getSessionCache();
        try
        {
            parent::deleteSessionFromSource($sessionId);
        }
        finally
        {
            $this->_cache = $oldCache;
        }
    }
}