<?php

// A typical Doctrine route collection CRUD action class framework, with the addition of support for subforms that
// edit subsets of the object's fields via AJAX. Note that the name of your module determines the name of the
// variable TODO: a list of allowed subforms (although the existence of the class is
// a good first pass at that); ways to check credentials and privileges on subforms, exposed in such a way that
// the show action can check them when creating edit buttons.

// TODO: think about whether $singular and $list are worth the trouble of being able to
// refer to things as 'event' and 'events' rather than 'item' and 'items' in templates.
// It would be simpler to dump all the metavariables

class pkSubCrudActions extends sfActions
{
  protected $module;
  protected $singular;
  protected $list;
  protected $model;
  
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);
    $this->module = $moduleName;
    $this->singular = strtolower(substr($this->module, 0, 1)) . substr($this->module, 1);
    $this->list = $this->singular . "_list";
    if (!isset($this->model))
    {
      $this->model = ucfirst($this->singular);
    }
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $list = $this->list;
    $this->$list = $this->getRoute()->getObjects();
  }

  public function executeShow(sfWebRequest $request)
  {
    $singular = $this->singular;
    $this->$singular = $this->getRoute()->getObject();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->getForm($request);
    
    return 'Ajax';
  }
  
  public function executeUpdate(sfWebRequest $request)
  {
    $this->getForm($request);
    
    if ($this->processForm($request, $this->form))
    {
      return $this->renderPartial($this->module . '/' . $this->form->subtype);
    }

    $this->setTemplate('edit');

    return 'Ajax';
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->getRoute()->getObject()->delete();

    $this->redirect($this->module . '/index');
  }
  
  public function executeNew(sfWebRequest $request)
  {
    $className = $this->model . 'CreateForm';
    $this->form = new $className();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $className = $this->model . 'CreateForm';
    $this->form = new $className();

    if ($this->processForm($request, $this->form))
    {
      $singular = $this->singular;
      return $this->redirect($this->module . '/show?id='.$this->$singular->id);
    }

    $this->setTemplate('new');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $singular = $this->singular;
      $this->$singular = $form->save();
      // Without this, one-to-many relationships don't show the
      // effects of the changes we just made when we render the partial
      // for the static view
      $this->$singular->refreshRelated();
      return true;
    }
    
    return false;
  }
  
  protected function getForm($request)
  {
    if ($request->hasParameter('form'))
    {
      $class = pkSubCrudTools::getFormClass($this->model, $request->getParameter('form'));
      
      // Custom form getters in the subform classes allow for dependency objection in a way 
      // that permits a chunk to operate on a relation class (like EventUser)
      // rather than directly on the object itself (like Event)

      if (method_exists($class, 'getForm'))
      {
        $this->form = call_user_func(array($class, 'getForm'), $this->getRoute()->getObject(), $request);
      }
      else
      {
        $this->form = new $class($this->getRoute()->getObject());
      }
      
      if (method_exists($this->form, 'userCanEdit') && (!$this->form->userCanEdit()))
      {
        $this->forward404();
      }
      
      return;
    }
    throw new sfException('No form parameter.');
  }

  // Beginning of roster-related stuff
  
  // Reusable code for rosters of users associated with an object. This stuff is only invoked if 
  // you actually call it from corresponding actions in your subclass. See POGIL's events and groups
  // actions classes. 

  protected function validateRosterUpdateAccess($form)
  {
    // Avoid the trouble of revalidating this for every form
    $id = $form->getValue($this->singular . '_id');
    $object = Doctrine::getTable($this->model)->find($id);
    if (!$object->userCanEdit())
    {
      $this->forward404();
    }
    $this->$singular = $object;
  }
  
  protected function updateRoster($request, $args)
  {
    $type = $this->singular;
    $form = $args['relationForm'];
    $p = $request->getParameter($type . '_user');
    $form->bind($p);
    if ($form->isValid())
    {
      $this->validateRosterUpdateAccess($form);
      // OK, now we know we really have the right to do this
      $form->save();
      return $this->redirect($this->generateUrl($this->singular . '_roster', $this->$singular));
    }
    else
    {
      // TODO: we can't currently display validation errors here. We're allowing attributes,
      // but they still have to be the sort the user can't get wrong inadvertently.
      // That happens to be fine for POGIL's enums.
      //
      // (We are validating, and we don't save if the form is invalid, so there's
      // no security issue here)
      $this->forward404();
    }
  }
  
  protected function removeUser($request)
  {
    $this->object = $this->getRosterObject($request);
    $user = Doctrine::getTable('sfGuardUser')->find($request->getParameter('user_id'));
    if (!$user)
    {
      $this->forward404();
    }
    if ($user)
    {
      $this->object->removeUser($user);
    }
    return $this->redirect($this->generateUrl($this->module . '_roster', $this->object));
  }
  
  protected function searchPotentialUsers($request)
  {
    $this->object = $this->getRosterObject($request);
    $name = $request->getParameter('q');
    if (strlen($name))
    {
      $this->potentialUsers = $this->object->searchPotentialUsers($name);
    }
    else
    {
      $this->potentialUsers = array();
    }    
    return $this->renderPartial('userpicker/searchPotentialUsers', array('potentialUsers' => $this->potentialUsers));
  }
  
  protected function getRosterObject($request, $param = false)
  {
    if ($param === false)
    {
      $param = $this->singular . '_id';
    }
    $object = Doctrine::getTable($this->model)->find($request->getParameter($param));
    if (!$object)
    {
      $this->forward404();
    }
    if (!$object->userCanEdit())
    {
      $this->forward404();
    }
    return $object;
  }
}
