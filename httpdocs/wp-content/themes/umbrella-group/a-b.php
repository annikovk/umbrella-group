<?php
/* Template Name: A-B
 * 
 * 
 *
 * 
 */
?>
<?php
$Pages = array(
    'https://taxlab.ru/promo/',
    'https://taxlab.ru/main/',
);
$MaxRandom = getrandmax();
$RandomValue = intval(rand(0, count($Pages) - 1));
header('Location: ' . $Pages[$RandomValue]);

?>