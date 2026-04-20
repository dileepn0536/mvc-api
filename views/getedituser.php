<form method="post" action="<?php echo dirname($_SERVER['SCRIPT_NAME']).'/users/update' ?>">
    <input type="hidden" value="<?php echo $userinfo['id'] ?>" name="id"/>
    <input type="text" name="name" value="<?php echo $userinfo['name'] ?>" />
    <br/>
    <input type="email" name="email" value="<?php echo $userinfo['email'] ?>" />
    <br/>
    <input type="submit" value="Update"/>
</form>