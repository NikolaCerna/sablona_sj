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

<?php
function preparePortfolio(int $numberOfRows = 2, int $numberOfCols = 4): array{
    $portfolio = [];
    $colIndex = 1;
    for ($i = 1; $i <= $numberOfRows; $i++) {
        for($j = 1; $j <= $numberOfCols; $j++) {
            $portfolio[$i][$j] = $colIndex;
            $colIndex++;
        }
    }
    return $portfolio;
}
?>
<?php
function finishPortfolio() {
    $json = file_get_contents("data/datas.json");
    $data = json_decode($json, true);
    $portfolio = preparePortfolio();
    foreach ($portfolio as $row) {
        echo '<div class="row">';
        foreach ($row as $index) {
            $title = $data["portfolio"]["portfolio" . $index . ".jpg"]["nadpis"];
            $url = $data["portfolio"]["portfolio" . $index . ".jpg"]["url"];
            echo '<div class="col-25">';
            echo '<a href="' . $url . '" target="_blank" style="display: block; height: 100%; text-decoration: none;">';
            echo '<div class="portfolio text-white text-center" id="portfolio-' . $index . '">';
            echo $title;
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
    }
}
?>

