<?php

class pkToolkitDeployTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandArgument('server',
        sfCommandArgument::REQUIRED, 
        'The remote server nickname. The server nickname must be defined in properties.ini')
      // add your own options here
    ));

    $this->namespace        = 'pkToolkit';
    $this->name             = 'deploy';
    $this->briefDescription = 'Deploys a site, then performs migrations, cc, etc.';
    $this->detailedDescription = <<<EOF
The [pkToolkit:deploy|INFO] task deploys a site to a server, carrying out additional steps after
the core Symfony project:deploy task is complete to ensure success.
Call it with:

  [php symfony pkToolkit:deploy [staging|production]|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $settings = parse_ini_file("config/properties.ini", true);
    if ($settings === false)
    {
      throw new sfException("You must be in a symfony project directory");
    }
    
    foreach ($settings as $section => $data)
    {
      if ($site === $section)
      {
        $found = true;
        break;   
      }
    }

    if (!$found)
    {
      throw new sfException("First argument must be a server nickname as found in properties.ini (for instance: staging or production");
    }
    
    $esite = escapeshellarg($site);
    $eauth = escapeshellarg($data['user'] . '@' . $data['host']);
    $eport = '';
    if (isset($data['port']))
    {
      $eport .= ' -p' . ($data['port'] + 0);
    }
    system("./symfony project:deploy --go $esection");
    $epath = escapeshellarg($data['dir']);
    $cmd = "ssh $eport $eauth " . escapeshellarg("(cd $epath; ./symfony doctrine:migration; ./symfony cc; ./symfony project:permissions)");
    echo("$cmd\n");
    system($cmd, $result);
    if ($result != 0)
    {
      throw new sfException("The remote task returned an error code: $result");
    }
  }
}
