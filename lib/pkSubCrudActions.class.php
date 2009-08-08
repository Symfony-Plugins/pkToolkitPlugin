<?php

// A typical Doctrine route collection CRUD action class framework, with the addition of support for subforms that
// edit subsets of the object's fields via AJAX. Note that the name of your module determines the name of the
// variable TODO: a list of allowed subforms (although the existence of the class is
// a good first pass at that); ways to check credentials and privileges on subforms, exposed in such a way that
// the show action can check them when creating edit buttons.

class pkSubCrudActions extends sfActions
{
  protected $module;
  protected $singular;
  protected $list;
  
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);
    $this->module = $moduleName;
    $this->singular = strtolower(substr($this->module, 0, 1)) . substr($this->module, 1);
    $this->list = $this->module . "_list";
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
      return $this->renderPartial($this->module . '/' . $this->form->type);
    }

    $this->setTemplate('edit');

    return 'Ajax';
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->getRoute()->getObject()->delete();

    $this->redirect($this->module . '/' . 'index');
  }
  
  public function executeNew(sfWebRequest $request)
  {
    $className = ucfirst($this->singular) . 'CreateForm';
    $this->form = new $className();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $className = ucfirst($this->singular) . 'CreateForm';
    $this->form = new $className();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $singular = $this->singular;
      $this->$singular = $form->save();
      
      return true;
    }
    
    return false;
  }
  
  protected function getForm($request)
  {
    if ($request->hasParameter('form'))
    {
      $class = ucfirst($this->singular) . ucfirst($request->getParameter('form')) . 'Form';
      $this->form = new $class($this->getRoute()->getObject());
    }
  }
}
