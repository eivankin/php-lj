<?php
require_once 'db/blog_entry/util.php';
require_once 'db/user/util.php';
require_once 'db/category/util.php';
require_once 'db/blog_entry/views.php';

$is_admin = isset($_SESSION['user_id']) && has_user_permission($_SESSION['user_id'], ADMIN);
$is_moderator = isset($_SESSION['user_id']) && has_user_permission($_SESSION['user_id'], MODERATOR);


$title = 'Публикации';
$content = "
<form style='width: 300px'>
    <h3>Поиск публикаций</h3>
    <div>
        <label for='title'>Заголовок</label>
        <input type='text' id='title' name='title' value='{$_GET['title']}'>
    </div>
    <div>
        <label for='content'>Содержимое</label>
        <input type='text' id='content' name='content' value='{$_GET['content']}'>
    </div>
    <div>
        <label for='author'>Автор</label>
        <select id='author' name='author'>
            <option value=''>Выберите автора</option>
            ";

foreach (get_all_users() as $user) {
    $selected = ($user['id'] == $_GET['author']) ? ' selected' : '';
    $content .= "<option value='{$user['id']}'{$selected}>{$user['username']}</option>";
}

$content .= "
        </select>
    </div>
    <div>
        <label for='category'>Категория</label>
        <select id='category' name='category'>
            <option value=''>Выберите категорию</option>";

foreach (get_categories() as $category) {
    $selected = ($category['id'] == $_GET['category']) ? ' selected' : '';
    $content .= "<option value='{$category['id']}'{$selected}>{$category['name']}</option>";
}

$content .= "
        </select>
    </div>
    
    <div>
        <label for='tags'>Теги</label>
        <select id='tags' name='tags[]' multiple>";

foreach (get_tags() as $tag) {
    $selected = '';
    if (in_array($tag['id'], $_GET['tags'] ?? [])) {
        $selected = ' selected';
    }
    $content .= "<option value='{$tag['id']}'{$selected}>{$tag['name']}</option>";
}

$selected = ['', ' selected'];
if (isset($_GET['mode']) && !$_GET['mode'])
    $selected = array_reverse($selected);

$content .= "</select>
    </div>
    <div>
        <label for='mode'>Тип поиска</label>
        <select id='mode' name='mode' required>
            <option value='0'{$selected[0]}>Объединение признаков (OR)</option>
            <option value='1'{$selected[1]}>Пересечение признаков (AND)</option>
        </select>
    </div>";

$selected = ['', '', ''];
$selected_index = null;
if ($_GET['order_by'] == 'published') {
    $selected_index = 0;
} elseif ($_GET['order_by'] == 'edited') {
    $selected_index = 1;
} elseif ($_GET['order_by'] == 'title') {
    $selected_index = 2;
}

if (isset($selected_index)) {
    $selected[$selected_index] = ' selected';
}

$content .= "
    <div>
        <label for='order_by'>Сортировать по столбцу</label>
        <select id='order_by' name='order_by'>
            <option>Выберите столбец</option>
            <option value='published'{$selected[0]}>Дата публикации</option>
            <option value='edited'{$selected[1]}>Дата редактирования</option>
            <option value='title'{$selected[2]}>Заголовок</option>
        </select>
    </div>";

$selected = [' selected', ''];
if (!empty($_GET['order']))
    $selected = array_reverse($selected);
$content .= "
    <div>
        <label for='order'>Тип сортировки</label>
        <select id='order' name='order'>
            <option value='0'{$selected[0]}>По возрастанию</option>
            <option value='1'{$selected[1]}>По убыванию</option>
        </select>
    </div>
    <button type='submit'>Поиск</button>
    <a href='./'><button type='button'>Сбросить фильтры</button></a>
</form>
<p><a href='./new'><button>Добавить публикацию</button></a></p>
";
$content .= '<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Заголовок</th>
            <th>Автор</th>
            <th>Категория</th>
            <th>Дата публикации</th>
            <th>Дата последнего редактирования</th>
            <th>Теги</th>
            <th>Просмотры</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>';

foreach (get_entries($_GET['title'], $_GET['content'],
    (!empty($_GET['category'])) ? $_GET['category'] : null,
    (!empty($_GET['author'])) ? $_GET['author'] : null, $_GET['tags'], $_GET['mode'] ?? true,
            $_GET['order_by'], $_GET['order'] ?? true) as $entry) {
    $actions = '';
    if ($is_admin || $entry['author_id'] == $_SESSION['user_id']) {
        $actions .= "<a href='./{$entry['id']}/edit'>Редактировать</a> | " .
            "<a href='./{$entry['id']}/delete'>Удалить</a>";
    } elseif ($is_moderator) {
        $actions .= "<a href='./{$entry['id']}/delete'>Удалить</a>";
    }

    $tags = join(' | ', array_map(function ($t) {
        return $t['name'];
    }, get_entry_tags($entry['id'])));

    $views_count = get_views_count($entry['id']);

    $author = get_user($entry['author_id']);
    $category = get_category($entry['category_id']);
    $content .= "<tr>
        <td>{$entry['id']}</td>
        <td><a href='./{$entry['id']}'>{$entry['title']}</a></td>
        <td><a href='/users/{$entry['author_id']}'>{$author['username']}</a></td>
        <td><a href='?category={$category['id']}'>{$category['name']}</a></td>
        <td>{$entry['published']}</td>
        <td>{$entry['edited']}</td>
        <td>{$tags}</td>
        <td>{$views_count}</td>
        <td>{$actions}</td>
    </tr>";
}
$content .= '</tbody></table>';
require_once 'pages/base.php';