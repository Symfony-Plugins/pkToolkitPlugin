<?php

class pkCleanTimeoutActions extends sfActions
{
  // Do NOT use these as the default signin actions. They are special-purpose
  // ajax/iframe breakers for use in forcing the user back to the home page
  // with a new session if they try to do an ajax action after timing out.

  public function executeCleanTimeout(sfRequest $request)
  {
    // Template is a frame/ajax breaker, redirects to phase 2
  }

  public function executeCleanTimeoutPhase2(sfRequest $request)
  {
    // They timed out. Get rid of their session and force them to the home page.
    // It's blunt but effective, there's no ambiguity as to whether your action
    // succeeded 
    $this->getRequest()->isXmlHttpRequest();
    $cookies = array_keys($_COOKIE);
    foreach ($cookies as $cookie)
    {
      // Leave the sfGuardPlugin remember me cookie alone
      if ($cookie === sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember'))
      {
        continue;
      }
      // ACHTUNG: only works if we specify the domain ('/' in most cases).
      // This lives in factory.yml... where we can't access it. So unfortunately
      // a redundant setting is needed
      setcookie($cookie, "", time() - 3600, sfConfig::get('app_pkToolkit_cleanTimeout_cookie_domain', '/'));
    }
    // Push the user back to the home page rather than the login prompt. Otherwise
    // we can find ourselves in an infinite loop if the login prompt helpfully
    // sends them back to an action they are not allowed to carry out. And in some
    // situations the home page feels more natural anyway
    $url = sfContext::getInstance()->getController()->genUrl('@homepage');
    header("Location: $url");
    exit(0);
  }
}
