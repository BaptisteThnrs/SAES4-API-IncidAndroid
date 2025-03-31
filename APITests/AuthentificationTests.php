<?php

namespace APITests;

use API\Authentification;


use PHPUnit\Framework\TestCase;

class AuthentificationTests extends TestCase {
    private $pdoMock;
    private $authMock;

    protected function setUp(): void {
        // Mock de PDO
        $this->pdoMock = $this->getMockBuilder(PDO::class)
                      ->disableOriginalConstructor()
                      ->getMock();

        
        // Création d'un mock de la classe Authentification avec PDO injecté
        $this->authMock = $this->getMockBuilder(Authentification::class)
        ->getMock();




        // Injection du mock PDO dans Authentification
        $reflection = new ReflectionClass($this->authMock);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue($this->authMock, $this->pdoMock);
    }

    public function testVerifLoginPassword_Success() {
        $stmtMock = $this->createMock(PDOStatement::class);
        
        $this->pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(['id_login' => 1, 'id_employe' => 123]);

        // Simulation de sendJSON
        $this->authMock->expects($this->once())
                       ->method('sendJSON')
                       ->with($this->callback(function ($data) {
                           return isset($data['APIKEY']) && $data['id_employe'] === 123;
                       }));

        $this->authMock->verifLoginPassword("testUser", "testPassword");
    }

    public function testVerifLoginPassword_Failure() {
        $stmtMock = $this->createMock(PDOStatement::class);
        
        $this->pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false); // Aucun utilisateur trouvé

        $this->authMock->expects($this->once())
                       ->method('sendJSON')
                       ->with(['Statut' => 'KO', 'message' => 'Logins incorrects.'], 401);

        $this->authMock->verifLoginPassword("wrongUser", "wrongPassword");
    }

    public function testAuthentification_MissingAPIKey() {
        unset($_SERVER["HTTP_APIKEY"]);

        $this->authMock->expects($this->once())
                       ->method('sendError')
                       ->with("Authentification nécessaire par APIKEY.", 401);

        $this->authMock->authentification();
    }

    public function testAuthentification_InvalidAPIKey() {
        $_SERVER["HTTP_APIKEY"] = "invalidKey";

        $stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false); // Clé invalide

        $this->authMock->expects($this->once())
                       ->method('sendError')
                       ->with("APIKEY invalide.", 403);

        $this->authMock->authentification();
    }
}
?>
