<?php
    // login page

    # generate random string
    function generateCode($length=6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length)
        {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }

    # connect to database
    $dbconn = pg_connect("host=localhost port=5433 dbname=site_db user=meduser password=meduser")
        or die('Невозможно соединиться с БД: ' . pg_last_error());

    if(isset($_POST['submit']))
    {
        $login = $_POST["login"];

        # check unique user
        $result = pg_query_params($dbconn, "select id, password from users where login=$1", array($login))
            or die('Ошибка запроса: ' . pg_last_error());

        $data = pg_fetch_row($result);

        # check password
        if($data['password'] === md5(md5($_POST['password'])))
        {
            # generate random number
            $hash = md5(generateCode(10));

            if(!@$_POST['not_attach_ip'])
            {
                # transform ip address to integer
                $insip = ip2long($_SERVER['REMOTE_ADDR']);
            }

            
        }
    }
?>

<form method="POST">
Логин <input name="login" type="text"><br>
Пароль <input name="password" type="password"><br>
Не прикреплять к IP(не безопасно) <input type="checkbox" name="not_attach_ip"><br>
<input name="submit" type="submit" value="Войти">
</form>
