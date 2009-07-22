<?php

class BasepkUserAdminForm extends sfGuardUserAdminForm
{
  public function configure()
  {
    parent::configure();

    $this->setWidget('groups_list', new sfWidgetFormDoctrineChoice(array(
      'model' => 'sfGuardGroup',
      'expanded' => true,
      'multiple' => true
    )));
  }
}
