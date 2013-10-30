<h1>Index Controller</h1>
<p>Welcome to Latte index controller.</p>

<h2>Download</h2>
<p>You can download Latte from github.</p>
<blockquote>
<code>git clone git://github.com/andreasc89/latte.git</code>
</blockquote>
<p>You can review its source directly on github: <a href='https://github.com/andreasc89/latte'>https://github.com/andreasc89/latte</a></p>

<h2>Installation</h2>
<p>First you have to make the data-directory writable. This is the place where Lydia needs
to be able to write and create files.</p>
<blockquote>
<code>cd latte; chmod 777 site/data</code>
</blockquote>

<p>Second, you have to enter the username and password for the MySQL-database.</p>
<blockquote>
<code>Configure the login credenials and server for your MySQL-database i the config.php file in the <b>site</b>-folder.</code>
</blockquote>

<p>And the last step, initialise the modules for Latte. You can do this through a controller. Point your browser to the following link.</p>
<blockquote>
<a href='<?=create_url('module/install')?>'>module/install</a>
</blockquote>