<?php
    function title(){
        $judul ="Aplikasi E-Dokter --)(*!!@#$%";
        $judul = preg_replace("[^A-Za-z0-9_\-\./,|]"," ",$judul);
        $judul = str_replace(array('.','-','/',',')," ",$judul);
        $judul = trim($judul);
        echo "$judul";	
    }

    function cekSessiAdmin() {
        if (isset($_SESSION['ses_dokter'])) {
            return true;
        } else {
            return false;
        }
     }

    function PasienAktif() {
        if (cekSessiAdmin()) {
            return $_SESSION['ses_dokter'];
        }
     }

    function isPengunjung() {
        if (cekSessiAdmin()) {
            return false;
        } else {
            return true;
        }
     }	


    function formProtek() {
        $aksi=isset($_GET['act'])?$_GET['act']:NULL;
        if (!cekSessiAdmin()) {
            $form = array ('HomeUser','Pasien');
            foreach ($form as $page) {
                if ($aksi==$page) {
                    echo "<META HTTP-EQUIV = 'Refresh' Content = '0; URL = ?act=Home'>";
                    exit;
                    break;
                }
            }
        }	
    }

    function actionPages() {
        $aksi=isset($_REQUEST['act'])?$_REQUEST['act']:NULL;
        formProtek();
        switch ($aksi) {
            case "Pasien"                                  : include_once("pages/listpasien.php"); break;
            case "HomeUser"                                : include_once("pages/listhome.php"); break;
            default                                        : include_once("pages/listhome.php");
        }   
    }
 
?>
