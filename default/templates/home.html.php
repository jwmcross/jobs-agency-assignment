
$jobs
$errors

<?php if(isset($errors)): ?>
    <?php foreach($errors as $error): ?>
        <p><?=$error?></p>
    <?php endforeach ?>
<?php endif ?>

<ul class="listing">
    <?php foreach($jobs as $job): ?>
        <li>
            <div class="details">
                <h2><?=$job->title;?></h2>
                <h3><?=$job->getCategory()->name?></h3>
                <h3><?=$job->salary;?></h3>
                <p><?=nl2br($job->description);?></p>
                <a class="more" href="/apply?id=<?=$job->id;?>">Apply for this job</a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>


id, start_time, end_time, date, user_id, amount