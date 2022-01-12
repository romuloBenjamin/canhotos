<?php
class Main extends Conexao
{
    private $db;
    var $sql;

    public function __construct()
    {
        $this->db = new Conexao();
        $this->db = $this->db;
        $this->db->exit_conexao();
        $this->db->open_conexao();
    }

    public function executeQuery()
    {
        if (!is_null($this->db)) {
            $resultSet = mysqli_query($this->db->get_conexao(), $this->sql) or die(mysqli_connect_error($this->db->get_conexao()));
            return $resultSet;
        }
    }

    public function server_conexao()
    {
        return $this->db->exit_conexao();
    }
}
