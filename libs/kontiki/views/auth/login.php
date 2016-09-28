<?php if (isset($_POST['username'])):  ?>
  <p><strong>Error</strong></p>
<?php endif; ?>

<form action="" method="POST">

<label for="username">Username</label>
<input type="text" name="username" id="username" size="20" value="" />

<label for="password">Password</label>
<input type="password" name="password" id="password" size="20" value="" />
<input type="submit" value="Login" />

</form>
