<?php
require_once('conf.php');
session_start();

$isAdminView = false;  // Variable to control whether to display admin features
$viewAsUser = isset($_REQUEST["viewAsUser"]) && $_REQUEST["viewAsUser"] == "true";

// Check if the user is an admin
if (isAdmin()) {
    $isAdminView = true;
}

//punktide lisamine
if (isset($_REQUEST["punktid0"]) && $isAdminView) {
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE tantsud SET punktid=0 WHERE id=?");
    $kask->bind_param("i", ($_REQUEST["punktid0"]));
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
}

if (isset($_REQUEST["peitmine"]) && $isAdminView) {
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE tantsud SET avalik=0 WHERE id=?");
    $kask->bind_param("i", ($_REQUEST["peitmine"]));
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
}

if (isset($_REQUEST["naitmine"]) && $isAdminView) {
    $kask = $yhendus->prepare("UPDATE tantsud SET avalik=1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["naitmine"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
}

if (isset($_REQUEST["kustuta_kommentaar_id"]) && $isAdminView) {
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE tantsud SET kommentaarid='' WHERE id=?");
    $kask->bind_param("i", $_REQUEST["kustuta_kommentaar_id"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
}

if (isset($_REQUEST["kustuta"])) {
    $paring = $yhendus->prepare("DELETE FROM tantsud WHERE id=?");
    $paring->bind_param("i", $_REQUEST["kustuta"]);
    $paring->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    //exit();
}

function isAdmin()
{
    return isset($_SESSION['onAdmin']) && $_SESSION['onAdmin'];
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tantsud tähtedega</title>
    <link rel="stylesheet" type="text/css" href="Style2.css">
</head>

<body>
<header>
    <?php
    if (isset($_SESSION['kasutaja'])) {
        ?>
        <h1>Tere, <?= "$_SESSION[kasutaja]" ?></h1>
        <a href="logout.php" id="log">Logi välja</a>
        <?php
    } else {
        ?>
        <a href="login.php" id="log">Logi sisse</a>
        <?php
    }
    "<br>";
    if ($isAdminView && !$viewAsUser) {
        echo '<a href="?viewAsUser=true" id="log">Näidata Admini nimel</a>';
    } elseif ($isAdminView && $viewAsUser) {
        echo '<a href="?viewAsUser=false" id="log">Näidata kasutaja nimel</a>';
    }
    ?>
    <h2>Tantsud tähtedega</h2>
    <h3>AdministreerimisLeht</h3>
</header>

<table>
    <tr>
        <th>Tantsupaari nimi</th>
        <th>Punktid</th>
        <th>Kuupäev</th>
        <th>Kommentaarid</th>
        <th>Avalik</th>
    </tr>
    <?php
    global $yhendus;
    $kask = $yhendus->prepare("SELECT id, tantsupaar, punktid, ava_paev, kommentaarid, avalik FROM tantsud WHERE (avalik=1 OR ?)");
    $kask->bind_param("i", $viewAsUser);
    $kask->bind_result($id, $tantsupaar, $punktid, $paev, $komment, $avalik);
    $kask->execute();
    while ($kask->fetch()) {
        $tekst = "Näita";
        $seisund = "naitmine";
        $tekst2 = "Kasutaja ei näe";
        if ($avalik == 1) {
            $tekst = "Peida";
            $seisund = "peitmine";
            $tekst2 = "Kasutaja näeb";
        }

        echo "<tr>";
        $tantsupaar = htmlspecialchars($tantsupaar);
        echo "<td>" . $tantsupaar . "</td>";
        echo "<td>" . $punktid . "</td>";
        echo "<td>" . $paev . "</td>";
        echo "<td>" . $komment . "</td>";
        echo "<td>" . $avalik . "/" . $tekst2 . "</td>";

        if ($isAdminView && $viewAsUser) {
            echo "<td><a href='?$seisund=$id'>$tekst</a></td>";
            echo "<td><a href='?punktid0=$id' id='kustuta'>Punktid Nulliks!</a></td>";
            echo "<td><a href='?kustuta_kommentaar_id=$id'>Kustuta kommentaar</a></td>";
            echo "<td><a href='?kustuta=$id' id='kustuta'>Kustuta</a></td>";
        }

        echo "</tr>";
    }
    ?>
</table>
</body>

</html>