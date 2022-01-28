<?php

$database = './countries.db'; // database location
$ip_access = 'disabled'; // allow, deny or disabled

////////////////////////////////////////////////////////////

function sqlite($method, $cmd)
{
    global $database, $values;
    try {    
        if (!file_exists($database)){throw new PDOException("database not found");}
        $db = new PDO("sqlite:$database");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("PRAGMA journal_mode = wal;");

        switch ($method) {
            case "query":
                try {
                    $result = $db->query($cmd);
                    return $result->fetchALL(PDO::FETCH_ASSOC);                    
                    $db = null;
                    unset($db);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage() . "<BR />Error Code: " . $e->getCode();
                    $db = null;
                    unset($db);
                    die();
                }
                break;
            case "update":
                try {
                    $update = $db->prepare($cmd);
                    $update->execute();
                    return $update->rowCount();                    
                    $db = null;
                    unset($db);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage() . "<BR />Error Code: " . $e->getCode();
                    $db = null;
                    unset($db);
                    die();
                }
                break;
            case "insert":
                try {
                    $qry = $db->prepare($cmd);
                    $qry->execute($values);
                    return $db->lastInsertId();                    
                    $db = null;
                    unset($db);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage() . "<BR />Error Code: " . $e->getCode();
                    $db = null;
                    unset($db);
                    die();
                }
                break;
            default:
                echo "Not Allowed";
                exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "<BR />Error Code: " . $e->getCode();
        $db = null;
        unset($db);
        die();
    }
}

function getUserIpAddr()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    return $ip;
}

function arrayToXML($array, SimpleXMLElement $xml, $child_name)
{
    foreach ($array as $k => $v) {
        if(is_array($v)) {
            (is_int($k)) ? arrayToXML($v, $xml->addChild($child_name), $v) : $this->arrayToXML($v, $xml->addChild(strtolower($k)), $child_name);
        } else {
            (is_int($k)) ? $xml->addChild($child_name, $v) : $xml->addChild(strtolower($k), $v);
        }
    }

    return $xml->asXML();
}

$allow = ["_", "language", "flag", "continent", "ISO_3166_1_alpha_2", "ISO_3166_1_alpha_3", "ISO_3166_1_numeric", "name_pt", "name_en", "capital_pt", "capital_en", "calling_code", "currency_name_en", "currency_code", "cc_tld", "continent_code", "continent_name_pt", "continent_name_en", "flags_16_16", "flags_24_24", "flags_32_32", "flags_48_48", "flags_64_64", "flags_128_128"];

$check = [];
foreach ($_REQUEST as $REQUEST => $key) {
    $check[] = $REQUEST;
    if (!in_array($REQUEST, $allow)) {
        echo json_encode(["Error" => "$REQUEST Not Allowed"], JSON_PRETTY_PRINT);
        exit();
    }
}

if (in_array($ip_access, ["allow", "deny"], true)) {
    $api_ips = sqlite("query", "SELECT list FROM 'ips';");
    $api_ips = array_column($api_ips, "list");

    $ip = getUserIpAddr();

    if ($ip_access == "allow") {
        if (!in_array($ip, $api_ips)) {
            echo json_encode(["Error" => "Not Allowed"], JSON_PRETTY_PRINT);
            exit();
        }
    } else {
        if (in_array($ip, $api_ips)) {
            echo json_encode(["Error" => "Not Allowed"], JSON_PRETTY_PRINT);
            exit();
        }
    }
}

$language = $_REQUEST["language"];

$all = ["calling_code", "currency_code", "currency_name_en", "cc_tld" ];

if (($_REQUEST["language"] ?? "") == "pt") {
    $language = ["name_pt", "capital_pt", "continent_name_pt", "continent_code"];
} else {
    $language = ["name_en", "capital_en", "continent_name_en", "continent_code"];
}

    switch ($_REQUEST["iso"]) {
        case "alpha_2":
            $iso_type = ["ISO_3166_1_alpha_2"];
            break;
        case "alpha_3":
            $iso_type = ["ISO_3166_1_alpha_3"];
            break;
        case "numeric":
            $iso_type = ["ISO_3166_1_numeric"];
            break;
        default:
            $iso_type = ["ISO_3166_1_alpha_2"];
    }

    switch ($_REQUEST["flag"]) {
        case "16":
            $flag_type = ["flags_16_16"];
            break;
        case "24":
            $flag_type = ["flags_24_24"];
            break;
        case "32":
            $flag_type = ["flags_32_32"];
            break;
        case "48":
            $flag_type = ["flags_48_48"];
            break;
        case "64":
            $flag_type = ["flags_64_64"];
            break;
        case "128":
            $flag_type = ["flags_128_128"];
            break;
        default:
            $flag_type = ["flags_16_16"];
    }

$select = array_merge($language, $iso_type, $all, $flag_type);

foreach ($select as $hide) {
    if (($_REQUEST["$hide"] ?? "") == "hide") {
        if (($key = array_search("$hide", $select)) !== false) {
            unset($select[$key]);
        }
    }
}

$select = implode(",", $select);

if (isset($_REQUEST["continent"])) {
    $continents = ["AF", "AN", "AS", "EU", "NA", "OC", "SA"];
    $continent = strtoupper($_REQUEST["continent"]);
    if (in_array($continent, $continents)) {
        $countries = sqlite("query", "SELECT $select FROM 'countries' WHERE continent_code = '$continent';");
    } else {
        $countries = sqlite("query", "SELECT $select FROM 'countries';");
    }
} else {
    $countries = sqlite("query", "SELECT $select FROM 'countries';");
}

header("Content-Type: application/json; charset=utf-8");
//echo json_encode(["countries" => $countries], JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
echo json_encode($countries, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);

/* uncomment this to return xml
 header("Content-Type: application/xml; charset=utf-8");
echo arrayToXML($countries, new SimpleXMLElement('<countries/>'), 'country');
*/

?>
