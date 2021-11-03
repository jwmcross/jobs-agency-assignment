<section class="left">
    <ul>
        <li><a href="/admin/category/list">List Category</a></li>
        <li><a href="/admin/category/add">Add Category</a></li>
    </ul>
</section>

<section class="right">
    <h2><?=$heading?></h2>
    <?php if(isset($errors)):?>
        <?php foreach($errors as $error):?>
            <p style="color: red;"><?=$error?></p>
        <?php endforeach;?>
    <?php endif;?>

    <form action="/admin/category/<?=$action?>" method="POST">
        <label>Name</label>
        <?php if (isset($category)):?>
            <input type="hidden" name="category[id]" value="<?=$category->id ?? ''?>" />
            <input type="hidden" name="old_category" value="<?=$category->name ?? ''?>" />
        <?php endif;?>
        <input type="text" name="category[name]" value="<?=$category->name ?? ''?>" />
        <input type="submit" name="submit" value="Save" />
    </form>
</section>