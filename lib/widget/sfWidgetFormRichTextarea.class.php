<?php

 /**
 * myWidgetFormRichTextarea represents a rich text editor.
 *
 * @author     Dominic Scheirlinck <dominic@dubdot.com>
 */
class sfWidgetFormRichTextarea extends sfWidgetFormTextarea
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('editor', 'tinymce');
    $this->addOption('tinymce_options', '');
    $this->addOption('tinymce_gzip', false);
    $this->addOption('css', false);
		$this->addOption('tool','Default');
		$this->addOption('height','150');
		$this->addOption('width','100%');
    
    parent::configure($options, $attributes);
  }
  
  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $editorClass = 'sfRichTextEditor' . $this->toCanonicalCase($this->getOption('editor'));
    if (!class_exists($editorClass)) {
      throw new sfConfigurationException(sprintf('The rich text editor "%s" does not exist.', $editorClass));
    }
    
    $editor = new $editorClass();
    if (!in_array('sfRichTextEditor', class_parents($editor))) {
      throw new sfConfigurationException(sprintf('The editor "%s" must extend sfRichTextEditor.', $editor));
    }

    $attributes = array_merge($attributes, $this->getOptions());
    $editor->initialize($name, $value, $attributes);

    // tom@punkave.com: 
    // Symfony contains an unfortunate "fix" to enable the old
    // Symfony 1.0 fillin helper to see the linked hidden field as a
    // type="text" field. This breaks any text with newlines in it in
    // Safari and Chrome. Un-fix the fix until this gets corrected 
    // in an official Symfony 1.2 release. 
    // http://trac.symfony-project.com/ticket/732

    $result = $editor->toHTML();
    $result = preg_replace('/type="text"/', 'type="hidden"',
      $result, 1);
    return $result;
  }
  
  /**
   * Converts a lower-case editor name to its canonical case
   *
   * @param string $editor
   * @return string
   */
  private function toCanonicalCase($editor)
  {
    switch ($editor) {
      case 'tinymce':
        return 'TinyMCE';
      case 'fck':
        return 'FCK';
    }
  }
}
