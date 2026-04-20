<a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/users/create">Create User</a>

<?php foreach ($users as $user): ?>
    <p>Name: <?= htmlspecialchars($user->name) ?></p>
    <p>Email: <?= htmlspecialchars($user->email) ?></p>

    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/users/edit?id=<?= $user->id ?>">Edit</a>

    <form method="POST" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/users/delete" style="display:inline;">
        <input type="hidden" name="id" value="<?= $user->id ?>">
        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
    </form>

    <hr>
<?php endforeach; ?>