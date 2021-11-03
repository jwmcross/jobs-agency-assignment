<section class="left">
    <ul>
        <li><a href="/admin/category/list">List Category</a></li>
        <li><a href="/admin/category/add">Add Category</a></li>
    </ul>
</section>

<section class="right">
    <h2>Categories</h2>
    <a class="new" href="/admin/category/add">Add new category</a>

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th style="width: 15%">&nbsp;</th>
            <th style="width: 15%">&nbsp;</th>
        </tr>

        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?=$category->name?></td>
                <td><a style="float: right" href="/admin/category/edit?id=<?=$category->id?>">Edit</a></td>
                <td><form method="POST" action="/admin/category/delete">
                        <input type="hidden" name="id" value="<?=$category->id?>" />
                        <input type="submit" name="submit" value="Delete" />
                    </form></td>
            </tr>
        <?php endforeach;?>
        </thead>
    </table>
</section>
