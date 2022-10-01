<?php

require_once 'classes' . DIRECTORY_SEPARATOR . 'root.php';

$s = new faqans;
print_r($s->getData("https://faq-ans.com/en/Q%26A/page=c4bfc30cc22ec52bc6a277f677cbb34f"));
?>