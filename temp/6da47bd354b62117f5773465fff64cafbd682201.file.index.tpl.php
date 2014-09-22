<?php /* Smarty version Smarty-3.1.19-dev, created on 2014-09-22 12:09:18
         compiled from "/Users/ilija/Projects/shade/themes/default/templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:204931861054200be44a4439-57375648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6da47bd354b62117f5773465fff64cafbd682201' => 
    array (
      0 => '/Users/ilija/Projects/shade/themes/default/templates/index.tpl',
      1 => 1411386935,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204931861054200be44a4439-57375648',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19-dev',
  'unifunc' => 'content_54200be4505ff6_67940180',
  'variables' => 
  array (
    'common_questions' => 0,
    'common_question' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54200be4505ff6_67940180')) {function content_54200be4505ff6_67940180($_smarty_tpl) {?><!DOCTYPE HTML>
<html lang="en">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="assets/stylesheets/main.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>

<body>
<div id="wrapper">
  <div id="header">
    <div class="illustration">
      <a href="./index.html"><img src="assets/images/header_illustration.png" alt="Illustration" /></a>
    </div>

    <h1>Welcome to activeCollab Help Center</h1>

    <div class="search_help">
      <form action="https://www.google.com/search" method="get" target="_blank">
        <input type="hidden" value="activecollab.com/help" name="sitesearch">
        <input type="text" placeholder="Search Help for Answers" name="q">
      </form>
    </div>
  </div>

  <div id="search_results" style="display: none;">
    <div id="no_results">
      <span class="no_results_image"><img src="assets/images/circle_warning.png" alt="" /></span>
      <span class="no_results_title">Sorry, no results for:</span>
      <span class="no_results_query">Setting for the remote update of the server</span>
    </div>

    <div id="returned_results">
      <h3>Search returned <b>6</b> results:</h3>
      <ol>
        <li><a href="#">How do I add new people to my activeCollab account?</a></li>
        <li><a href="#">Is there a way i can see what are my employees working on?</a></li>
        <li><a href="#">What is fastest way to add many tasks to project?</a></li>
        <li><a href="#">Will my password expire?</a></li>
        <li><a href="#">Will i be able to replace user on a project with another one?</a></li>
      </ol>
    </div>
  </div>

  <div id="content">
    <div id="help_shortcuts">
      <ul>
        <li>
          <a href="./whats-new/index.html">
            <span class="help_shortcut_title">What's New?</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bell.png" alt="" /></span>
            <span class="help_shortcut_details">Get up to date about new features<br>and modules</span>
          </a>
        </li>
        <li>
          <a href="./books/index.html">
            <span class="help_shortcut_title">User Manuals &amp; Guides</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-manuals.png" alt="" /></span>
            <span class="help_shortcut_details">A-Z manuals which will let you know<br>activeCollab in depth</span>
          </a>
        </li>
        <li>
          <a href="./videos/index.html">
            <span class="help_shortcut_title">Instructional Videos</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-videos.png" alt="" /></span>
            <span class="help_shortcut_details">See great tutorials and cheat sheets to<br>optimize your experience with aC</span>
          </a>
        </li>
      </ul>
    </div>

    <div id="help_common_questions">
      <h3>Commonly Asked Questions</h3>

      <ul>
      <?php  $_smarty_tpl->tpl_vars['common_question'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['common_question']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['common_questions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['common_question']->key => $_smarty_tpl->tpl_vars['common_question']->value) {
$_smarty_tpl->tpl_vars['common_question']->_loop = true;
?>
        <li><a href="<?php echo \ActiveCollab\Shade::clean($_smarty_tpl->tpl_vars['common_question']->value['page_url'],$_smarty_tpl);?>
"><?php echo \ActiveCollab\Shade::clean($_smarty_tpl->tpl_vars['common_question']->value['question'],$_smarty_tpl);?>
</a></li>
      <?php } ?>
      </ul>
    </div>

    <div id="help_contact">
      <ul>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Report a Bug</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bug.png" alt="" /></span>
            <span class="help_shortcut_details">System gives you a hard time :(<br>Please let us know and we'll help ASAP.</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Suggest a Feature</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-bulb.png" alt="" /></span>
            <span class="help_shortcut_details">Have an idea for a killer feature that would save you a lot of time?</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Praise activeCollab</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-heart.png" alt="" /></span>
            <span class="help_shortcut_details">Get in touch<br>if you (HEART) activeCollab.</span>
          </a>
        </li>
        <li>
          <a href="https://www.activecollab.com/contact.html">
            <span class="help_shortcut_title">Ask a Question</span>
            <span class="help_shortcut_image"><img src="assets/images/circle-mail.png" alt="" /></span>
            <span class="help_shortcut_details">Have a question?<br>Our support team is here to help.</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <?php echo $_smarty_tpl->getSubTemplate ("footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

</div>
</body>
</html><?php }} ?>
