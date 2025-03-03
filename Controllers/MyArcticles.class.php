<?php

namespace conference\Controllers;

use conference\Models\Session_Model;
use Couchbase\ViewQuery;

class MyArcticles extends ALoggedController
{
    const EDIT_BUT = "edit_ok";
    const DELETE_BUT = "ar_delete";
    const CANCEL_BUT = "edit_cancel";

    public function __construct($twig, $pdo)
    {
        parent::__construct($twig, $pdo);
        $this->min_rights = 4;
        $this->view_data["title"] = "Mé články";
    }

    public function do_stuff()
    {
        $this->process_form();

        $this->init();

        if(!$this->VIEW){
            $this->VIEW = "MyArticles.view.twig";
            $this->view_data["clanky"] = $this->pdo->articles_by_author($this->session->get(Session_Model::USER_ID));
            //var_dump($this->view_data["clanky"]);
        }

        echo $this->twig->render($this->VIEW,$this->view_data);
    }

    private function process_form(){
        if(isset( $_POST[self::EDIT_BUT] )){
            $this->pdo->update_ardesc($_POST["ar_id"], $_POST["popis_edit"]);
        }
        else if(isset( $_POST[self::DELETE_BUT] )){
            $filename = $this->pdo->get_article($_POST["ar_id"])[0]["nazev_souboru"];
            if($this->pdo->deletArticle($_POST["ar_id"]))
                //delete the associated file
                unlink(ARTICLES_DIR.$filename);
        }
        else if(isset( $_POST[self::CANCEL_BUT] )){
            //just reload page
        }
    }
}