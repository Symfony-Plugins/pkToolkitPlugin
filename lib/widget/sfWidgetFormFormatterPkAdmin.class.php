<?php

class sfWidgetFormSchemaFormatterPkAdmin extends sfWidgetFormSchemaFormatter 
{
  protected
    $rowFormat = "<div class=\"pk-form-row\">\n  %label%\n  %field% <div class='pk-form-error'>%error%</div>\n %help%%hidden_fields%\n</div><!--/pk-form-row-->\n",
    $errorRowFormat = '%errors%',
    $helpFormat = '%help%',
    $decoratorFormat ="<div class=\"pk-admin-form-container\">\n %content%\n</div><!--/pk-form-container-->";
}
