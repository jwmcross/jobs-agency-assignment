<section class="left">
    <ul>
        <li><a href="/admin/enquiry/list">Outstanding</a></li>
        <?php if ($user->hasPermission(\Jobs\Entity\Users::MANAGE_ENQUIRIES)): ?>
            <li><a href="/admin/enquiry/complete">Complete</a></li>
        <?php endif;?>
    </ul>
</section>

<section class="right">
    <h2>Completed Enquiries</h2>
    <table>
        <thead>
        <tr>
            <th style="width: 5%">Name</th>
            <th style="width: 5%">Email</th>
            <th style="width: 5%">Telephone</th>
            <th style="width: 20%">Enquiry</th>
            <th style="width: 5%">Completed By</th>
            <th style="width: 5%">&nbsp;</th>
        </tr>
        <?php foreach ($enquiries as $enquiry) :?>
            <tr>
                <td><?=$enquiry->name;?></td>
                <td><?=$enquiry->email;?></td>
                <td><?=$enquiry->telephone;?></td>
                <td><?=$enquiry->enquiry_details;?></td>
                <td><?=$enquiry->getUser()->username;?></td>
                <td>
                    <form action="/admin/enquiry/complete/delete" method="POST">
                        <input type="hidden" name="enquiry[id]" value="<?=$enquiry->id?>">
                        <input type="submit" name="submit" value="Delete"?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </thead>
    </table>
</section>