<section class="left">
    <ul>
        <li><a href="/admin/users/list">All users</a></li>
        <li><a href="/admin/users/clients">Clients</a></li>
        <li><a href="/admin/users/staff">Staff</a></li>
    </ul>
</section>

<section class="right">
    <h2><?=$heading?></h2>

    <?php if(isset($errors)):?>
        <?php foreach($errors as $error):?>
            <p style="color: red;"><?=$error?></p>
        <?php endforeach;?>
    <?php endif;?>

    <form action="/admin/users/<?=$action?>" method="POST">

        <?php if(isset($user->id)): ?>
            <input type="hidden" name="user[id]" value="<?= $user->id?>" />
            <input type="hidden" name="old_username" value="<?= $user->username ?? null?>" />
        <?php endif; ?>

        <label>Username</label>
        <input type="text" name="user[username]" value="<?= $user->username ?? ''?>" />

        <label>Password</label>
        <input type="password" name="user[password]" value=""/>

        <label>Confirm-Password</label>
        <input type="password" name="confirm_password"/>

        <label><strong>Set Permissions :</strong></label>
        <?php foreach($permissions as $name => $value): ?>
            <div>
                <label><?=ucwords(strtolower(str_replace('_',' ',$name)))?> : <?=str_repeat('-',3)?></label>
                <input type="checkbox" name="permissions[]" value="<?=$value?>"
                    <?php if(isset($user)) :
                        if($user->hasPermission($value) && $value !== null):?>
                            checked
                        <?php endif;
                    endif;?>
                />
            </div>
        <?php endforeach;?>

        <label>Account Type</label>
        <select name="user[type]">
            <option value="">Select User Type</option>
            <option value="2" <?php if (isset($user)){if($user->type ==2) echo 'selected';}?>>Client</option>
            <option value="1" <?php if (isset($user)){if($user->type ==1) echo 'selected';}?>>Staff</option>
        </select>

        <label>Account Status</label>
        <select name="user[status]">
            <option value="">Select Status</option>
            <option value="0" <?php if (isset($user) && ($user->status == 0 )) echo 'selected';?>>Disabled</option>
            <option value="1" <?php if (isset($user) && ($user->status == 1)) echo 'selected';?>>Active</option>
        </select>

        <input type="submit" name="submit" value="Save" />

    </form>

</section>