<form method="post" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/users/store">
    <input type="text" name="name" placeholder="Enter Name" />
    <br/>
    <input type="email" name="email" placeholder="Enter Email" />
    <br>

    <input type="submit" value="Create"/>
</form>