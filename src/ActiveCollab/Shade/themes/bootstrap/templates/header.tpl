<!DOCTYPE HTML>
<html lang="<{$project->getShortLocale()}>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><{$project->getName()}></title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <![endif]-->

  <{*<title>activeCollab Help Center</title>*}>
  <{*<meta http-equiv="content-type" content="text/html; charset=utf-8">*}>

  <{*<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>*}>
  <{*<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>*}>

  <{*<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">*}>

  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
  <{stylesheet_url page_level=$page_level}>

  <{foreach $plugins as $plugin}>
    <{$plugin->renderHead() nofilter}>
  <{/foreach}>
</head>

<body>
  <{foreach $plugins as $plugin}>
    <{$plugin->renderBody() nofilter}>
  <{/foreach}>

  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><{$project->getName()}></a>
      </div>

      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="active"><a href="/index.html">Home</a></li>
          <li><a href="./whats-new/index.html">What's New?</a></li>
          <li><a href="./release-notes/index.html">Release Notes</a></li>
          <li><a href="./books/index.html">User Manuals &amp; Guides</a></li>
          <li><a href="./videos/index.html">Instructional Videos</a></li>
        </ul>
        <form class="navbar-form navbar-right" role="search">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Search">
          </div>
        </form>
      </div>
    </div>
  </nav>

