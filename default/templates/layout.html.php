<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title><?=$title ?? "Jo's Jobs";?></title>
</head>
<body>
    <header>
        <section>
            <aside>
                <h3>Office Hours:</h3>
                <p>Mon-Fri: 09:00-17:30</p>
                <p>Sat: 09:00-17:00</p>
                <p>Sun: Closed</p>
            </aside>
            <h1>Jo's Jobs</h1>
        </section>
    </header>

    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/jobs/list">Jobs</a>
                <ul>
                    <?php foreach($categories as $category) : ?>
                        <li><a href="/jobs/list/category?categoryId=<?=$category->id?>"><?=$category->name?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/about-us">About Us</a></li>
            <li><a href="/faq">FAQ</a></li>
            <li><a href="/contact-us">Contact us</a></li>
            <?php if ($loggedin) :?>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/logout">Logout</a></li>
            <?php else: ?>
                <li><a href="/login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <img src="/images/randombanner.php" />

    <main class="<?=$mainClass ?? 'home'?>">
        <?=$output?>
    </main>

    <footer>
        &copy; Jo's Jobs <?=(new DateTime())->format('Y');?>
    </footer>
</body>
</html>
