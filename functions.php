<?php
function generateSlides($dir) {
    $files = glob($dir . "/*.jpg");
    $json = file_get_contents("data/datas.json");
    $data = json_decode($json, true);
    $text = $data["banner_text"];

    foreach ($files as $file) {
        $filename = basename($file);
        if (isset($text[$filename])) {
            echo '<div class="slide fade">';
            echo '<a href="' . ($text[$filename]["url"]) . '" target="_blank">';
            echo '<img src="' . $file . '">';
            echo '</a>';
            echo '<div class="slide-text">';
            echo ($text[$filename]["nadpis"]);
            echo '</div>';
            echo '</div>';
        }
    }
}
?>