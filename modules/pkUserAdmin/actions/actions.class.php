<?php

require_once dirname(__FILE__).'/../lib/BasepkUserAdminActions.class.php';
require_once dirname(__FILE__).'/../lib/pkUserAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pkUserAdminGeneratorHelper.class.php';

/**
 * pkUserAdmin actions.
 *
 * @package    sfShibbolethPlugin
 * @subpackage pkUserAdmin
 * @author     Fabien Potencier
 * @version    SVN: $Id: actions.class.php 12896 2008-11-10 19:02:34Z fabien $
 */
class pkUserAdminActions extends BasepkUserAdminActions
{
  protected function buildQuery()
  {
    $query = parent::buildQuery();
    // This user is for running scheduled tasks only. It must remain a superuser and
    // should never be marked active or have a known password (it has a randomly generated
    // password just in case someone somehow marks it active). So we hide it from 
    // the admin panel, where nothing good could ever happen to it.
    $query->andWhere($query->getRootAlias() . '.username <> ?', array('pktaskuser'));
    return $query;
  }
}
