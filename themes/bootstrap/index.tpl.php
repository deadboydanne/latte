

<!doctype html>
<html lang="en">
  <head>
  <meta charset='utf-8'/>
  <title>Latte - <?=$title?></title>
	<link rel='shortcut icon' href='<?=theme_url($favicon)?>'/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Beskrivning av sidan">
    <meta name="author" content="">
    <link rel='stylesheet' href='<?=theme_url($stylesheet)?>'/>
    <?php if(isset($inline_style)): ?><style><?=$inline_style?></style><?php endif; ?>

    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
	  .footer {
		  background: #f4f4f4;
	  }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

  </head>



<body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href='<?=base_url()?>'><img id='site-logo' src='<?=theme_url($logo)?>' alt='logo' width='30' height='30' style='margin-top: -10px;' /><?=$header?></a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              <?=login_menu()?>
            </p>
            <ul class="nav">
            	
	  <?php if(region_has_content('navbar')): ?>
      <?=render_views('navbar')?>
      <?php endif; ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
    <?php if(region_has_content('sidebar')): ?>
      <div class="row-fluid">
        <div class="span3">
        
          <div class="well sidebar-nav">
          <?=render_views('sidebar')?>
          </div><!--/.well -->
         
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <div id='primary'><?=get_messages_from_session()?><?=@$main?><?=render_views('primary')?><?=render_views()?></div>
          </div>
        </div><!--/span-->
      </div><!--/row-->
<?php else: ?>
     
     <div class="row-fluid">
        <div class="span12">
          <div class="hero-unit">
            <div id='primary'><?=get_messages_from_session()?><?=@$main?><?=render_views('primary')?><?=render_views()?></div>
          </div>
        </div><!--/span-->
      </div><!--/row-->
      
<?php endif; ?>
      
      
          <div class="row-fluid">
  <?php if(region_has_content('footer-column-one', 'footer-column-two', 'footer-column-three', 'footer-column-four')): ?>
            <div id="footer-column-one" class="span3"><?=render_views('footer-column-one')?></div><!--/span-->
            <div id="footer-column-two" class="span3"><?=render_views('footer-column-two')?></div><!--/span-->
            <div id="footer-column-three" class="span3"><?=render_views('footer-column-three')?></div><!--/span-->
            <div id="footer-column-four" class="span3"><?=render_views('footer-column-four')?></div><!--/span-->
<?php endif; ?>
          </div><!--/row-->
          <hr>
	  <footer>
        <div id="footer"><?=render_views('footer')?><?=$footer?><?=get_debug()?></div>
	  </footer>

    </div>
<!-- Inactive all javascript
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
-->

  </body>








</html>