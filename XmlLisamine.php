<?php
$xml=simplexml_load_file("order.xml");
// väljastab massivist getChildrens
function getPurchase($xml){
    $array=getName($xml);
    return $array;
}
// väljastab  laste andmed
function getName($people){
    $result=array($people);
    $childs=$people -> shipTo;

    if(empty($childs))
        return $result;

    foreach ($childs as $child){
        $array=getName($child);
        $result=array_merge($result, $array);
    }
    return $result;
}

function getParent($peoples, $people){
    if ($people == null) return null;
    foreach ($peoples as $parent){
        if (!hasChilds($parent)) continue;
        foreach ($parent->shipTo as $child){
            if($child->name == $people->name){
                return $parent;
            }
        }
    }
    return null;
}
function hasChilds($people){
    return !empty($people -> name);
}

function getAllstreets($items){
    $streets = $items -> street -> name;
    $allStreets = null;

    for ($i = 0; $i < count($streets); $i++){
        $allStreets .= " | ".$streets[$i]." | ";
    }
    return $allStreets;
}

// Searching planets in table
function searchByName($searchWord){
    global $peoples;
    $result=array();
    foreach ($peoples as $people){
        $parent=getParent($peoples, $people);
        if (empty($parent)) continue;
        if (substr(strtolower($people -> name), 0, strlen($searchWord)) == strtolower($searchWord)){
            array_push($result, $people);
        }
    }
    return $result;
}

$peoples=getPurchase($xml);

?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Order purchase</title>
</head>
<header><p>Order purchase</p></header>
<body>
<style>
    body{

        height: 100%;
    }

    header{
        margin-top: 75px;
        background-color: rgba(0, 0, 0, 0.4);
        color: aliceblue;
        font-family: "Arial Black";
        font-style: italic;
        margin-bottom: 50px;
        text-align: center;
        font-size: 50px;
    }

    #firstDiv {
        margin: auto;
        display: flex;
    }

    #code {
        position:absolute;
        background-color: rgba(255, 255, 255, 0.6);
        overflow: scroll;
        height: 550px;
    }

    #secondDiv {
        padding: 20px 50px;
        background-color: rgba(255, 255, 255, 0.4);
        align-items: center;
        justify-content: center;
        width: 42.5%;
        margin: auto;
        border-radius: 20px;
        margin-right: 200px;
    }

    table {
        border-collapse: collapse;
        width: 810px;
        height: auto;
        font-family: "Comic Sans MS";
    }
    table tr th {
        background-color: black;
        color: aliceblue;
        text-align: center;
        height: 35px;
        font-size: 17px;
        font-family: "Arial Black";
    }

    table td {
        height: auto;
        text-align: center;
    }
</style>

<div id="firstDiv">
    <div id="code">
        <?php
        highlight_file("xmlLisamine.php")
        ?>
    </div>
    <div id="secondDiv">
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Street</th>
                <th>ZIP</th>
            </tr>
            <tr>
                <?php
                foreach ($peoples as $people){
                    $parent=getParent($peoples, $people);
                    if (empty($parent)) continue;

                    $parentOfParent=getParent($peoples, $parent);
                    echo '<tr>';

                    echo '<td>'. $parent -> name.'</td>';
                    echo '<td>'. $people -> name.'</td>';
                    if ($people -> street -> name == null) {
                        echo '<td>'.'---'.'</td>';
                    }
                    else{
                        echo '<td>'. getAllstreets($people).'</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </tr>
        </table>
        <hr>
        <form action="?" method="post"><br>
            <label for="planetName" style="font-family: 'Arial Black'">Planet name:</label>
            <br>
            <input type="text" name="search" placeholder="name...">
            <button>OK</button>
        </form>
        <br>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Streets</th>
                <th>ZIP-codes</th>
            </tr>
            <tr>
                <?php
                if (!empty($_POST['search'])){
                    $result=searchByShipToName($_POST["search"]);
                    // sama tabel
                    foreach ($result as $people){
                        $parent=getParent($peoples, $people);
                        if (empty($parent)) continue;

                        $parentOfParent=getParent($peoples, $parent);
                        echo '<tr>';

                        echo '<td>'. $parent -> name.'</td>';
                        echo '<td>'. $people -> name.'</td>';
                        if ($people -> street -> name == null) {
                            echo '<td>'.'---'.'</td>';
                        }
                        else{
                            echo '<td>'. getAllstreets($people).'</td>';
                        }

                        echo '</tr>';

                    }
                }
                ?>
            </tr>
        </table>
    </div>
</div>

</body>
</html>