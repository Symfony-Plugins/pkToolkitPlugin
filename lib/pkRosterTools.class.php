<?php

// Beginning of roster-related stuff

// Reusable code for rosters of users associated with an object. This stuff is only invoked if 
// you actually call it from corresponding actions in your subclass. See POGIL's events and groups
// actions classes. 

class pkRosterTools
{
  // You can pass in a query with just an orderBy clause. The relation between the 
  // two clases will have the prefix r.
  
  static public function getCurrentRelationForms($object, $type, $formClass = false, $query = false)
  {
    $cobject = get_class($object);
    $relationClass = $cobject . 'User';
    if ($formClass === false)
    {
      $formClass = $cobject . 'UserForm';      
    }
    
    // TODO: a way to impose an order on this list. Allowing the specification of
    // extra ORDER BYs ought to be enough
    if ($query === false)
    {
      $query = Doctrine_Query::create();
    }
    $objectUsers = $query
      ->from("$relationClass r")
      ->innerJoin('r.User u')
      ->leftJoin('u.Profile p')
      ->where('r.' . $type . '_id = ?', array($object->id))
      ->execute();
    
    $objectUserForms = array();
    
    foreach ($objectUsers as $objectUser)
    {
      $objectUserForm = new $formClass($objectUser);
      $objectUserForm->getWidgetSchema()->setIdFormat($type . '_user_' . $objectUser->user_id . '_%s');
      $objectUserForm->getWidgetSchema()->setFormFormatterName('list');
      $objectUserForms[] = $objectUserForm;
    }
    
    return $objectUserForms;
  }
  
  // Implementation of the actual search for users not yet associated with the object
  // (users not on the roster). Your model class must implement a searchPotentialUsers() method,
  // primarily because your model class knows what its relationship to the users is called and
  // I don't. But that method is typically a simple wrapper around this one.
  
  // $relation is the name of the collection of related objects from the user object's
  // point of view - usually the capitalized plural of the related object's model class,
  // but not always. It's the foreignAlias in the schema. If you're dealing with attendees
  // of events, then $relation will typically by 'Events'.
  //
  // $object is the object whose already-associated users should be excluded.
  //
  // $name is the name we're searching for.
  
  // The query below is safe because even if the user does put a \ at the end of $name, all
  // they are doing is searching for a literal %. If they try to put in a ' or similar the
  // quoting provided by the Doctrine substitution mechanism will cover that.
  
  // TODO: this query is not tolerant of missing middle initials and the like. To
  // really cope with that stuff well will require more thought. We should come up with a
  // really great name-searching solution and stick to it across sites.
  
  static public function searchPotentialUsersBody($relation, $object, $name)
  {
    sfContext::getInstance()->getLogger()->info('QQ getting users');
    $allMatches = Doctrine_Query::create()
      ->from('sfGuardUser u')
      ->leftJoin('u.Profile p')
      ->leftJoin('u.' . $relation . ' e WITH e.id = ?', array($object->id))
      ->addWhere('p.first_name LIKE ? OR p.last_name LIKE ? OR p.fullname LIKE ?', array("$name%", "$name%", "$name%"))
      ->limit(sfConfig::get('app_searchusers_limit', 10))
      ->execute();
       
    $users = $object->getUsers();
    // I should be able to do that without manually filtering, but my 'e.id IS NULL' logic didn't
    // quite work and it's time to move on. (TODO: figure that problem out sometime.
    // However there's no big performance hit to doing it this way.)
    $ids = pkArray::listToHashById($users);
    $matches = array();
    
    foreach ($allMatches as $match)
    {
      if (isset($ids[$match->id]))
      {
        continue;
      }
      $matches[] = $match;
    }
    
    return $matches;
  }

  // The $action argument is '$this' in the actions class you're calling this from
  
  static public function validateRosterUpdateAccess($action, $form)
  {
    // Avoid the trouble of revalidating this for every form
    $id = $form->getValue($action->singular . '_id');
    $object = Doctrine::getTable($action->model)->find($id);
    if (!$object)
    {
      $this->forward404();
    }
    if (!$object->userCanEdit())
    {
      $this->forward404();
    }
    $singular = $action->singular;
    $action->$singular = $object;
  }

  static public function updateRoster($action, $request, $args)
  {
    $singular = $action->singular;
    $form = $args['relationForm'];
    $p = $request->getParameter($singular . '_user');
    $form->bind($p);
    if ($form->isValid())
    {
      self::validateRosterUpdateAccess($action, $form);
      // OK, now we know we really have the right to do this
      $form->save();
      $url = $action->generateUrl($action->singular . '_roster', $action->$singular);
      return $action->redirect($action->generateUrl($action->singular . '_roster', $action->$singular));
    }
    else
    {
      // TODO: we can't currently display validation errors here. We're allowing attributes,
      // but they still have to be the sort the user can't get wrong inadvertently.
      // That happens to be fine for POGIL's enums.
      //
      // (We are validating, and we don't save if the form is invalid, so there's
      // no security issue here)
      $action->forward404();
    }
  }

  static public function removeUser($action, $request)
  {
    $action->object = self::getRosterObject($action, $request);
    $user = Doctrine::getTable('sfGuardUser')->find($request->getParameter('user_id'));
    if (!$user)
    {
      $action->forward404();
    }
    if ($user)
    {
      $action->object->removeUser($user);
    }
    return $action->redirect($action->generateUrl($action->module . '_roster', $action->object));
  }

  static public function searchPotentialUsers($action, $request)
  {
    $action->object = self::getRosterObject($action, $request);
    $name = $request->getParameter('q');
    if (strlen($name))
    {
      $action->potentialUsers = $action->object->searchPotentialUsers($name);
    }
    else
    {
      $action->potentialUsers = array();
    }    
    return $action->renderPartial('userpicker/searchPotentialUsers', array('potentialUsers' => $action->potentialUsers));
  }

  // Returns the object the users in the roster are to be associated with.
  // By default, if the action class is Event, then the parameter looked for will be
  // event_id. Access is denied if the user does not have edit access to the object
  
  static public function getRosterObject($action, $request, $param = false)
  {
    if ($param === false)
    {
      $param = $action->singular . '_id';
    }
    $object = Doctrine::getTable($action->model)->find($request->getParameter($param));
    if (!$object)
    {
      $action->forward404();
    }
    if (!$object->userCanEdit())
    {
      $action->forward404();
    }
    return $object;
  }
}
