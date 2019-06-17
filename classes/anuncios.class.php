<?php

class anuncios {

    public function getMeusAnuncios() {
        global $pdo;
        $array = array();
        $sql = $pdo->prepare(
            "SELECT *,
                (SELECT 
                    anuncios_imagens.link
                FROM   
                    anuncios_imagens
                WHERE 
                    anuncios_imagens.id_anuncio = anuncios.id
                LIMIT  1) 
            AS 
                link
            FROM   
                anuncios
            WHERE 
                id_usuario = :id_usuario ");
        $sql->bindValue(":id_usuario", $_SESSION['cLogin']);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $array = $sql->fetchAll();
        }

        return $array;
    }

    public function getAnuncio($id){
        $array = array();
        global $pdo;

        $sql = $pdo->prepare("SELECT * FROM anuncios WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();

        if($sql->rowCount() > 0) {
            $array = $sql->fetch();
            $array['fotos'] = array();

            $sql = $pdo->prepare("SELECT id, link FROM anuncios_imagens WHERE id_anuncio = :id_anuncio");
            $sql->bindValue(":id_anuncio", $id);
            $sql->execute();

            if($sql->rowCount() > 0) {
                $array['fotos'] = $sql->fetchAll();
            }
        }

        return $array;

    }

    public function addAnuncio($titulo, $categoria, $valor, $descricao, $estado) {
        global $pdo;

        $sql = $pdo->prepare(
            "INSERT INTO 
                anuncios 
            SET 
                titulo = :titulo,
                id_usuario = :id_usuario, 
                id_categoria = :id_categoria, 
                descricao = :descricao, 
                valor = :valor, 
                estado = :estado");
        $sql->bindValue(":titulo", $titulo);
        $sql->bindValue(":id_usuario", $_SESSION['cLogin']);
        $sql->bindValue(":id_categoria", $categoria);
        $sql->bindValue(":descricao", $descricao);
        $sql->bindValue(":valor", $valor);
        $sql->bindValue(":estado", $estado);
        $sql->execute();
    }
    
    public function editAnuncio($titulo, $categoria, $valor, $descricao, $estado, $fotos, $id) {
        global $pdo;

        $sql = $pdo->prepare(
            "UPDATE 
                anuncios 
            SET 
                titulo = :titulo,
                id_usuario = :id_usuario, 
                id_categoria = :id_categoria, 
                descricao = :descricao, 
                valor = :valor, 
                estado = :estado
            WHERE
                id = :id
            AND
                id_usuario = :id_usuario");
        $sql->bindValue(":titulo", $titulo);
        $sql->bindValue(":id_usuario", $_SESSION['cLogin']);
        $sql->bindValue(":id_categoria", $categoria);
        $sql->bindValue(":descricao", $descricao);
        $sql->bindValue(":valor", $valor);
        $sql->bindValue(":estado", $estado);
        $sql->bindValue(":id", $id);
        $sql->bindValue(":id_usuario", $_SESSION['cLogin']);
        $sql->execute();


            if(count($fotos) > 0) {
                for ($q=0; $q < count($fotos['tmp_name']); $q++) { 
                        $tipo = $fotos['type'] [$q];

                        if(in_array($tipo, array('image/jpeg', 'image/png'))){
                            $tmpname = md5(time().rand(0,9999)).'.jpg';
                            move_uploaded_file($fotos['tmp_name'][$q], 'assets/images/anuncios/'.$tmpname);

                            list($width_orig, $height_orig) = getimagesize('assets/images/anuncios/'.$tmpname);
                            $ratio = $width_orig/$height_orig;

                            $width = 500;
                            $height = 500;

                            if($width/$height > $ratio) {
                                $width = $height*$ratio;
                            } else {
                                $height = $width/$ratio;
                            }
                            
                            $img = imagecreatetruecolor($width, $height);
                            if($tipo == 'image/jpeg') {
                                $origi = imagecreatefromjpeg('assets/images/anuncios/'.$tmpname);
                            } elseif($tipo == 'image/png') {
                                $origi = imagecreatefrompng('assets/images/anuncios/'.$tmpname);
                            }

                            imagecopyresampled($img, $origi, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                            imagejpeg($img, 'assets/images/anuncios/'.$tmpname, 80);

                            $sql = $pdo->prepare("INSERT INTO anuncios_imagens SET id_anuncio = :id_anuncio, link = :link");
                            $sql->bindValue(":id_anuncio", $id);
                            $sql->bindValue(":link", $tmpname);
                            $sql->execute();


                        }
                }

            }

    }

    public function excluirAnuncio($id) {
        global $pdo;

        $sql = $pdo->prepare("DELETE FROM anuncios_imagens WHERE id_anuncio = :id_anuncio");
        $sql->bindValue(":id_anuncios", $id);
        $sql->execute();

        $sql = $pdo->prepare("DELETE FROM anuncios WHERE id = :id AND id_usuario = :id_usuario");
        $sql->bindValue(":id", $id);
        $sql->bindValue(":id_usuario", $_SESSION['cLogin']);
        $sql->execute();

    }

    public function excluirFoto($id) {
        global $pdo;
        $id_anuncio = 0;

        $sql = $pdo->prepare("SELECT * FROM anuncios_imagens WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();

        if($sql->rowCount() > 0 ){
            $row = $sql->fetch();
            $id_anuncio = $row['id_anuncio'];
            if (is_file("assets/images/anuncios/".$row['link'])){
                unlink("assets/images/anuncios/".$row['link']);
            }
        }


        $sql = $pdo->prepare("DELETE FROM anuncios_imagens WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();


        return $id_anuncio;


    }
   
}