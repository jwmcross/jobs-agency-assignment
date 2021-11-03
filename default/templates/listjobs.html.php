<section class="left">
    <ul>
        <li><a href="/jobs/list">All Jobs</a></li>
        <li style="color:white;"><strong><u>Jobs By Category:</u></strong></li>
        <?php foreach($categories as $categoryList):?>
            <li
                <?php if(isset($_GET['categoryId']) && $categoryList->id == $_GET['categoryId']):?>
                    class="current"
                <?php endif;?>
            ><a href="/jobs/list/category?categoryId=<?=$categoryList->id;?>"><?=$categoryList->name;?></a></li>
        <?php endforeach;?>
    </ul>
</section>

<section class="right"><table>
        <tr>
            <td>
                <form action="" method="GET">
                    <?php if (isset($_GET['categoryId'])) : ?>
                        <input type="hidden" name="categoryId" value="<?=$_GET['categoryId']?>"/>
                    <?php endif;?>
                    <label>Filter by Location:</label>
                    <select name="location">
                        <option value="">Filter Location---</option>
                        <?php foreach($locations as $location): ?>
                            <option value="<?=$location->location?>"
                                <?php if(isset($_GET['location']) && $location->location == $_GET['location']) echo 'Selected';?>
                            ><?=$location->location?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Go"/></li>
                </form>
            </td>
        </tr>

    </table>
    <h1><?=($category->name ?? 'Job').' Listings';?></h1>
    <ul class="listing">
        <?php foreach($jobs as $job): ?>
            <li>
                <div class="details">
                    <h2><?=$job->title;?></h2>
                    <h3><?=$job->getCategory()->name;?></h3>
                    <h3><?=$job->salary;?></h3>
                    <p><?=nl2br($job->description);?></p>
                    <a class="more" href="/apply?id=<?=$job->id;?>">Apply for this job</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
