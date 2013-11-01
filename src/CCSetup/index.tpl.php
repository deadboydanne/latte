<h1>Installation</h1>

<p>Welcome to the installation process for Latte. To begin with I just ran some tests on the server and this is what I found out:</p>

<br>
<h2>PHP Version</h2>
<?=$phpversion[1]?>

<br>
<h2>Folder site/data writable?</h2>

<?=$folderDataWritable[1]?>

<? if($folderDataWritable && $phpversion): ?>

<br>
<h2>Database connection</h2>
<? if($connectedToDatabase): ?>

<div class="success"><p>Successfully connected to database, excellent!</p></div>

<br>
<h2>Create tables and insert information</h2>

<div class="info">
<p>Only one little step left, let's create the tables in the database and insert some information. <a style="font-weight: bold;" href="<?=create_url('setup','install');?>">Let's go &rarr;</a></p>
<p>Warning, this will erase all previous entries in the database.</p>
</div>

<? else: ?>

<div class="error"><p>Could not connect to database. Please enter your host and login credentials below:</p>
<?=$form?>
</div>

<? endif; ?>

<? else: ?>

<div class="error">The installation was aborted due to problems mentioned above. Please fix these and reload the page.</div>

<? endif; ?>