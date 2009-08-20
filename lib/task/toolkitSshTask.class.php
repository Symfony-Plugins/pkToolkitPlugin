<?php

class toolkitSsh extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addArgument('server', sfCommandArgument::REQUIRED, 'A server name as listed in properties.ini (examples: staging, production)');

    $this->namespace        = 'pkToolkit';
    $this->name             = 'ssh';
    $this->briefDescription = 'Opens an interactive ssh connection to the specified server using the username, port and hostname in properties.ini';
    $this->detailedDescription = <<<EOF
The [pkToolkit:ssh|INFO] task opens an interactive ssh connection to the specified server, using the
credentials specified in properties.ini.

Call it with:

  [php symfony pkToolkitPlugin:ssh servername (examples: staging, production)]
  
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $server = $arguments['server'];
    $data = parse_ini_file($this->configuration->getRootDir() . '/config/properties.ini', true);
    if (!isset($data[$server]))
    {
      throw new sfException("$server does not exist in config/properties.ini. Examples: staging, production\n");
    }
    $data = $data[$server];
    $cmd = "ssh ";
    if (isset($data['port']))
    {
      $cmd .= "-p " . escapeshellarg($data['port']);
    }
    if (isset($data['user']))
    {
      $cmd .= " -l " . escapeshellarg($data['user']);
    }
    $cmd .= " " . escapeshellarg($data['host']);
    passthru($cmd);
  }
}
