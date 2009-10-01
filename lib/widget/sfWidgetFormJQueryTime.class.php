<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormJQueryDate represents a date widget rendered by JQuery UI.
 *
 * This widget needs JQuery and JQuery UI to work.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormJQueryDate.class.php 12875 2008-11-10 12:22:33Z fabien $
 */
class sfWidgetFormJQueryTime extends sfWidgetFormTime
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * image:   The image path to represent the widget (false by default)
   *  * config:  A JavaScript array that configures the JQuery time widget
   *  * culture: The user culture
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('image', false);
    $this->addOption('config', '{}');
    $this->addOption('culture', '');

    parent::configure($options, $attributes);

    if ('en' == $this->getOption('culture'))
    {
      $this->setOption('culture', 'en');
    }
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $prefix = $this->generateId($name);

    $image = '';
    if (false !== $this->getOption('image'))
    {
      // TODO: clock widget handling
    }
    $hourid = $this->generateId($name.'[hour]');
    $minid = $this->generateId($name.'[minute]');
    
    $s = '<span style="display: none">' . parent::render($name, $value, $attributes, $errors) . '</span>';
    $val = htmlspecialchars($value, ENT_QUOTES);
    $s .= "<input type='text' name='pk-ignored' id='$prefix-ui' value='$val' class='" . (isset($attributes['class']) ? $attributes['class'] : '') . "'>";
    $s .= <<<EOM
<ul id='$prefix-picker' class='pk-timepicker'>
</ul>
  <script>
  $(function() {
    var chour = $('#$hourid').val();
    var cmin = $('#$minid').val();
    if (chour.length)
    {
      // Set manual time entry field to nicely formatted current time if present
      $('#$prefix-ui').val(prettyTime(chour, cmin));
    }
    // On focus, show the list
    $('#$prefix-ui').focus(function() {
      $('#$prefix-picker').show();
    });
    // Reselect current time on blur if it is one of the choices.
    // Also update the hidden select fields
    // And hide the list
    $('#$prefix-ui').blur(function() {
      var val = $(this).val();
      var components = val.match(/(\d\d?):(\d\d)\s*(am|pm)/i);
      if (components)
      {
        var hour = components[1];
        var min = components[2];
        if (min < 10)
        {
          min = '0' + Math.floor(min);
        }
        var ampm = components[3].toUpperCase();
        var formal = hour + ':' + min + ampm;
        $(this).val(formal);
        if ((ampm === 'AM') && (hour == 12))
        {
          hour = 0;
        }
        if (ampm === 'PM')
        {
          hour += 12;
        }
        $('#$hourid').val(hour);
        $('#$minid').val(min);
        $('#$prefix-picker li').removeClass('current');
        $('#$prefix-picker li').each(function() {
          if (formal === $(this).text())
          {
            // Causes race condition with click events in Firefox, 
            // breaks clicking on a time
            // scrollTo(this);
            $(this).addClass('current');
          }
        });
        // ACHTUNG: this breaks clicking on the list in Firefox.
        // If you don't have that problem solved, don't uncomment this.
        // $('#$prefix-picker').hide();
      }
      else
      {
        if (val.length)
        {
          alert("The time must be in hh:mm format, followed by AM or PM. Hint: click on the suggested times.");
          $('#$prefix-ui').focus();
        }
      }
    });
    var hour;
    var min;
    // Set up time choices
    var first = 1;
    for (hour = 0; (hour < 24); hour++)
    {
      for (min = 0; (min < 60); min += 30)
      {
        var e = $('<li>' + prettyTime(hour, min) + '</li>').appendTo($('#$prefix-picker'));
        $(e).data('hour', hour);
        $(e).data('min', min);
        // Currently selected time gets current class, if present, and a jumpscroll
        if ((hour == chour) && (min == cmin))
        {
          $(e).addClass('current');
          scrollTo(e);
        }
        // On click update the UI and the hidden select fields
        $(e).click(function() {
          $('#$prefix-ui').val($(this).text());
          $('#$hourid').val($(this).data('hour'));
          $('#$minid').val($(this).data('min'));
          $('#$prefix-picker li').removeClass('current');
          $(this).addClass('current');
        });
      }
    }
    function scrollTo(e)
    {
      var picker = $('#$prefix-picker');
      // Relative offsets are confusing, so just compare it to the offset of the first one.
      var first = $('#$prefix-picker li:first');
      var foffset = first.offset();
      var offset = $(e).offset();
      var height = picker.height();
      var scrollTop = Math.floor(offset.top - foffset.top - height / 2 - 10);
      picker.attr('scrollTop', scrollTop);
    }
    function prettyTime(hour, min)
    {
      var ampm = 'AM';
      phour = hour;
      if (hour >= 12)
      {
        ampm = 'PM';
      }
      if (hour >= 13)
      {
        phour -= 12;
      }
      if (phour == 0)
      {
        phour = 12;
      }
      pmin = min;
      if (min < 10)
      {
        pmin = '0' + Math.floor(min);
      }
      return phour + ':' + pmin + ampm;
    }
  });
  </script>
EOM
;
    return $s;
  }
}
