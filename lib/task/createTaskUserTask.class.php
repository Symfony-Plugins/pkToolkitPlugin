<?php

/**
 * Create a superuser account named taskuser and mark it inactive so that
 * no one can log into it directly. This task is used as the default user for
 * maintenance tasks that need to access model classes that check user privileges.
 * 
 */
class pkCreateTaskUserTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace = 'pkToolkit';
    $this->name = 'create-task-user';
    $this->briefDescription = 'Creates a locked superadmin account for use by maintenance tasks';

    $this->detailedDescription = <<<EOF
The [pkToolkit:create-task-user|INFO] task creates a locked superadmin account for use by maintenance
tasks such as cron jobs and the like. This account is never logged into in the normal way, and
should never be marked active. The pkUserAdmin module hides it to prevent confusion. If you are not 
using pkUserAdmin you might want to hide it in your own user management admin modules.

This task will refresh the settings for the account if it already exists.

The account is named pktaskuser to make conflicts with other accounts unlikely.

  [./symfony pkToolkit:create-task-user|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $user = new sfGuardUser();
    $user->setUsername('pktaskuser');
    // Set a good unique password just in case someone cluelessly clears the active flag
    $user->setPassword(pkGuid::generate());
    // Prevents normal login
    $user->setIsActive(false);
    $user->setIsSuperAdmin(true);
    $user->replace();
  }
}
