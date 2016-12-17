<?php

class SV_SessionCache_XenForo_Session extends XFCP_SV_SessionCache_XenForo_Session
{
    protected $sessionCache = null;

    protected function getSessionCache()
    {
        if ($this->sessionCache !== null)
        {
            return $this->sessionCache;
        }
        $config = XenForo_Application::getConfig();
        if ($config && $config->sessionCache)
        {
            $this->sessionCache = XenForo_Application::getInstance()->loadCache($config->sessionCache);
        }
        else if ($this->_cache)
        {
            $this->sessionCache = $this->_cache;
        }

        if ($this->sessionCache === null)
        {
            $this->sessionCache = false;
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