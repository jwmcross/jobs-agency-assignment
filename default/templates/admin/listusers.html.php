<section class="left">
    <ul>
        <li><a href="/admin/users/list">All users</a></li>
        <li><a href="/admin/users/clients">Clients</a></li>
        <li><a href="/admin/users/staff">Staff</a></li
    </ul>
</section>

<section class="right">
    <h2><?=$heading?></h2>
    <a class="new" href="/admin/users/add">Add new user</a>

    <table>
        <thead>
        <tr>
            <th style="width: 10%">UserName</th>
            <th style="width: 5%">Account Type</th>
            <th style="width: 5%">Account Status&nbsp;</th>
            <th style="width: 5%">&nbsp;</th>
            <th style="width: 5%">&nbsp;</th>
        </tr>

        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?=$user->username?></td>
                <td><?=$user->getAccountType()?></td>
                <td><?=$user->getAccountStatus()?></td>
                <td>
                    <form method="POST" action="/admin/users/edit">
                        <input type="hidden" value="<?=$user->id?>" name="userId"/>
                        <input type="submit" name="edit" value="Edit" />
                    </form>
                </td>
                <td>
                    <form method="POST" action="/admin/users/delete">
                        <input type="hidden" name="user[id]" value="<?=$user->id?>" />
                        <input type="submit" name="delete" value="Delete" />
                    </form>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</section>