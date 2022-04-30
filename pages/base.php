<html lang="ru">
<head>
    <title>
        <?php
        if (isset($title))
            echo $title;
        ?>
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
<header>
    <h1>Блог</h1>
    <nav>
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="/users">Пользователи</a></li>
            <li><a href="/entries">Публикации</a></li>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<li><a href="/account">Личный кабинет</a></li>';
                echo '<li><a href="/logout">Выйти</a></li>';
            } else {
                echo '<li><a href="/login">Войти</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>
<main>
    <?php
    if (isset($message))
        echo '<p>' . $message . '</p>';

    if (isset($content))
        echo $content;
    ?>
</main>
<footer>
    &copy; 2022
</footer>
</body>
</html>