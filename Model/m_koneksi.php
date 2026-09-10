<?php
class koneksi {
    private $host = "localhost";
    private $username = "root";
    private $pass = "";
    private $db = "pinjamin_electronic";

    public $koneksi;

    function __construct()
    {
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->pass, $this->db);

        if (!$this->koneksi) {
            die("Koneksi ke database gagal: " . mysqli_connect_error());
        }
    }
}
?>