<?php
$string = "Here! is some text, and numbers 12345, and symbols  !¬`$%^&*()_+=-;'@:/.,<>?\"";
$new_string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
echo $new_string
?>