<?php

namespace conference\Controllers;

class ReviewReview extends ALoggedController
{

    public function __construct($twig, $pdo)
    {
        parent::__construct($twig, $pdo);
        $this->min_rights = 2;
        $this->view_data["title"] = "Rozhodnout recenze";
    }


    public function do_stuff()
    {
        $this->init();

        if($this->VIEW){
            echo $this->twig->render($this->VIEW,$this->view_data);
            return;
        }

        $this->process_form();

        $this->VIEW = "ReviewReview.view.twig";
        $clanky = $this->pdo->arenrev();
        //var_dump($this->view_data["clanky"]);

        //additional info for all articles: author's name, article's reviews
        foreach ($clanky as $idx => $clanek){
                $recenze = $this->pdo->arallrevs_user_info($clanek["id_clanek"]);

                $complete = true;
                foreach($recenze as $rec){
                    $complete = $complete && ($rec["hodnoceni"] != null);
                }

                if(!$complete){
                    //not all reviewers have reviewed -> yeet and skip this revision
                    unset($clanky[$idx]);
                    continue;
                }

                $autor = $this->pdo->get_article_author($clanek["id_clanek"])[0];
                $autor = $autor["jmeno"]." ".$autor["prijmeni"];

                //var_dump($clanek);

                $clanky[$idx]["autor_jmeno"] = $autor;
                $clanky[$idx]["recenzenti"] = $recenze;
            }

        $this->view_data["clanky"] = $clanky;


        echo $this->twig->render($this->VIEW,$this->view_data);
    }

    private function process_form(){
        if(isset( $_POST["schvalit"] )){
            $this->pdo->aracc($_POST["ar_id"]);
        }
        if(isset( $_POST["zamitnout"] )){
            $this->pdo->ardecl( $_POST["ar_id"] );
        }
        if(isset( $_POST["vratit"] )){
            $this->pdo->delrevs($_POST["ar_id"]);
        }
    }

}