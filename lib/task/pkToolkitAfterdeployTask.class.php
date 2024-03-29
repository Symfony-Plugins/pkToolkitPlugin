<?php

class pkToolkitAfterdeployTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('env', 
        sfCommandArgument::REQUIRED, 
        'The remote environment ("staging")')
    ));

  $this->addOptions(array(
    new sfCommandOption('skip-migrate', 
      sfCommandOption::PARAMETER_NONE)
  ));
    $this->namespace        = 'pkToolkit';
    $this->name             = 'after-deploy';
    $this->briefDescription = 'Remote end of pkToolkit:deploy';
    $this->detailedDescription = <<<EOF
The [pkToolkit:after-deploy|INFO] task carries out appropriate tasks on the server end
after the project:deploy task has been run on the dev end. It is invoked remotely by
pkToolkit:deploy.

It currently invokes:

./symfony cc
./symfony doctrine:migrate --env=envname

You can skip the migrate step with --skip-migrate.

You won't normally call it yourself, but you could call it with:

  [php symfony pkToolkit:after-deploy (staging|prod)|INFO]
  
Note that you must specify the environment.
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->attemptTask('cc');
    if (!$options['skip-migrate'])
    {
      $this->attemptTask('doctrine:migrate', array(), array('env' => $arguments['env']));
    }
  }
  
  protected function attemptTask($task, $args = array(), $options = array())
  {
    array_unshift($args, $task);
    foreach ($options as $key => $value)
    {
      $args[] = "--$key=$value";
    }
    $args = implode(' ', array_map('escapeshellarg', $args));
    echo("Launching remote task $args\n");
    system("./symfony $args", $result);
    if ($result != 0)
    {
      throw new sfException("Remote task $task produced error code $result");
    }
  }
}
