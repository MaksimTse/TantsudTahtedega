<?php
require_once('conf.php');
session_start();

if(isset($_REQUEST["paarinimi"]) && !empty($_REQUEST["paarinimi"]) && isAdmin()){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO tantsud (tantsupaar, punktid, ava_paev) VALUES(?, 50, NOW())");
    $kask->bind_param("s", ($_REQUEST["paarinimi"]));
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    //exit();
}
//punktide lisamine
if(isset($_REQUEST["heatants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid=punktid+1 WHERE id=?");
    $kask->bind_param("i", ($_REQUEST["heatants"]));
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    //exit();
}
if(isset($_REQUEST["halbtants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid=punktid-1 WHERE id=?");
    $kask->bind_param("i", ($_REQUEST["halbtants"]));
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    //exit();
}
function isAdmin(){
    return isset($_SESSION['onAdmin']) && $_SESSION['onAdmin'];
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tantsud tähtedega</title>
    <link rel="stylesheet" type="text/css" href="Style2.css">
</head>
<body>
<header>
    <?php
    if(isset($_SESSION['kasutaja'])){
        ?>
        <h1>Tere, <?="$_SESSION[kasutaja]"?></h1>
        <a href="logout.php" id="log">Logi välja</a>
        <?php
    } else {
        ?>
        <a href="#" onclick="openModal()" id="log">Logi sisse</a>
        <?php
    }
    ?>
    <h2>Tantsud tähtedega</h2>
    <h3>KasutajaLeht</h3>
</header>
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="pass" required>

            <input type="submit" value="Login">
        </form>
    </div>
</div>

<table>
    <tr>
        <th>Tantsupaari nimi</th>
        <th>Punktid</th>
        <th>Kuupäev</th>
        <th>Kommentaarid</th>
    </tr>
<?php
global $yhendus;
    $kask=$yhendus->prepare("SELECT id, tantsupaar, punktid, ava_paev, kommentaarid FROM tantsud WHERE avalik=1");
    $kask->bind_result($id, $tantsupaar, $punktid, $paev, $komment);
    $kask->execute();
    while($kask->fetch()){
        echo "<tr>";
        $tantsupaar=htmlspecialchars($tantsupaar);
        echo "<td>".$tantsupaar."</td>";
        echo "<td>".$punktid."</td>";
        echo "<td>".$paev."</td>";
        echo "<td>".$komment."</td>";
        echo "
        <form action='?'>
        <input type='hidden' value='$id' name='komment'>
        <input type='text' name='uuskomment' id='uuskomment'>
        <input type='submit' value='OK'>
        </form>
        ";
        if(isAdmin()){
                echo "<td><a href='?heatants=$id'>Lisa +1 punkt</a></td>";
                echo "<td><a href='?halbtants=$id'>Lisa -1 punkt</a></td>";
        }
        echo "</tr>";
    }

?>
    <?php
    if(isAdmin()){?>
    <br><br>
    <form action="?">
        <label for="paarinimi"><strong>Lisa uus paar</strong></label>
        <input type="text" name="paarinimi" id="paarinimi">
        <input type="submit" value="Lisa">
    </form>
    <?php
    }
    ?>
</table>

</body>
<script>
    function openModal() {
        document.getElementById('loginModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('loginModal').style.display = 'none';
    }

    window.onclick = function (event) {
        var modal = document.getElementById('loginModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
</html>
