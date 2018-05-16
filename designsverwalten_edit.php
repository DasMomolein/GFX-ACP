<?php
include("header.php");
if (isset($_SESSION["acp"])) {

    echo "<h1>Designs bearbeiten</h1>";

    if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "edit") {
        if (is_numeric($_REQUEST["id"])) {
            if (!empty($_POST["series"]) && !empty($_POST["name"])) {
                $update = "UPDATE gfx SET 
					serie = '" . save($_POST["series"], $link) . "', 
					name = '" . save($_POST["name"], $link) . "' 
					WHERE id = '" . save($_REQUEST["id"], $link) . "'";
                $update_a = mysqli_query($link, $update) OR die(mysqli_error($link));
                echo "<p class='ok'>Design erfolgreich editiert.<br></p>";
                echo '<meta http-equiv="refresh" content="1; url=designsverwalten.php">';
            } else {
                echo "<p class='fault'>Ein Feld wurde nicht ausgefüllt!<br></p>";
            }
        }
    }

    $abfrage_series = "SELECT serie, COUNT(id) as anzahl FROM gfx WHERE typ = 'Design' GROUP BY serie";
    $ergebnis_series = mysqli_query($link, $abfrage_series);
    $series = "";
    while ($row_series = mysqli_fetch_object($ergebnis_series)) {
        $series .= "<option value='" . $row_series->serie . "'";
        if (wert("gfx WHERE id = '" . save($_REQUEST["id"], $link) . "'", "serie", $link) == $row_series->serie) {
            $series .= " selected";
        }
        $series .= ">" . $row_series->serie . "</option>";
    }

    $abfrage_design = "SELECT vorschau, id, vorschaugross FROM gfx WHERE id='" . $_REQUEST["id"] . "'";
    $ergebnis_design = mysqli_query($link, $abfrage_design);
    $row_design = mysqli_fetch_object($ergebnis_design);
    echo "Du bearbeitest folgendes Design:<br>";
    echo "<img src='Designs/" . $row_design->id . "_vorschau" . $row_design->vorschau . "'";
    ?>
    onmouseover="Tip('<img
            src=\'Designs/<?= $row_design->id; ?>_vorschaugross<?= $row_design->vorschaugross; ?>\'>')" onmouseout="UnTip()"
    <?php
    echo "><br><br>";

    echo "<form action='designsverwalten_edit.php?action=edit&id=" . $_REQUEST["id"] . "' method='POST'>";
    echo "<table>";
    echo "<tr>";
    echo "<td>Name:</td>";
    echo "<td>";
    echo "<input type='text' name='name' size='20' value='" . wert("gfx WHERE id='" . save($_REQUEST["id"], $link) . "'", "name", $link) . "'>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Series:</td>";
    echo "<td><select name='series'>" . $series . "</select></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2'><input type='submit' value='EDIT!' /></td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";
} else {
    echo "<p class='fault'>Kein Zutritt!</p>";
}
include("footer.php");
?>
