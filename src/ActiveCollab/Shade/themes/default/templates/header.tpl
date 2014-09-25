<!DOCTYPE HTML>
<html lang="<{$project->getShortLocale()}>">
<head>
  <title>activeCollab Help Center</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="../../assets/stylesheets/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

  <{foreach $plugins as $plugin}>
    <{$plugin->renderHead() nofilter}>
  <{/foreach}>
</head>