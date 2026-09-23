<?php

class m_user
{
    private $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function tampilUser()
    {
        $query = mysqli_query(
            $this->koneksi,
            "SELECT * FROM tb_user
             ORDER BY id_user DESC"
        );

        return $query;
    }

    public function tambahUser(
        $nama,
        $username,
        $password,
        $role,
        $no_telp
    ) {

        $password = password_hash(
            $password,
            PASSWORD_BCRYPT
        );

        $nama = mysqli_real_escape_string(
            $this->koneksi,
            $nama
        );

        $username = mysqli_real_escape_string(
            $this->koneksi,
            $username
        );

        $role = mysqli_real_escape_string(
            $this->koneksi,
            $role
        );

        $no_telp = mysqli_real_escape_string(
            $this->koneksi,
            $no_telp
        );

        $query = mysqli_query(
            $this->koneksi,
            "INSERT INTO tb_user
            (
                nama,
                username,    
                password,
                role,
                no_telp,
                status_aktif
            )
            VALUES
            (
                '$nama',
                '$username',
                '$password',
                '$role',
                '$no_telp',
                1
            )"
        );

        return $query;
    }

    public function registerUser(
        $nama,
        $username,
        $password,
        $no_telp
    ) {

        $password = password_hash(
            $password,
            PASSWORD_BCRYPT
        );

        $nama = mysqli_real_escape_string(
            $this->koneksi,
            $nama
        );

        $username = mysqli_real_escape_string(
            $this->koneksi,
            $username
        );

        $no_telp = mysqli_real_escape_string(
            $this->koneksi,
            $no_telp
        );

        $query = mysqli_query(
            $this->koneksi,
            "INSERT INTO tb_user
            (
                nama,
                username,
                password,
                role,
                no_telp,
                status_aktif
            )
            VALUES
            (
                '$nama',
                '$username',
                '$password',
                'peminjam',
                '$no_telp',
                1
            )"
        );

        return $query;
    }

    public function getUserById($id)
    {
        $id = (int)$id;

        $query = mysqli_query(
            $this->koneksi,
            "SELECT * FROM tb_user
             WHERE id_user = $id
             LIMIT 1"
        );

        return mysqli_fetch_assoc($query);
    }

    public function editUser(
        $id,
        $nama,
        $username,
        $role,
        $no_telp
    ) {

        $id = (int)$id;

        $nama = mysqli_real_escape_string(
            $this->koneksi,
            $nama
        );

        $username = mysqli_real_escape_string(
            $this->koneksi,
            $username
        );

        $role = mysqli_real_escape_string(
            $this->koneksi,
            $role
        );

        $no_telp = mysqli_real_escape_string(
            $this->koneksi,
            $no_telp
        );

        $query = mysqli_query(
            $this->koneksi,
            "UPDATE tb_user SET
                nama = '$nama',
                username = '$username',
                role = '$role',
                no_telp = '$no_telp'
             WHERE id_user = $id"
        );

        return $query;
    }

    public function editPassword($id, $password)
    {
        $id = (int)$id;

        $password = password_hash(
            $password,
            PASSWORD_BCRYPT
        );

        $query = mysqli_query(
            $this->koneksi,
            "UPDATE tb_user SET
                password = '$password'
             WHERE id_user = $id"
        );

        return $query;
    }

    public function hapusUser($id)
    {
        $id = (int)$id;

        $query = mysqli_query(
            $this->koneksi,
            "DELETE FROM tb_user
             WHERE id_user = $id"
        );

        return $query;
    }
}
?>