<?php
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function _cleaninjections($data) {
    return str_replace(array("\n", "\r", "%0a", "%0d"), '', $data);
}
?>
