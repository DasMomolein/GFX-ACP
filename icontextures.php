<?php
include("header.php");
include("pagetest.php");

echo "<h1>Icontextures ";
if (isset($_REQUEST["webby"])) {
    echo "- " . $_REQUEST["webby"];
}
if (isset($_REQUEST["serie"])) {
    echo "- " . $_REQUEST["serie"];
}
echo "</h1>";
$abfrage_webby = "SELECT webby, COUNT(id) as anzahl FROM gfx WHERE typ = 'Icontexture' GROUP BY webby";
$ergebnis_webby = mysqli_query($link, $abfrage_webby);
$webby = "";
while ($row_webby = mysqli_fetch_object($ergebnis_webby)) {
    if (isset($_REQUEST['webby']) && $_REQUEST['webby'] == $row_webby->webby) {
        $webby .= "<option value='" . $row_webby->webby . "' selected>" . $row_webby->webby . " 
				[" . $row_webby->anzahl . "]</option>";
    } else {
        $webby .= "<option value='" . $row_webby->webby . "'>" . $row_webby->webby . " [" . $row_webby->anzahl . "]</option>";
    }
}

$bereich = "icontexture";

if (isset($_GET['action'])) {

    // falls ein Fehler auftritt
    $fehler = array();

    if (!isset($_GET['item'])) {
        $fehler[] = "<b>Es wurde keine Itemid gesetzt!</b>";
    }
    if (!isset($_GET['wertung'])) {
        $fehler[] = "<b>Es ist keine Wertung vorhanden!</b>";
    }

    if (count($fehler) == 0) {

        $rating = new rating($link);
        $rating->setitemid($_GET['item']);
        $rating->setwertung($_GET['wertung']);
        $rating->setip(getenv("REMOTE_ADDR"));
        $rating->setbereich($bereich);

        if (!$rating->create()) {
            $fehler[] = "<b>Es ist ein unbekannter Fehler aufgetreten!</b>";
        }
    }
    if (count($fehler) > 0) {
        for ($i = 0; $i < count($fehler); $i++) {
            echo $fehler[$i] . "<br>";
        }
        echo "<p></p>";
    } else {
        echo "Das Item wurde erfolgreich bewertet.";
    }
}

echo "<table align='center'>";
echo "<tr>";
echo "<td>";
echo "<form method='GET'>";
echo "Webby:</td>";
echo "<td align='center'><select name='webby'>";
echo "<option value=''>Alle</option>";
echo $webby . "</select></td>";
echo "<td><input type='submit' value='OK'></form></td>";
echo "</tr>";
echo "</table>";
echo "<br><br>";

/* SEITEN FUNKTIONEN */
$itemsPerPage = 9; // Wieviele Gfx pro Seite?
$start = 0;
if (!empty($_GET['go'])) {
    $start = ($_GET['go'] - 1) * $itemsPerPage;
}
$zusatz = "";

if (isset($_REQUEST["webby"]) && $_REQUEST["webby"] != "") {
    pagemittypundwebby($itemsPerPage, "gfx", "Icontexture", save($_REQUEST["webby"], $link), $link);
    $zusatz = "AND webby = '" . save($_REQUEST["webby"], $link) . "'";
} else {
    pagemittyp($itemsPerPage, "gfx", "Icontexture", $link);
}
/* SEITEN FUNKTIONEN ENDE*/

echo "<table align='center'>";
echo "<tr>";

if ($start >= 0) {
    $abfrage_icons = "SELECT * FROM gfx WHERE typ = 'Icontexture' " . $zusatz . " ORDER BY id DESC LIMIT 
			" . $start . ", " . $itemsPerPage;
} else {
    $abfrage_icons = "SELECT * FROM gfx WHERE typ = 'Icontexture' " . $zusatz . " ORDER BY id DESC LIMIT 
			0, " . $itemsPerPage;
}

$i = 0;
$ergebnis_icons = mysqli_query($link, $abfrage_icons);
while ($row_icons = mysqli_fetch_object($ergebnis_icons)) {
    if ($i % 3 == 0 && $i != 0) {
        echo "</tr><tr>";
    }
    $i++;

    echo "<td>";
    echo "<div class='gfx'>";
    echo "<div class='gfx'>";
    echo "<img src='Icontextures/" . $row_icons->id . $row_icons->bild . "'>";
    echo "</div>";
    echo "Von: " . $row_icons->webby . "<br />";
    echo "Am: " . date("d.m.y", $row_icons->timestamp) . "<br />";
    echo "<div style='text-align: center;'>";
    $werte = ratingszaehlen($row_icons->id, $bereich, $link);
    for ($j = 1; $j <= 5; $j++) {

        $ratingitem = new rating($link);
        $ratingitem->setip(getenv("REMOTE_ADDR"));
        $ratingitem->setbereich($bereich);
        $ratingitem->setitemid($row_icons->id);

        if (!$ratingitem->ipvoted()) {
            echo "<A href='?action=rating&item=" . $row_icons->id . "&wertung=" . $j . "'>";
        }
        if ($j <= $werte['rate']) {
            echo "<img src='ratingsystem/star.png' border='0'> ";
        } else {
            echo "<img src='ratingsystem/star_leer.png' border='0'> ";
        }
        if (!$ratingitem->ipvoted()) {
            echo "</A>";
        }
    }
    echo "(" . $werte['anzahl'] . ")";
    echo "</div>";
    echo "</div>";
    echo "</td>";

}
echo "</tr>";
echo "</table>";

include("footer.php");
?>
