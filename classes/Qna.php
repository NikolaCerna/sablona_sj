<?php
namespace otazkyodpovede;
error_reporting(E_ALL);
ini_set('display_errors', "On");

define('__ROOT__', dirname(dirname(__FILE__))); // presuň sem, ak nie je ešte pred require
require_once(__ROOT__.'/classes/Database.php');
use Database;

class QnA extends Database {
    protected $connection;

    public function __construct() {
        $this->connect();
        $this->connection = $this->getConnection();
    }

    public function insertQnA() {
        try {
            $data = json_decode(file_get_contents(__ROOT__.'/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            $this->connection->beginTransaction(); // oprava preklepu: connnection -> connection

            $sql = "SELECT COUNT(*) FROM qna WHERE otazka = :otazka";
            $statement = $this->connection->prepare($sql);

            $insertSql = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)";
            $insertStatement = $this->connection->prepare($insertSql);

            for ($i = 0; $i < count($otazky); $i++) {
                $statement->bindParam(':otazka', $otazky[$i]);
                $statement->execute();
                $count = $statement->fetchColumn();

                if ($count == 0) {
                    $insertStatement->bindParam(':otazka', $otazky[$i]);
                    $insertStatement->bindParam(':odpoved', $odpovede[$i]);
                    $insertStatement->execute();
                }
            }

            $this->connection->commit();
            http_response_code(200);
        } catch (Exception $e) {
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            http_response_code(500);
            $this->connection->rollback();
        }
    }

    public function getQnA() {
        try {
            $sql = "SELECT otazka, odpoved FROM qna";
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            http_response_code(200);
            return $statement->fetchAll();
        } catch (Exception $e) {
            http_response_code(500);
            echo "Chyba pri načítaní údajov: " . $e->getMessage();
            return [];
        }
    }
}
