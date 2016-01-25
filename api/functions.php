<?php

include("configuration.php");

//$dbcfg = $cfg['db_' . $environment];
//$server = $dbcfg['hostname'];
//$db_user = $dbcfg['username'];
//$db_pw = $dbcfg['password'];
//$db = $dbcfg['database'];
//die;
mysql_connect($server, $db_user, $db_pw);
mysql_select_db($db) or die("Datenbank konnte nicht gefunden werden.");

function unpack_zip($path) {
    $response = new Response;
    $zip = new ZipArchive;
    $target = str_replace('.zip', '', $path);
    if ($zip->open($path) === TRUE) {
        $zip->extractTo($target);
        $zip->close();
        //echo 'ok';
    } else {
        $response->status = 500;
        $response->message = 'Unable to unpack ZIP file';
        $response->json($response);
    }
}

function unpack_tar($path) {
    $response = new Response;
    //echo "Unpack.\n";
    //echo "path:".$path;
    /*
      system("mkdir archiv/name");
      system("cp upload/FosCam9805.tar.gz archiv/name/FosCam9805.tar.gz");
      system("mkdir archiv/name/temp");
      //system("tar -xvzf archiv/name/FosCam9805.tar.gz -C archiv/name/temp/");
     */

    // This input should be from somewhere else, hard-coded in this example
    $file_name = $path;

    // Raising this value may increase performance
    $buffer_size = 4096; // read 4kb at a time
    $out_file_name = str_replace('.gz', '', $file_name);

    // Open our files (in binary mode)
    $file = gzopen($file_name, 'rb');
    $out_file = fopen($out_file_name, 'wb');

    // Keep repeating until the end of the input file
    while (!gzeof($file)) {
        // Read buffer-size bytes
        // Both fwrite and gzread and binary-safe
        fwrite($out_file, gzread($file, $buffer_size));
    }
    // Files are done, close files
    fclose($out_file);
    gzclose($file);

    $phar_data = new PharData($out_file_name);
    $phar_data->extractTo(str_replace(".tar", "", $out_file_name));

    //unlink($out_file_name);
    unset($phar_data);
    Phar::unlinkArchive($out_file_name);

    //echo "finish unpacking archive<br>";
}

function read_json($path, $user_id, $filetype, $wrong_folder) {
    $response = new Response;
    if ($filetype == "gz") {
        $path = str_replace(".tar.gz", "", $path);
    }
    if ($filetype == "zip") {
        $path = str_replace(".zip", "", $path);
    }
    // Heck correct package path
    if (is_dir($path . '/' . $wrong_folder)) {
        $response->status = 500;
        $response->message = 'Package file has an invalid path. Need to repack file again';
        $response->json($response);
    }
    //var_dump($path);
    $targetdir = $path . "/module.json";
    if (!file_exists($targetdir)) {
        $response->status = 500;
        $response->message = 'Unable to read module.json file';
        $response->json($response);
    }

    $jsonfile = @file_get_contents("$targetdir");
    $jsonarray = json_decode($jsonfile, true);
    $category = $jsonarray['category'];
    $author = $jsonarray['author'];
    $homepage = $jsonarray['homepage'];
    $version = $jsonarray['version'];
    $maturity = $jsonarray['maturity'];
    $moduleName = $jsonarray['moduleName'];
    $latest = date('Ymdhis');
    $lang_target = $path . "/lang";

    if (file_exists($lang_target . "/de.json")) {
        //echo "DE: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/de.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        $de_desc = $jsonarray_lang['m_descr'];
        $de_title = $jsonarray_lang['m_title'];
    } else {
        //echo "DE: empty";
    }

    if (file_exists($lang_target . "/en.json")) {
        //echo "en: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/en.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        if (!$jsonarray_lang['m_descr'] || !$jsonarray_lang['m_title']) {
            $response->status = 500;
            $response->message = 'Missing m_title or m_descr in lang/en.json';
            $response->json($response);
        }
        $en_desc = $jsonarray_lang['m_descr'];
        $en_title = $jsonarray_lang['m_title'];
    } else {
        $response->status = 500;
        $response->message = 'Missing file: lang/en.json';
        $response->json($response);
    }

    if (file_exists($lang_target . "/ru.json")) {
        //echo "ru: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/ru.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        $ru_desc = $jsonarray_lang['m_descr'];
        $ru_title = $jsonarray_lang['m_title'];
    } else {

        //echo "ru: empty";
    }
    if ($en_title == "" || $en_desc == '') {
        $response->status = 500;
        $response->message = 'Empty m_title or m_descr in lang/en.json';
        $response->json($response);
    }

//    if ($en_title == "") {
//        $jsonfile = file_get_contents("$targetdir");
//        $jsonarray = json_decode($jsonfile, true);
//        $en_title = $jsonarray['defaults']['title'];
//    }
    $copy = check_available($user_id, $en_title, $path, $moduleName, $filetype);

    if ($copy == "ok") {
        $jsonfile = file_get_contents("$targetdir");
        $jsonarray = json_decode($jsonfile, true);

        $response = store_json($jsonarray, $targetdir, $path, $user_id, $filetype);
    } else {
        return "error";
    }
}

function check_available($user_id, $en_title, $path, $moduleName, $filetype) {
    $response = new Response;
    include("configuration.php");

    mysql_connect($server, $db_user, $db_pw);
    mysql_select_db($db) or die("Datenbank konnte nicht gefunden werden.");

    $sql = "SELECT * FROM modules WHERE title = '" . $en_title . "' AND user_id = " . $user_id;
    $result = mysql_query($sql);
    $count = 0;
    $current_module_id = 0;
    while ($row = mysql_fetch_object($result)) {
        $count++;
        $current_module_id = $row->id;
    }
    if ($count > 0) {
        //exist
        //is new
        //Copy archive
        if ($filetype == "gz") {
            $file = $path . ".tar.gz";
            $newfile = 'modules/' . $moduleName . '.tar.gz';
        }
        if ($filetype == "zip") {
            $file = $path . ".zip";
            $newfile = 'modules/' . $moduleName . '.zip';
        }


        $copy_tar = "";
        if (!copy($file, $newfile)) {/* echo "copy $file schlug fehl...\n"; */
            $response->status = 500;
            $response->message = 'Unable to copy file: ' . $file;
            $response->json($response);
        } else {
            $copy_tar = "ok";
        }
        //Copy correspondening image
        if (file_exists($path . "/htdocs/icon.png")) {
            $file = $path . "/htdocs/icon.png";
            $newfile = 'modules/' . $moduleName . '.png';

            if (!copy($file, $newfile)) {
                echo "copy $file schlug fehl...\n";
            }
            //Copy correspondening image
        } else {
            //no Image -> later
        }

        if ($copy_tar == "ok") {
            return $copy_tar;
        }
    } else {
        //is new
        //Copy archive
        if ($filetype == "gz") {
            $file = $path . ".tar.gz";
            $newfile = 'modules/' . $moduleName . '.tar.gz';
        }
        if ($filetype == "zip") {
            $file = $path . ".zip";
            $newfile = 'modules/' . $moduleName . '.zip';
        }
        $copy_tar = "";
        if (!copy($file, $newfile)) {
            $response->status = 500;
            $response->message = 'Unable to copy file: ' . $file;
            $response->json($response);
        } else {
            $copy_tar = "ok";
        }
        //Copy correspondening image
        if (file_exists($path . "/htdocs/icon.png")) {
            $file = $path . "/htdocs/icon.png";
            $newfile = 'modules/' . $moduleName . '.png';

            if (!copy($file, $newfile)) {
                echo "copy $file schlug fehl...\n";
            }
            //Copy correspondening image
        } else {
            //no Image -> later
        }

        if ($copy_tar == "ok") {
            return $copy_tar;
        }
    }
}

function store_json($jsonarray, $targetdir, $path, $user_id, $filetype) {

    $response = new Response;
    $category = $jsonarray['category'];
    //$author = $jsonarray['author'];
    $author = iconv(mb_detect_encoding($jsonarray['author'], mb_detect_order(), true), 'UTF-8', $jsonarray['author']);
    $homepage = $jsonarray['homepage'];
    $version = $jsonarray['version'];
    $maturity = $jsonarray['maturity'];
    $moduleName = $jsonarray['moduleName'];

    $lang_target = $path . "/lang";

    if (file_exists($lang_target . "/de.json")) {
        //echo "DE: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/de.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        //$de_desc = $jsonarray_lang['m_descr'];
        //$de_title = $jsonarray_lang['m_title'];
        $de_desc = iconv(mb_detect_encoding($jsonarray_lang['m_descr'], mb_detect_order(), true), 'UTF-8', $jsonarray_lang['m_descr']);
        $de_title = iconv(mb_detect_encoding($en_title = $jsonarray_lang['m_title'], mb_detect_order(), true), 'UTF-8', $en_title = $jsonarray_lang['m_title']);
    } else {
        //echo "DE: empty";
    }

    if (file_exists($lang_target . "/en.json")) {
        //echo "en: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/en.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        //$en_desc = $jsonarray_lang['m_descr'];
        //$en_title = $jsonarray_lang['m_title'];
        $en_desc =  iconv(mb_detect_encoding($jsonarray_lang['m_descr'], mb_detect_order(), true), 'UTF-8', $jsonarray_lang['m_descr']);
        $en_title = iconv(mb_detect_encoding($en_title = $jsonarray_lang['m_title'], mb_detect_order(), true), 'UTF-8', $en_title = $jsonarray_lang['m_title']);
       
        
    } else {
        $response->status = 500;
        $response->message = 'Missing file: lang/en.json';
        $response->json($response);
    }

    if (file_exists($lang_target . "/ru.json")) {
        //echo "ru: OK";

        $jsonfile_lang = file_get_contents($lang_target . "/ru.json");
        $jsonarray_lang = json_decode($jsonfile_lang, true);
        //$ru_desc = $jsonarray_lang['m_descr'];
       // $ru_title = $jsonarray_lang['m_title'];
         $ru_desc =  iconv(mb_detect_encoding($jsonarray_lang['m_descr'], mb_detect_order(), true), 'UTF-8', $jsonarray_lang['m_descr']);
        $ru_title = iconv(mb_detect_encoding($en_title = $jsonarray_lang['m_title'], mb_detect_order(), true), 'UTF-8', $en_title = $jsonarray_lang['m_title']);
    } else {

        //echo "ru: empty";
    }

    if ($en_title == "") {
        $jsonfile = file_get_contents("$targetdir");
        $jsonarray = json_decode($jsonfile, true);
        $en_title = $jsonarray['defaults']['title'];
    }
    if (!$en_title || empty($en_title)) {
        $response->status = 500;
        $response->message = 'Missing module title';
        $response->json($response);
    }

    include("configuration.php");

    mysql_connect($server, $db_user, $db_pw);
    mysql_select_db($db) or die("Datenbank konnte nicht gefunden werden.");
    //mysql_query("set names 'utf8'");
     mysql_query("SET NAMES 'utf8'");
        mysql_query("set character_set_client='utf8'");
        mysql_query("set collation_connection='utf8_general_ci'");


    $sql = "SELECT * FROM modules WHERE title = '" . $en_title . "' AND user_id = " . $user_id;
    $result = mysql_query($sql);
    $count = 0;
    $current_module_id = 0;
    while ($row = mysql_fetch_object($result)) {
        $count++;
        $current_module_id = $row->id;
    }
    if ($count > 0) {
        //exist -> update

        $de_desc = str_replace("'", "", $de_desc);
        $en_desc = str_replace("'", "", $en_desc);

        if ($filetype == "gz") {
            $modulename_db = $moduleName;
            $module_file = $moduleName . ".tar.gz";
        }
        if ($filetype == "zip") {
            $modulename_db = $moduleName;
            $module_file = $moduleName . ".zip";
        }
        $sql = "UPDATE modules SET
	category = '" . $category . "', 
	author = '" . $author . "', 
	homepage = '" . $homepage . "', 
	icon = '" . $moduleName . '.png' . "', 
	version = '" . $version . "', 
	maturity = '" . $maturity . "',
	title = '" . $en_title . "',
	description = '" . $en_desc . "', 
	last_updated = '" . date("Y-m-d H:i:s") . "',
	user_id = '" . $user_id . "',
	modulename = '" . $modulename_db . "',
        file = '" . $module_file . "',
	detail_images = '0', 
	verified = '0', 
	contributed = '0'
	WHERE id=" . $current_module_id;

        $result = mysql_query($sql);
        //LANGUAGES einbinden!
        $last_id = mysql_insert_id();
        $sql = "UPDATE lang SET  
	desc_de	 = '" . $de_desc . "',
	desc_en		= '" . $en_desc . "', 
	desc_ru		= '" . $ru_desc . "', 
	title_de	= '" . $de_title . "',	 
	title_en	= '" . $en_title . "',	 
	title_ru= '" . $ru_title . "' WHERE id=" . $current_module_id;
        $result = mysql_query($sql);

        //Copy archiv-version
        if ($filetype == "gz") {
            $file = $path . ".tar.gz";
            $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.tar.gz';
            $insert_module = $moduleName . "_" . date("YmdHis") . '.tar.gz';
        }
        if ($filetype == "zip") {
            $file = $path . ".zip";
            $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.zip';
            $insert_module = $moduleName . "_" . date("YmdHis") . '.zip';
        }

        if (!copy($file, $newfile)) {
            echo "copy $file schlug fehl...\n";
            $copy_tar = "false";
        } else {
            $copy_tar = "ok";
        }
        $file = $path . "/htdocs/icon.png";
        $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.png';
        if (!copy($file, $newfile)) {
            echo "copy $file schlug fehl...\n";
        }

        $sql = "INSERT INTO archiv (module_id, modulename, image, version, last_updated, archiv) VALUES
    (" . $last_id . ",'" . $moduleName . "','" . $moduleName . "_" . date("YmdHis") . '.png' . "','" . $version . "','" . date("Y-m-d H:i:s") . "','" . $insert_module . "')";
        $result = mysql_query($sql);
        //Finished
        //Rederict to main.php
        //rrmdir("temp/".$user_id);
        $response->json($response);
        //Header("Location: main.php?error=0");
        //exit;
    } else {
        //new -> insert
        $name_exist = false;
        $sql = "SELECT * FROM modules WHERE title = '" . $en_title . "'";
        $result = mysql_query($sql);
        $count = 0;
        $current_module_id = 0;
        while ($row = mysql_fetch_object($result)) {
            $name_exist = true;
        }
        if ($name_exist == false) {
            $de_desc = str_replace("'", "", $de_desc);
            $en_desc = str_replace("'", "", $en_desc);

            if ($filetype == "gz") {
                $modulename_db = $moduleName;
                $module_file = $moduleName . ".tar.gz";
            }
            if ($filetype == "zip") {
                $modulename_db = $moduleName;
                $module_file = $moduleName . ".zip";
            }
            $sql = "INSERT INTO modules(
	category, 
	author, 
	homepage, 
	icon, 
	version, 
	maturity,
	title,
	description, 
	last_updated,
	user_id,
	modulename,
        file,
	detail_images, 
	verified, 
	contributed)
	VALUES('" . $category . "','" . $author . "','" . $homepage . "','" . $moduleName . '.png' . "','" . $version . "','" . $maturity . "','" . $en_title . "','" . $en_desc . "','" . date("Y-m-d H:i:s") . "','" . $user_id . "','"
                    . $modulename_db . "','" . $module_file . "','0','0','0')";
            //echo $sql;
            $result = mysql_query($sql);
            $response->data = array('id' => mysql_insert_id());
            //LANGUAGES einbinden!
            $last_id = mysql_insert_id();
            $sql = "INSERT INTO lang VALUES(" . $last_id . ",'" . $de_desc . "','" . $en_desc . "','" . $ru_desc . "','" . $de_title . "','" . $en_title . "','" . $ru_title . "')";
            $result = mysql_query($sql);


            //Copy archiv-version
            if ($filetype == "gz") {
                $file = $path . ".tar.gz";
                $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.tar.gz';
                $insert_module = $moduleName . "_" . date("YmdHis") . '.tar.gz';
            }
            if ($filetype == "zip") {
                $file = $path . ".zip";
                $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.zip';
                $insert_module = $moduleName . "_" . date("YmdHis") . '.zip';
            }

            if (!copy($file, $newfile)) {
                echo "copy $file schlug fehl...\n";
                $copy_tar = "false";
            } else {
                $copy_tar = "ok";
            }
            $file = $path . "/htdocs/icon.png";
            $newfile = 'archiv/' . $moduleName . "_" . date("YmdHis") . '.png';
            if (!copy($file, $newfile)) {
                echo "copy $file schlug fehl...\n";
            }

            $sql = "INSERT INTO archiv (module_id, modulename, image, version, last_updated, archiv) VALUES
    (" . $last_id . ",'" . $moduleName . "','" . $moduleName . "_" . date("YmdHis") . '.png' . "','" . $version . "','" . date("Y-m-d H:i:s") . "','" . $insert_module . "')";
            $result = mysql_query($sql);
            //Finished
            //Rederict to main.php

            rrmdir("temp/" . $user_id);
            $response->json($response);
            //Header("Location: main.php?error=0");
            //exit;
        } else {
            //module_name exist;
            rrmdir("temp/" . $user_id);
            $response->status = 500;
            $response->message = 'Module_name exist';
            $response->json($response);
            //Header("Location: main.php?error=11");
            //exit;
        }
    }
}

function edit_entry() {
//echo "edit entry";
}

function upload_image() {
    
}

function upload_tar() {
    
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    rrmdir($dir . "/" . $object);
                else
                    @unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        @rmdir($dir);
    }
}

?>