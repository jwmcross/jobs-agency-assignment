<section class="left">
    <ul>
        <li><a href="/admin/jobs/list">Jobs</a></li>
    </ul>
</section>

<section class="right">
    <h2><?=$heading ?? 'Add Job'?></h2>

    <?php if(isset($errors)):?>
        <?php foreach($errors as $error):?>
            <p style="color: red;"><?=$error?></p>
        <?php endforeach;?>
    <?php endif;?>

    <form action="/admin/jobs/<?=$action?>" method="POST">

        <?php if(isset($job)):?>
            <input type="hidden" name="job[id]" value="<?=$job->id ?? ''?>" />
            <input type="hidden" name="job[active]" value="1" />
        <?php endif;?>
        <label>Title</label>
        <input type="text" name="job[title]" value="<?=$job->title ?? ''?>" />

        <label>Description</label>
        <textarea name="job[description]"><?=$job->description ?? ''?></textarea>

        <label>Location</label>
        <input type="text" name="job[location]" value="<?=$job->location ?? ''?>" />


        <label>Salary</label>
        <input type="text" name="job[salary]" value="<?=$job->salary ?? ''?>" />

        <label>Category</label>
        <select name="job[categoryId]">
            <option value="">Select Category---</option>
            <?php
            foreach ($categories as $category): ?>
                <?php if($category->id==$job->categoryId) : ?>
                    <option selected="selected" value="<?=$category->id?>"><?=$category->name?></option>
                <?php else:?>
                    <option value="<?=$category->id?>"><?=$category->name?></option>
                <?php endif;?>
            <?php endforeach; ?>
        </select>

        <label>Closing Date</label>
        <input type="date" name="closingDate" value="<?=$job->closingDate ?? '' ?>"  />

        <input type="submit" name="submit" value="Save" />
    </form>

</section>
