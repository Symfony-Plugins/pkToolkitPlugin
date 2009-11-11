<?php

class pkTaskTools
{
  /**
   * Signs in as a superuser (pktaskuser) and creates a suitable context and db connection.
   *
   * @param sfConfiguration $configuration  An sfConfiguration instance
   * @param string          $connectionName The connection name (defaults to doctrine)
   *
   *
   * In addition to signing in as pktaskuser (a superadmin with all privileges), this method also
   * creates a context and a database connection to prevent "default context not found" errors elsewhere.
   *
   * The signInAsTaskUser method is intended to be called at the beginning of the execute method 
   * of your task. 
   *
   * This method sets up a context, opens a Doctrine database connection, and signs in as the 
   * pktaskuser superadmin user, ensuring that privileges are available on objects that check 
   * privileges on a user by user basis. This method takes a task configuration object and 
   * a database connection name, which defaults to doctrine. 
   *
   * Call the method like this:
   * pkTaskTools::signinAsTaskUser($this->createConfiguration($options['application'], $options['env']), $options['connection']);
   */
  
  static public function signinAsTaskUser($configuration, $connectionName = 'doctrine')
  {
    // Create the context
    sfContext::createInstance($configuration);
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($configuration);
    $connection = $databaseManager->getDatabase($connectionName)->getConnection();
    
    // Fetch the task user, create if necessary
    $user = self::getTaskUser();
    
    // Sign in as the task user
    sfContext::getInstance()->getUser()->signin($user, false);
  }
  
  static public function getTaskUser()
  {    
    $user = Doctrine::getTable('sfGuardUser')->findOneByUsername('pktaskuser');
    if (!$user)
    {
      $user->setUsername('pktaskuser');
      // Set a good unique password just in case someone cluelessly sets the active flag.
      // This further ensures that no one can ever log in with this account
      $user->setPassword(pkGuid::generate());
      // Prevents normal login
      $user->setIsActive(false);
      $user->setIsSuperAdmin(true);
      $user->save();
    }
    return $user;
  }
}
