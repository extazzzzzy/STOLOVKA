<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        session_start();
        $connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');
        $id = $_SESSION['id'];
        $role = $_SESSION['role'];

        if ($role != "user" || $_SESSION['id'] == "")
        {
            header("Location: http://localhost/Pages/auth.php");
            die();
        }
        else {
            $username = $connectMySQL -> query("SELECT `first_name` FROM `users` WHERE `id` = '$id'") -> fetch_assoc()['first_name'];
            $phone_number = $connectMySQL -> query("SELECT `phone_number` FROM `users` WHERE `id` = '$id'") -> fetch_assoc()['phone_number'];
            $address = $connectMySQL -> query("SELECT `address` FROM `users` WHERE `id` = '$id'") -> fetch_assoc()['address'];

            echo "<div><h1>Name: " . $username . "</h1>";
            echo "<h3>Phone: " . $phone_number . "</h3>";
            echo "<h3>Address: " . $address . "</h3>";
            echo "<h3>Role: " . $role . "</h3></div>";

            echo "<form id='reg' style='display: none;' action='../php/userDataChange.php' method='post'>
                    <input type='text' name='edit_f_name' style='margin-bottom: 5px;' value='$username'></br>
                    <input type='text' name='edit_add' style='margin-bottom: 5px;' value='$address'></br>
                    <input type='submit' name='edit_btn' style='margin-bottom: 5px;' value='Внести изменения'>
                </form>"; 
        }
    ?>
    <div style='margin-bottom: 5px;'><button id="show_form" style="display:block;" onclick="HideForm(0)">Изменить данные</button>
    <button id="hide_form" style="display:none;" onclick="HideForm(1)">Скрыть</button></div>
    
    <div style='margin-bottom: 5px;'><form action="../php/logout.php" method="post">
        <button type="submit" id="logout_btn">Выйти</button>
    </form></div>

    <script>
        function HideForm(bool) {
            if (bool == 0)
            {
                document.getElementById('reg').style.display='block';
                document.getElementById('show_form').style.display='none';
                document.getElementById('hide_form').style.display='block';
            }
            else {
                document.getElementById('reg').style.display='none';
                document.getElementById('show_form').style.display='block';
                document.getElementById('hide_form').style.display='none';
            }
        }
    </script>
</body>
</html>