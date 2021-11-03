<section class="left">
    <ul>
        <?php if ($user->hasPermission(\Jobs\Entity\Users::MANAGE_JOBS)): ?>
            <li><a href="/admin/jobs/list">Manage Jobs</a></li>
        <?php endif?>

        <?php if ($user->hasPermission(\Jobs\Entity\Users::MANAGE_CATEGORIES)): ?>
            <li><a href="/admin/category/list">Manage Categories</a></li>
        <?php endif?>

        <?php if ($user->hasPermission(\Jobs\Entity\Users::VIEW_ENQUIRIES) ||
            $user->hasPermission(\Jobs\Entity\Users::MANAGE_ENQUIRIES)): ?>
            <li><a href="/admin/enquiry/list">View Enquiries</a></li>
        <?php endif?>

        <?php if ($user->hasPermission(\Jobs\Entity\Users::MANAGE_USERS)): ?>
            <li><a href="/admin/users/list">View All Users</a></li>
            <li><a href="/admin/users/staff">-Staff</a></li>
            <li><a href="/admin/users/clients">-Clients</a></li>
        <?php endif?>
    </ul>
</section>

<section class="right">
    <h2><?=$accountType?> Panel</h2>
    <h3>Logged in as: <?=$user->username;?></h3>
</section>

