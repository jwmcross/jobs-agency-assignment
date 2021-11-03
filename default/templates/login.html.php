<h2>Login</h2>

<?php if(isset($errors)):?>
    <?php foreach($errors as $error):?>
        <p style="color: red;"><?=$error?></p>
    <?php endforeach;?>
<?php endif;?>

<form action="/login" method="POST" enctype="multipart/form-data">
    <label>Your name</label>
    <input type="text" name="user[username]" />

    <label>Password</label>
    <input type="password" name="user[password]" />

    <input type="submit" name="submit" value="Login" />
</form>


