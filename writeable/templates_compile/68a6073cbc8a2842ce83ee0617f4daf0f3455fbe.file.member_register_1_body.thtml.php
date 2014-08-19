<?php /* Smarty version Smarty-3.0.6, created on 2013-07-16 16:38:40
         compiled from "/home/banujos/public_html/betz/writeable/email_templates/member_register_1_body.thtml" */ ?>
<?php /*%%SmartyHeaderCode:40925381051e5bd6015a898-00143276%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '68a6073cbc8a2842ce83ee0617f4daf0f3455fbe' => 
    array (
      0 => '/home/banujos/public_html/betz/writeable/email_templates/member_register_1_body.thtml',
      1 => 1372604393,
      2 => 'file',
    ),
    '9466c1f706c1681ca24a0a6fcd6474de0663022b' => 
    array (
      0 => '/home/banujos/public_html/betz/writeable/email_templates/email_layout.thtml',
      1 => 1372604393,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '40925381051e5bd6015a898-00143276',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_url')) include '/home/banujos/public_html/betz/themes/_plugins/function.url.php';
if (!is_callable('smarty_function_setting')) include '/home/banujos/public_html/betz/themes/_plugins/function.setting.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style>
h1 { background-color: #e6f2f8; color: #0b5679; font-size: 18pt; font-weight: normal; font-family: helvetica, arial, sans-serif; margin: 0 0 10px 0; padding: 7px 10px }
div.body { padding: 10px; font-size: 10pt; font-family: helvetica, arial, sans-serif; color: #111; }
</style>
</head>
<body>
<div class="body">
	
<p>Hi <?php echo $_smarty_tpl->getVariable('member')->value['first_name'];?>
,</p>

<p>Thank you for registering an account at <?php echo $_smarty_tpl->getVariable('site_name')->value;?>
.  Your account details are below:</p>

<p><b>Username</b>: <?php echo $_smarty_tpl->getVariable('member')->value['username'];?>
</p>
<p><b>Password</b>: <?php echo $_smarty_tpl->getVariable('password')->value;?>
</p>

<a href="<?php echo smarty_function_url(array(),$_smarty_tpl);?>
">Click here to login now</a>.


	
	<?php echo smarty_function_setting(array('name'=>"email_signature"),$_smarty_tpl);?>

</div>
</body>
</html>