<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/user/util.php';

$title = 'Управление правами пользователя';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'users', '/permissions');

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав для управления разрешениями пользователей';
    http_response_code(403);
    exit();
}

$user = get_user($id);
if (!isset($user))
    not_found();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        remove_permission_from_user($id, $_POST['delete_id']);
        $message = 'Разрешение успешно удалено';
    } elseif (isset($_POST['add_id'])) {
        add_permission_to_user($id, $_POST['add_id']);
        $message = 'Разрешение успешно добавлено';
    } elseif (isset($_POST['add_name']) && isset($_POST['add_description'])) {
        $permission_id = get_or_create_permission($_POST['add_name'], $_POST['add_description']);
        add_permission_to_user($id, $permission_id);
        $message = 'Разрешение успешно добавлено';
    }
}

$permissions = get_user_permissions($id);

$content = '<table>
<thead>
    <tr>
        <th>Обозначение</th>
        <th>Описание</th>
        <th>Действия</th>
    </tr>
</thead><tbody>';

foreach ($permissions as $permission) {
    $content .= "<tr>
    <td>{$permission['internal_name']}</td>
    <td>{$permission['description']}</td>
    <td><form method='post'>
        <input type='hidden' name='delete_id' value='{$permission['id']}'>
        <button type='submit'>Удалить</button>
    </form></td>
</tr>";
}

$content .= '
<tr>
        <td><input form="add" type="text" name="add_name" placeholder="Введите обозначение" required></td>
        <td><textarea form="add" rows="1" name="add_description" placeholder="Введите описание" required></textarea></td>
        <td><button form="add" type="submit">Добавить</button></td>
</tr>
<tr>
        <td colspan="2"><select form="add_by_id" name="add_id" required>
            <option value="">Выберите обозначение права для добавления</option>
            ';

foreach (get_all_permissions() as $permission) {
    $content .= "<option value='{$permission['id']}'>{$permission['internal_name']}</option>";
}

$content .= '
        </select></td>
        <td><button form="add_by_id" type="submit">Добавить</button></td>
</tr>
</tbody></table>
<form id="add_by_id" method="post"></form><form id="add" method="post"></form>';
require_once 'pages/base.php';