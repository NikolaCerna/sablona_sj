<?php
namespace otazkyodpovede;

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/config.php');
use PDO;
class QnA{
    private $conn;
    public function __construct() {
        $this->connect();
    }
    private function connect() {
        $config = DATABASE;

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        try {
            $this->conn = new PDO('mysql:host=' . $config['HOST'] . ';dbname=' .
                $config['DBNAME'] . ';port=' . $config['PORT'], $config['USER_NAME'],
                $config['PASSWORD'], $options);
        } catch (PDOException $e) {
            die("Chyba pripojenia: " . $e->getMessage());
        }
    }

    public function insertQnA() {
        try {
            // Načítanie JSON súboru
            $data = json_decode(file_get_contents(__ROOT__.'/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            // Vloženie otázok a odpovedí v rámci transakcie
            $this->conn->beginTransaction();

            $sql = "SELECT COUNT(*) FROM qna WHERE otazka = :otazka"; // zisti, ci otazka existuje cize 1/0
            $statement = $this->conn->prepare($sql);

            $insertSql = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)";
            $insertStatement = $this->conn->prepare($insertSql);

            for ($i = 0; $i < count($otazky); $i++) {
                // skontroluje, ci otatka v databaze uz existuje alebo nie
                $statement->bindParam(':otazka', $otazky[$i]);
                $statement->execute();
                $count = $statement->fetchColumn();

                // ak neexistuje ju vložíme
                if ($count == 0) {
                    $insertStatement->bindParam(':otazka', $otazky[$i]);
                    $insertStatement->bindParam(':odpoved', $odpovede[$i]);
                    $insertStatement->execute();
                }
            }

            $this->conn->commit();
            // echo "Dáta boli vložené";
        } catch (Exception $e) {
            // Zobrazenie chybového hlásenia
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            $this->conn->rollback(); // Vrátenie späť zmien v prípade chyby
        } // finally {
            // Uzatvorenie spojenia
        // $this->conn = null;
        // }
    }

    public function getQnA() { //metoda na ziskanie otazoka  dopovedi z databzy
        try {
            $insertSql = "SELECT otazka, odpoved FROM qna";// tahame otazky a odpovede z databazy
            $insertStatementt = $this->conn->prepare($insertSql);
            $insertStatementt->execute();
            return  $insertStatementt->fetchAll();//vyberieme VSETKY (je to pole otazok a odpovedi)
        } catch (Exception $e) {
            echo "Chyba pri načítaní údajov: " . $e->getMessage();
            return [];
        }
    }
}