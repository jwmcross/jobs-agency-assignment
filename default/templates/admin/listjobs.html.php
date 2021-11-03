<section class="left">
    <ul>
        <li><a href="/admin/jobs/list">Active Jobs</a></li>
        <li><a href="/admin/jobs/archived">Archived Jobs</a></li>
    </ul>
</section>

<section class="right">
    <h2><?=$heading?></h2>
    <a class="new" href="/admin/jobs/add">Add new job</a>

    <form action="" method="GET">
        <label>Filer by Category:</label>
        <select name="category">
            <option value="">Select---</option>
            <?php foreach($categories as $category) : ?>
                <option value="<?=$category->id?>"
                    <?php if (isset($_GET['category']) && $category->id == $_GET['category']) echo 'Selected';?>
                ><?=$category->name?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Go"/>
    </form>

    <table>
        <thead>
        <tr>
            <th style="width: 15%">Title</th>
            <th style="width: 10%">Category</th>
            <th style="width: 15%">Salary</th>
            <th style="width: 10%">Closing Date</th>
            <th style="width: 10%">&nbsp;</th>
            <th style="width: 5%">&nbsp;</th>
            <th style="width: 5%">&nbsp;</th>
            <th style="width: 5%">&nbsp;</th>
        </tr>

        <?php foreach ($jobs as $job) :?>
            <tr>
                <td><?=$job->title;?></td>
                <td><?=$job->getCategory()->name;?></td>
                <td><?=$job->salary;?></td>
                <td><?=$job->closingDate;?></td>

                <td><a style="float: right" href="/admin/jobs/applicants?jobId=<?=$job->id?>">View applicants(<?=$job->countApplicants() ?? '0'?>)</a></td>
                <td><a style="float: right" href="/admin/jobs/edit?jobId=<?=$job->id?>">Edit</a></td>
                <?php if($job->active ==1):?>
                    <td><form method="post" action="/admin/jobs/archivejob">
                            <input type="hidden" name="job[id]" value="<?=$job->id?>" />
                            <input type="hidden" name="job[active]" value="0" />
                            <input type="submit" name="submit" value="Archive" />
                        </form></td>
                <?php endif;?>

                <td><form method="post" action="/admin/jobs/delete">
                        <input type="hidden" name="job[id]" value="<?=$job->id?>" />
                        <input type="submit" name="submit" value="Delete" />
                    </form></td>
            </tr>
        <?php endforeach; ?>

        </thead>
    </table>
</section>
