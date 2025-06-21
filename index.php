<?php
    session_start();
    
    //Data Array
    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [
            ["id" => 1, "title" => "Tugas Nomor 1", "status" => "belum"],
            ["id" => 2, "title" => "Tugas Nomor 2", "status" => "selesai"],
        ];
    }
    $tasks = $_SESSION['tasks'];

    //Menambahkan Tugas
    if (isset($_POST['new_task']) && !empty($_POST['new_task'])) {
        $newTitle = htmlspecialchars($_POST['new_task']);
        $lastTask = end($tasks);
        $newId = $lastTask ? $lastTask['id'] + 1 : 1;
        $tasks[] = ["id" => $newId, "title" => $newTitle, "status" => "belum"];
    }

    //Menandai Tugas
    if (isset($_GET['toggle'])) {
        foreach ($tasks as &$task) {
            if ($task['id'] == $_GET['toggle']) {
                $task['status'] = ($task['status'] == 'belum') ? 'selesai' : 'belum';
                break;
            }
        }
        // Simpan kembali ke session
        $_SESSION['tasks'] = $tasks;
        // Redirect untuk menghilangkan parameter toggle
        header("Location: index.php");
        exit;
    }

    //Menghapus Tugas
    if (isset($_GET['hapus'])) {
        $tasks = array_filter($tasks, function ($task) {
            return $task['id'] != $_GET['hapus'];
        });
        $_SESSION['tasks'] = $tasks;
        header("Location: index.php"); // <- Redirect
        exit;
    }

    //Mengedit Tugas
    if (isset($_POST['edit_id']) && isset($_POST['edit_task'])) {
        foreach ($tasks as &$task) {
            if ($task['id'] == $_POST['edit_id']) {
                $task['title'] = htmlspecialchars($_POST['edit_task']);
                break;
            }
        }
    }

    $_SESSION['tasks'] = $tasks;

    //Fungsi Menampilkan Data Array
    function tampilkanDaftar($tasks) {
    foreach ($tasks as $task) {
        $checked = ($task['status'] == 'selesai') ? 'checked' : '';
        $labelClass = ($task['status'] == 'selesai') ? 'form-check-label selesai' : 'form-check-label';

        echo "
            <li class='list-group-item d-flex justify-content-between align-items-center'>
                <div class='form-check'>
                    <input class='form-check-input me-2' type='checkbox' onclick='location.href=\"?toggle={$task['id']}\"' $checked>
                    <label class='$labelClass'>{$task['title']}</label>
                </div>
                <div>
                    <a href='?hapus={$task['id']}' class='btn btn-sm btn-danger'>Hapus</a>
                    <button onclick='isiFormEdit(\"{$task['id']}\", \"{$task['title']}\")' class='btn btn-sm btn-warning'>Edit</button>
                </div>
            </li>
            ";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Aplikasi To-Do List Sederhana</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
        <div class="container mt-5">    
            <h1 class="text text-center">Aplikasi To-Do List</h1>
            <form action="" method="POST" class="mb-4">
                <div class="input-group">
                    <input type="text" name="new_task" class="form-control" placeholder="Masukkan tugas baru..." required>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>

            <form action="" method="POST" id="formEdit" style="display:none;" class="mb-4">
                <div class="input-group">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="text" name="edit_task" id="edit_task" class="form-control" required>
                    <button class="btn btn-success" type="submit">Simpan Perubahan</button>
                </div>
            </form>
            <ul class="list-group">
                <?php tampilkanDaftar($tasks); ?>
            </ul>
        </div>

        <script>
            function isiFormEdit(id, title) {
                document.getElementById('formEdit').style.display = 'block';
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_task').value = title;
                window.scrollTo(0, 0);
            }
        </script>
    </body>
</html>