[?php slot('body_class') ?]pk-admin [?php echo $sf_params->get('module'); ?] [?php echo $sf_params->get('action');?] [?php end_slot() ?]

<?php if (isset($this->params['css'])): ?> 
[?php use_stylesheet('<?php echo $this->params['css'] ?>', 'first') ?] 
<?php else: ?> 
[?php slot('body_class') ?]pk-admin [?php echo $sf_params->get('action'); ?] [?php end_slot() ?]

[?php use_stylesheet('/pkToolkitPlugin/css/pkToolkit.css', 'first') ?]
[?php use_stylesheet('/pkContextCMSPlugin/css/pkContextCMS.css', 'first') ?]
[?php use_stylesheet('/pkToolkitPlugin/css/pkAdmin.css', 'first') #Admin Styles ?]

[?php use_javascript('/pkToolkitPlugin/js/pkControls.js') ?]
[?php use_javascript('/pkToolkitPlugin/js/pkUI.js') ?]

[?php use_stylesheet('/pkAdminPlugin/js/theme/ui.all.css', 'first') # JQ Date Picker Styles (This doesn't have to be the ui.all.css, we could make a custom css later ) ?]
[?php use_javascript('/pkAdminPlugin/js/jquery-ui-personalized-1.6rc6.min.js', 'last') # JQ Date Picker JS (This can/should be consolidated with sfJqueryReloadedPlugin/js/jquery-ui-sortable...) ?]
<?php endif; ?>