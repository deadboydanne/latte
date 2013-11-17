<h1>Welcome to Latte!</h1>

<?php if(file_exists('site/data/dbconfig.php')): ?>

<p>This is a reference installation of the MVC-framework Latte. The framework is created by Andreas Carlsson as an assignment for a PHP/MVC-class in Blekinge Tekniska Högskola. It's based on the framework <a href="https://github.com/mosbth/lydia.git">Lydia</a> but with quite a few modifications.</p>

<h2>Download</h2>
<p>You can download Latte from github.</p>
<blockquote>
<code>git clone git://github.com/andreasc89/latte.git</code>
</blockquote>
<p>You can review its source directly on github: <a href='https://github.com/andreasc89/latte'>https://github.com/andreasc89/latte</a></p>

<h2>Installation</h2>
<p>The installation process can be found in the <a href="<?=create_url('setup');?>">setup</a>-controller</p>

<? else: ?>


<h2>Looks like it's your first time here.</h2>
<p>Please go to the <a href="<?=create_url('setup');?>">setup</a>-controller to check that your server is capable of running the framework and connect the framework to the your MySQL-database.</p>

<h2>Download</h2>
<p>You can download Latte from github.</p>
<blockquote>
<code>git clone git://github.com/andreasc89/latte.git</code>
</blockquote>
<p>You can review its source directly on github: <a href='https://github.com/andreasc89/latte'>https://github.com/andreasc89/latte</a></p>



<? endif; ?>


