<?php
class Conexao
{
    private $password;
    private $host;
    private $user;
    private $table;
    private $db;

    public function __construct()
    {
        $this->host = CONEXAO_PRINCIPAL["host"];
        $this->user = CONEXAO_PRINCIPAL["usuario"];
        $this->table = CONEXAO_PRINCIPAL["database"];
        $this->password = CONEXAO_PRINCIPAL["senha"];
    }

    public function get_conexao()
    {
        $myConn = mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->table
        );
        mysqli_set_charset($myConn, CONEXAO_PRINCIPAL["charset"]);
        if (mysqli_connect_errno($myConn, CONEXAO_PRINCIPAL["charset"])) {
            $sqlError = json_encode(array("status" => 0, "msn" => mysqli_connect_error()));
            $this->db = $sqlError;
            exit();
        } else {
            $this->db = $myConn;
        }
        return $this->db;
    }
    /*OPEN CONEXÃO AT CLASS*/
    public function open_conexao()
    {
        return $this->db;
    }
    public function exit_conexao()
    {
        $exit = new Conexao();
        $exit->db = $this->db;
        if (!is_null($exit->db)) {
            return mysqli_close($exit->db);
        }
    }
}
