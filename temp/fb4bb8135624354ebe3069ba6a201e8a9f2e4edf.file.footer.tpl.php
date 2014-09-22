<?php /* Smarty version Smarty-3.1.19-dev, created on 2014-09-22 12:11:36
         compiled from "/Users/ilija/Projects/shade/themes/default/templates/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:34366200654200e20354381-69593585%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb4bb8135624354ebe3069ba6a201e8a9f2e4edf' => 
    array (
      0 => '/Users/ilija/Projects/shade/themes/default/templates/footer.tpl',
      1 => 1411387893,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '34366200654200e20354381-69593585',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19-dev',
  'unifunc' => 'content_54200e2036e890_65677825',
  'variables' => 
  array (
    'copyright_since' => 0,
    'copyright' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54200e2036e890_65677825')) {function content_54200e2036e890_65677825($_smarty_tpl) {?><div id="footer">
  <div class="footer_space"></div>
  <div class="rights"><p>&copy; <?php if ($_smarty_tpl->tpl_vars['copyright_since']->value) {?><?php echo \ActiveCollab\Shade::clean($_smarty_tpl->tpl_vars['copyright_since']->value,$_smarty_tpl);?>
-<?php }?><?php echo \ActiveCollab\Shade::clean(date('Y'),$_smarty_tpl);?>
 &middot; <?php echo \ActiveCollab\Shade::clean($_smarty_tpl->tpl_vars['copyright']->value,$_smarty_tpl);?>
, All rights reserved. Built using <a href="https://www.activecollab.com/labs/shade" title="Shade helps you build help portal for your product. It's free, open source software" target="_blank">Shade v1.0</a>.</p></div>

  <div class="social">
    <p>Stay up to date with all new features:</p>
    <ul class="links">
      <li><a href="https://twitter.com/activecollab" target="_blank"><img title="Twitter" alt="Twitter" src="assets/images/icon_twitter.png"></a></li>
      <li><a href="https://www.facebook.com/activecollab" target="_blank"><img title="Facebook" alt="Facebook" src="assets/images/icon_facebook.png"></a></li>
      <li><a href="https://plus.google.com/+activecollab" target="_blank"><img title="Google+" alt="Google+" src="assets/images/icon_google.png"></a></li>
    </ul>
  </div>
</div><?php }} ?>
