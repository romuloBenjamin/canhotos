<?php
/*---------------------------------------------->DIRETORIOS<----------------------------------------------*/
if (!isset($core)) define("DIR_PATH", "./cnt-files/");
if (isset($core)) define("DIR_PATH", "../../../");
define("REPOS", "\\\\172.16.0.19\\Desenvolvimento\\");
define("LOCAL", "local");
//define("LOCAL", "web");
/*------------------------------------------------>FORMATS<-----------------------------------------------*/
define(
    "IMAGES_FORMATS",
    array(
        "jpeg",
        "jpg",
        "png",
        "tif",
        "tiff",
        "pdf",
        "txt"
    )
);
/*----------------------------------------------->CONEXOES<-----------------------------------------------*/
define(
    "CONEXAO_PRINCIPAL",
    array(
        "usuario" => "root",
        "senha" => "",
        "database" => "wwsale_intranet_unificado",
        "host" => "127.0.0.1",
        "charset" => "utf8"
    )
);
define(
    "CERTIFICADOS",
    "MTAyNjI3Mjc="
);
/*----------------------------------------------->REMOVES<-----------------------------------------------*/
define(
    "REMOVES",
    array(
        ".", "+", "-", "/", "*", "=", "[", "]", "{", "}", "|", "?", "~", "^", "´", "`", ">", "<", ":", ";", ",", "\\", "!", "#", "%", "¨", "&", "(", ")", "_", "\n"
    )
);
define(
    "REMOVE_SPECIALS",
    array("—", ".", "+", "-", "/", "*", "=", "[", "]", "{", "}", "|", "?", "~", "^", "´", "`", ">", "<", ":", ";", ",", "\\", "!", "#", "%", "¨", "&", "(", ")", "_", "\n", "”", "|")
);
define(
    "REMOVE_ACENTOS",
    array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ñ", "ý")
);
define(
    "POSSIVEIS",
    array("q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "j", "k", "l", "ç", "z", "x", "c", "v", "b", "n", "m", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0")
);
define(
    "ONLY_NUMBERS",
    array("q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "j", "k", "l", "ç", "z", "x", "c", "v", "b", "n", "m", "´", "[", "~", "]", ",", ".", ";", "/", "`", "{", "}", "?", ":", ">", "<", "^", "ª", "º", "°", "|", "\'", "'", "!", "@", "#", "$", "%", "¨", "&", "*", "(", ")", "_", "+", "-", "=", "§", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ñ", "ý", ".", "-", "_", "/", "'", " ")
);
/*--------------------------------------------->CNPJ RANGES<---------------------------------------------*/
/*SALES*/
define(
    "SALES_PRIMARY_KNOW_NAMES",
    array("SALES", "SATLES", "BALES", "SATES", "SALÉES", "SALEES")
);
define(
    "SALES_EQUIP",
    array(
        "ini" => 10000000000000,
        "end" => 18999999999999,
        "CNPJ" => "10290557000168",
        "PASS" => "10262727",
        "SITE" => "SALES EQUIP",
        "SECONDARY_KNOW_NAMES" => array("EQUIP", "BOUIP", "EOUIP", "KQUIP", "CQUIP", "UOUIP", "EGUIP")
    )
);
define(
    "SALES_IND",
    array(
        "ini" => 60000000000000,
        "end" => 69999999999999,
        "CNPJ" => "66826918000100",
        "PASS" => "10262727",
        "SITE" => "SALES IND",
        "SECONDARY_KNOW_NAMES" => array("IND", "INO")
    )
);
/*SANDALO*/
define(
    "SANDALO_PRIMARY_KNOW_NAMES",
    array("SANDALO", "BANDALO", "BANOALO")
);
define(
    "COMERCIAL_SANDALO",
    array(
        "ini" => 20000000000000,
        "end" => 27999999999999,
        "CNPJ" => "21823607000141",
        "PASS" => "10262727",
        "SITE" => "SANDALO COMER",
        "SECONDARY_KNOW_NAMES" => array("COMER", "OOMER", "COMEB")
    )
);
define(
    "SANDALO_EQUIP",
    array(
        "ini" => 30000000000000,
        "end" => 34999999999999,
        "CNPJ" => "30379727000192",
        "PASS" => "10262727",
        "SITE" => "SANDALO EQUIP",
        "SECONDARY_KNOW_NAMES" => array("EQUIP", "BOUIP", "EOUIP", "KQUIP", "CQUIP", "UOUIP", "EGUIP")
    )
);
/*DONA*/
define(
    "DONA_PRIMARY_KNOW_NAMES",
    array("DONA", "OONA")
);
define(
    "DONA_DESCARTAVEIS",
    array(
        "ini" => 35000000000000,
        "end" => 49999999999999,
        "CNPJ" => "35765246000139",
        "PASS" => "10262727",
        "SITE" => "DONA DESCARTAVE"
    )
);
/*ARRAY EMPRESAS*/
define(
    "EMPRESAS",
    array(SALES_EQUIP, COMERCIAL_SANDALO, SALES_IND, SANDALO_EQUIP, DONA_DESCARTAVEIS)
);
/*-------------------------------------------->PHPMAILER<--------------------------------------------*/
define(
    "MAILER",
    array(
        "host" => "smtp.gmail.com",
        "username" => "sales.cleaner.externo3@gmail.com",
        "password" => "Q2xlQG5uZXJTYWxlcw==",
        "port" => "465"
    )
);
