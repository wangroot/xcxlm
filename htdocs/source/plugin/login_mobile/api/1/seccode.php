<?php
if (!defined('IN_MOBILE_API')) {
    exit('Access Denied');
}
if (isset($_GET["check"])) {
    if (DzSecCode::check()) {
        die("success");
    } else {
        die("fail");
    }
}

$code = DzSecCode::mkcode(4,true);
DzSecCode::display2($code);
?>
