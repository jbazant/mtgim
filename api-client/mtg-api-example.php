<?php
/**
 * @brief mtg-api-lib usage example
 *
 * Simple example of usage mtg-api-lib.
 * Shows basic usage of Mtgim_Api_Client class. There's not implemented any cacheing of results
 * (you should cache results of method Mtgim_Api_Client::shopsAvailable), nor advanced settings
 * of Adapter. For more information refer to documentation.
 */

// ----- user settings -----
// these settings are testing ones - you will get only fake results
// you can change them to your production settings
// if you do not have production setting, contact us
$apiKey = 'testKey';
$secret = 'testSecret';

// ---------- do not edit below this line ----------

// ----- usage example -----
// include library
require_once(__DIR__ . '/mtg-api-lib/Client.php');

// instantiate library
$mtgim = new Mtgim_Api_Client($apiKey, $secret);

// get list of available shops
$shops = $mtgim->shopsAvailable();

// available card types
$cardTypes = $mtgim->typesAvailable();

// local variables
$showResults = FALSE;
$message = NULL;
$cardname = '';
$selectedShop = '';
$selectedType = '';

// if form was sent, try to find card results
if (isset($_POST['cardname']) && !empty($_POST['cardname'])) {
    $cardname = $_POST['cardname'];
    $selectedShop = $_POST['shop'];
    $selectedType = $_POST['foil'];

    // make sure values are valid
    if (!array_key_exists($selectedShop, $shops) || !array_key_exists($selectedType, $cardTypes)) {
        $message = 'Neplatné parametry!';
    }
    else {
        // form is valid, find results
        $results = $mtgim->findPrice($cardname, $selectedShop, $selectedShop);
        if (empty($results)) {
            $message = 'Vašemu hledání neodpovídá žádný výsledek';
        }
        else {
            $showResults = TRUE;
        }
    }
}

// ----- render html page -----
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Příklad použití mtg-api-lib</title>
</head>

<body>
    <h1>Příklad použití mtg-api-lib</h1>

    <?php if (!empty($message)): ?>
        <div id="message-holder">
            <p><?= $message ?></p>
        </div>
    <?php endif; ?>

    <div id="form-holder">
        <form action="mtg-api-example.php" method="post">
            <label for="cardname">Název karty:</label>
            <input type="text" id="cardname" name="cardname" value="<?= htmlspecialchars($cardname); ?>" placeholder="Název karty" />

            <label for="shop">Obchod:</label>
            <select id="shop" name="shop">
                <?php foreach($shops as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $selectedShop == $key ? 'selected="selected"' : '' ?>><?= $val ?></option>
                <?php endforeach; ?>
            </select>

            <label for="foil">Typ karty:</label>
            <select id="foil" name="foil">
                <?php foreach($cardTypes as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $selectedType == $key ? 'selected="selected"' : '' ?>><?= $val ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="send" id="btn-send" value="Vyhledat" />
        </form>
    </div>

    <?php if ($showResults): ?>
        <div id="results-holder">
            <table>
                <thead>
                    <tr>
                        <td>Název karty</td>
                        <td>Edice</td>
                        <td>Kvalita</td>
                        <td>Cena</td>
                        <td>Počet skladem</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?= $result['name'] ?></td>
                            <td><?= $result['expansion'] ?></td>
                            <td><?= $result['quality'] ?></td>
                            <td><?= $result['value'] ?></td>
                            <td><?= $result['amount'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div id="footer">
        <p><strong>Autor:</strong> Jiří Bažant <a href="http://mtgim.cz">MtGiM.cz</a></p>
        <p>&copy; Všechna práva vyhrazena</p>
    </div>

</body>
</html>
