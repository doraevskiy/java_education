<?php
    // registration page

    # connect to database
    $dbconn = pg_connect("host=localhost port=5433 dbname=site_db user=meduser password=meduser")
        or die('Невозможно соединиться с БД: ' . pg_last_error());

    # check submit request
    if(isset($_POST['submit']))
    {
        $err = array();

        $login = $_POST['login'];

        # check login
        if(!preg_match("/^[a-zA-Z0-9]+$/", $login))
        {
            $err[] = "Логин может состоять только из букв английского алфавита и цифр";
        }

        if(strlen($login) < 3 or strlen($login) > 30)
        {
            $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
        }

        # check unique user
        $result = pg_query_params($dbconn, "select count(id) from users where login=$1", array($login))
            or die('Ошибка запроса: ' . pg_last_error());

        if(pg_fetch_row($result)[0] > 0)
        {
            $err[] = "Пользователь с таким логином уже существует в БД";
        }

        if(count($err) === 0)
        {
            $password = md5(md5(trim($_POST['password'])));

            pg_query_params($dbconn, "insert into users (login, password) values ($1, $2)",
                array($login, $password))
                or die('Ошибка запроса: ' . pg_last_error());
            
            header("Location: login.php"); exit();
        }
        else
        {
            print "<b>При регистрации произошли следующие ошибки:</b><br>";

            foreach($err as $error)
            {
                print $error."<br>";
            }
        }
    }
?>

<form method="POST">
Логин: <input name="login" type="text"><br>
Пароль: <input name="password" type="text"><br>
<input name="submit" type="submit" value="Зарегистрироваться">
</form>
