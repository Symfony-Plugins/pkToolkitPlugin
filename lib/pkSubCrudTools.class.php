<?php

class pkSubCrudTools
{
  static public function getCurrentRelationForms($object, $type, $formClass = false)
  {
    $cobject = get_class($object);
    $relationClass = $cobject . 'User';
    if ($formClass === false)
    {
      $formClass = $cobject . 'UserForm';      
    }
    
    // TODO: a way to impose an order on this list. Allowing the specification of
    // extra ORDER BYs ought to be enough
    $objectUsers = Doctrine_Query::create()
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
  
  // Fetch users, excluding those who are already attached to this object. 

  // $relation is the name of the collection of related objects from the user object's
  // point of view - usually the capitalized plural of the related object's model class,
  // but not always. It's the foreignAlias in the schema.
  //
  // $object is the object whose existing related users should be excluded.
  //
  // $name is the partial name we're searching for.
  
  // The query below is safe because even if the user does put a \ at the end of $name, all
  // they are doing is searching for a literal %. If they try to put in a ' or similar the
  // quoting provided by the Doctrine substitution mechanism will cover that.
  
  // TODO: this query leaves a lot to be desired as long as we don't have a real
  // fullname field. Also, it's not tolerant of missing middle initials and the like. To
  // really cope with that stuff well will require more thought. We should come up with a
  // really great name-searching solution and stick to it across sites.
  
  
  static public function searchPotentialUsers($relation, $object, $name)
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
  
  static public function getFormClass($model, $subtype)
  {
    return $model . ucfirst($subtype) . 'Form';
  }
}

