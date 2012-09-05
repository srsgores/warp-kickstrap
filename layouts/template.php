<?php
/*------------------------------------------------------------------------------------------------------------------------
    Author: Sean Goresht
    www: http://seangoresht.com/
    github: https://github.com/srsgores

    twitter: http://twitter.com/S.Goresht

     warp-kickstrap Joomla Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  template.php
	=============================================================================
	 This file is responsible for OUTPUTTING the layout to the client (person viewing the page).  It is supposed to be as "logicless" as possible, and should represent the very foundation of the resulting DOM.

	 This file does the following things:
	 	--Resolve dependencies for the template's CSS, Javascript, and configurations
	 	--Set the html structure according to the language file
	 	--Output modules depending on whether their positions are enabled, according to Joomla.  This is always achieved through an if/else statement.

	 	Note: the logic behind how modules are displayed is part of the WARP engine, and should not be confused with the template (ex. how "stacked" templates appear).  To customize this, please see the %d - $d - %d files in the warp folder.

	 Template CSS class explanation:
		1140 grid (choice 1)
			--Rows (.row): each individual row
			--Columns .[number]col, .[number]col last.  These are columns.  The grid itself is 12 blocks, so the selected columns should add up to 12.  The last column element should have the class "last" so as to remove any margins.  Note: we can decide not to use the last class if we enable selectivizr in IE, so that we can use the :last-child pseudo-selector.  Even with Modernizr loaded, we can't do the :last-child pseudo-selector in IE 8 -.
			--Container (.container): is the outer container for the entire page, adding padding-left and padding-right
		Twitter Bootstrap (choice 2) -- FIXED-WIDTH
			--Rows (.row): each individual row.  Same as 1140
			--Columns (.span[number]): should add up to 12, like 1140 columns.  Note: these are FIXED-WIDTH
		YooTheme (choice 3) -- FIXED-WIDTH
			--Rows (.clearfix): sets a clearfix so the next float will appear on a new row (NOT RECOMMENDED).
			--Columns (.width[number in percentage]) 

		Sitewide CSS classes (just for this example version)
			--.grid-box: this was YooTheme's idea.  In any case, we need some class to identify each separate entity on a row.  We could use the selected framework's column class, and use an attribute selector to get anything starting with a series of letters, but this is undesirable as it isn't well-supported in older browsers.  For this reason, we use the grid-box class.
			--.module: this is the class prepended to each module.  This is the class used to style all modules.
			--.page: this is the class applied to the body in almost all cases.  This is a YooTheme thing, and I'm looking at taking it out.
			--#content: this is the div containing all but the header, footer, and sidebars.  This is used to facilitate AJAX calls
--------------------------------------------------------------------------------------------------------------------- */


// get template configuration
include($this['path']->path('layouts:template.config.php'));

?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>
<?php echo $this['template']->render('head'); ?>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

	<?php if ($this['modules']->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this['modules']->render('absolute'); ?>
	</div>
	<?php endif; ?>
	<?php
	if($this['config']->get('loader') == "1")
	{
		$loader_class = "loading visuallyhidden";
		echo "<div id = \"ajaxloader1\"></div>";
	}
	?>
	<div class="container <?php if (isset($loader_class)){echo $loader_class;}?>">

		<header id="header" class = "row">

			<?php if ($this['modules']->count('toolbar-l + toolbar-r') || $this['config']->get('date')) : ?>
			<div id="toolbar" class = "row">

				<?php if ($this['modules']->count('toolbar-l') || $this['config']->get('date')) : ?>
				<div class="sixcol">

					<?php if ($this['config']->get('date')) : ?>
					<time datetime="<?php echo $this['config']->get('datetime'); ?>"><?php echo $this['config']->get('actual_date'); ?></time>
					<?php endif; ?>
					<div class = "sixcol last">
					<?php echo $this['modules']->render('toolbar-l'); ?>
					</div>

				</div>
				<?php endif; ?>
				<?php if ($this['modules']->count('toolbar-r')) : ?>
				<div class="sixcol last"><?php echo $this['modules']->render('toolbar-r'); ?></div>
				<?php endif; ?>

			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('logo + headerbar')) : ?>
			<div id="headerbar" class = "row">

				<?php if ($this['modules']->count('logo')) : ?>
				<a id="logo" class = "eightcol" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo
				$this['modules']->render('logo'); ?></a>
				<?php endif; ?>

				<?php if($this['modules']->count('headerbar')) : ?>
				<?php echo $this['modules']->render('headerbar'); ?>
				<?php endif; ?>

			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('menu + search')) : ?>
			<div id="menubar" class = "row">

				<?php if ($this['modules']->count('menu')) : ?>
				<nav id="menu" class = "eightcol"><?php echo $this['modules']->render('menu'); ?></nav>
				<?php endif; ?>

				<?php if ($this['modules']->count('search')) : ?>
				<div id="search" class = "fourcol last"><?php echo $this['modules']->render('search'); ?></div>
				<?php endif; ?>

			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('banner')) : ?>
			<div id="banner"><?php echo $this['modules']->render('banner'); ?></div>
			<?php endif; ?>

		</header>
		<div id = "content">
		<?php if ($this['modules']->count('top-a')) : ?>
		<section id="top-a" class="row grid-block"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></section>
		<?php endif; ?>

		<?php if ($this['modules']->count('top-b')) : ?>
		<section id="top-b" class="row grid-block"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></section>
		<?php endif; ?>

		<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
		<div id="main" class="row grid-block">

			<div id="maininner" class="grid-box">

				<?php if ($this['modules']->count('innertop')) : ?>
				<section id="innertop" class="row grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('breadcrumbs')) : ?>
				<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
				<?php endif; ?>

				<?php if ($this['config']->get('system_output')) : ?>
				<section class="row grid-block"><?php echo $this['template']->render('content'); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('innerbottom')) : ?>
				<section id="innerbottom" class="row grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
				<?php endif; ?>

			</div>
			<!-- maininner end -->

			<?php if ($this['modules']->count('sidebar-a')) : ?>
			<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>

			<?php if ($this['modules']->count('sidebar-b')) : ?>
			<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>

		</div>
		<?php endif; ?>
		<!-- main end -->

		<?php if ($this['modules']->count('bottom-a')) : ?>
		<section id="bottom-a" class="row grid-block"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></section>
		<?php endif; ?>

		<?php if ($this['modules']->count('bottom-b')) : ?>
		<section id="bottom-b" class="row grid-block"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></section>
		<?php endif; ?>

		<?php if ($this['modules']->count('footer + debug') || $this['config']->get('warp_branding') || $this['config']->get('totop_scroller')) : ?>
			</div>
		<footer id="footer" class = "row">

			<?php if ($this['config']->get('totop_scroller')) : ?>
			<a id="totop-scroller" href="#page"></a>
			<?php endif; ?>

			<?php
				echo $this['modules']->render('footer');
				$this->output('warp_branding');
				echo $this['modules']->render('debug');
			?>

		</footer>
		<?php endif; ?>

	</div>

	<?php echo $this->render('footer'); ?>

</body>
</html>