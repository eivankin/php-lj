<?php
/**
 * Базовый шаблон для всех страниц сайта.
 *
 * Как использовать:
 * 1. Опционально: задать желаемые значения для заголовка ($title), сообщения для пользователя ($message) и содержимого страницы ($content).
 * 2. Подключить файл через команду require.
 */
?>
<html lang="ru">
<head>
    <title>
        <?php
        // Вывести заголовок страницы, если он дан.
        if (isset($title))
            echo $title;
        ?>
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header id="top">
    <nav>
        <ul class="menu">
            <li><img src="/assets/logo.svg" width="40" height="40" alt="Логотип"></li>
            <li><a class="white" href="/">Главная</a></li>
            <li><a class="white" href="/users/">Пользователи</a></li>
            <li><a class="white" href="/entries/">Публикации</a></li>
            <li><a class="white" href="/categories/">Категории</a></li>
            <li><a class="white" href="/tags/">Теги</a></li>
        </ul>
    </nav>
    <nav>
        <ul class="menu">
            <?php
            // Предлагать разные действия для анонимных (вход) и аутентифицированных (выход и переход к личному кабинету) пользователей.
            if (isset($_SESSION['user_id'])) {
                echo '<li><a class="white" href="/account">Личный кабинет</a></li>
                <li><a class="white" href="/logout">Выйти</a></li>';
            } else {
                echo '<li><a class="white" href="/login">Войти</a></li>
                    <li><a class="white" href="/register">Зарегистрироваться</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>
<main>
    <?php
    // Если передано сообщение для пользователя, то вывести его.
    if (isset($message))
        echo '<div class="message fixed-width"><div>' . $message . '</div></div>';

    // Если передано содержимое страницы, то вывести его.
    if (isset($content))
        echo $content;
    ?>
</main>
<footer>
    <p><a class="white" href="#top">↑ Наверх</a></p>
    <p>Связаться с администратором: <a class="white" href="mailto:webmaster@<?php echo $_SERVER['HTTP_HOST'] ?>">
            webmaster@<?php echo $_SERVER['HTTP_HOST'] ?></a></p>
    <hr>
    <p>&copy; 2022</p>
</footer>
</body>
</html>