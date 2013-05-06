<h1>Theme</h1>
<<<<<<< HEAD
<p>Currently active theme is: <strong><?=$theme_name?></strong></p>
<p>Once you visit this page, the file <code>less.php</code> in the <code>/theme/<?=$theme_name?></code> folder will compile a .css-file also placed under that same folder.</p>

<p>However, you can and should rather use a overriding stylesheet found in <code>site/themes/neat</code>. It's more readable and should be more safe to use.</p>

<p>To view a specific region(s), append <a href='<?=create_url('','someregions/primary','')?>' alt=''>'/someregions/'</a> followed by the name of the regions you want to show between each dash to the url.</p>

<p>To write messages into a region, see <code>CViewContainer::AddString()</code>.</br>
To add content into regions, see <code>CViewContainer::AddInclude()</code>.</p>

<p>Also, we're adding a grid overlay to only this page in <code>CCTheme::__construct</code> using <code>CViewContainer::AddStyle()</code> which adds CSS-code into a style-tag at the top of the page (check page header in the source to see it used on this page).</p>
=======
<p>This is a helper to aid in theme developing and testing.<p>
<p>Current theme is: <?=$theme_name?></p>
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
