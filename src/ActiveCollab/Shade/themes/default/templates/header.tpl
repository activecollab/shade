<!DOCTYPE HTML>
<html lang="<{$project->getShortLocale()}>">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

  <{stylesheet_url page_level=$page_level}>

  <{foreach $plugins as $plugin}>
    <{$plugin->renderHead() nofilter}>
  <{/foreach}>
</head>

<body>
  <{foreach $plugins as $plugin}>
    <{$plugin->renderBody() nofilter}>
  <{/foreach}>