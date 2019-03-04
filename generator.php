<?php
require_once 'excelreader/Excel/reader.php';
$input_dir="template";
$output_dir="clients";

	$section="";
	$headline="";
	$direction="";
	$description_1="";
	$description_2="";
	$description_2_tmp=false;


	$header="<!DOCTYPE html><html lang='en'>
				<head>
					<meta charset='UTF-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
					<title>nazev produktu</title>
					<link rel='stylesheet' href='./css/bootstrap.min.css'>
					<link rel='stylesheet' href='./css/styles.css'>
				</head>
				<body>";

	$footer="</body></html>";


// PARSE DATA FILE - EXCEL
function readData($data_xls,$description_1,$description_2,$description_2_tmp,$direction,$headline,$header,$footer,$file_output){

/*
wreiteDataToFile($header,$file_output);	
$data = new Spreadsheet_Excel_Reader();
$file_to_include = $data_xls;
$data->read($file_to_include);
$data->setOutputEncoding('CP1251');
*/


$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('UTF-8');
$file_to_include = $data_xls;
$data->setUTFEncoder('mb');	
$data->read($file_to_include);
wreiteDataToFile($header,$file_output);

$row=0;
$image_path="";

	for($idx = 1; $idx <= $data->sheets[0]['numRows']; $idx++) {

			// direction
		if (isset($data->sheets[0]['cells'][$idx][1])) {
			$direction= $data->sheets[0]['cells'][$idx][1];
		}
	
			// headline
		if (isset($data->sheets[0]['cells'][$idx][2]) && $data->sheets[0]['cells'][$idx][2]=="Headline") {
			$headline= $data->sheets[0]['cells'][$idx][3];
			$description_2_tmp=false;

			$row++;

			if ($row<10) {
				$image_path="0".$row;
			}else{
				$image_path=$row;
			}

			// echo $image_path."<br>";
		}
			// description
		if (isset($data->sheets[0]['cells'][$idx][2]) && $data->sheets[0]['cells'][$idx][2]=="Feature") {
			$description_1= $data->sheets[0]['cells'][$idx][3];


			if (isset($data->sheets[0]['cells'][$idx+1][2]) && $data->sheets[0]['cells'][$idx+1][2]=="Feature") {
				$description_2= $data->sheets[0]['cells'][$idx+1][3];
			}

		if ($description_2_tmp==false) {
			
			$section="<section>";
			?>
			<?php
			if (isset($direction) && ($direction=="L" ||  $direction=="P")) {
				

				if ($direction=="P") {
					$section.='<div class="container">
								<div clas="row">
									<div class="col-md-6"><img class="img-responsive img--center" src="./img/'.$image_path.'L.jpg" alt=""></div>
									<div class="col-md-6">
										<div class="row">
											<p class="title">'.$headline.'</p>
										</div>
										<div class="row">
											<p class="descr">'.$description_1.'</p>
										</div>';
										?>
										
										<?php
									
										if ($description_2!="") {
											 $section.='<div class="row"><p class="descr">'.$description_2.'</p></div>';
										}								
										?>
									<?php
									$section.='</div>								
								</div>								
								</div>
							</section>';
				}
				?>
				<?php

				if($direction=="L"){

				$section.='<div class="container">
								<div class="row">
									<div class="col-md-6">
										<div class="row">
											<p class="title">'.$headline.'</p>
										</div>
										<div class="row">
											<p class="descr">'.$description_1.'</p>
										</div>';
										?>
				
										<?php
									
											if ($description_2!="") {
												 $section.='<div class="row"><p class="descr">'.$description_2.'</p></div>';
											}						
										?>

										<?php
									$section.='</div>
									<div class="col-md-6"><img class="img-responsive img--center" src="./img/'.$image_path.'P.jpg" alt=""></div>
								</div>
							</div>
							</section>';
				}
			}else{				
				$section.='<div class="container">
								<div class="row">
									<p class="title">'.$headline.'</p>
								</div>
								<div class="row">
									<p class="descr">'.$description_1.'</p>
								</div>';
								?>

								<?php
									
									if ($description_2!="") {
											 $section.='<div class="row"><p class="descr">'.$description_2.'</p></div>';
										}								
								?>
								<?php
								$section.='<div class="row">
									<img class="img-responsive img--center" src="./img/'.$image_path.'.jpg" alt="">
								</div>
							</div>
							</section>';					
			 }
			 ?>
			 <?php

			wreiteDataToFile($section,$file_output);			

			if ($description_2!="") {
				$description_2_tmp=true;
			}

			$headline="";
			$description_1="";
			$description_2="";
			$direction="";
		}
	}
}

wreiteDataToFile("</body></html>",$file_output);	

}

	function wreiteDataToFile($datas,$file){
		$current = file_get_contents($file);
			$current.=$datas;
			file_put_contents($file, $current);
	}

// READ FILE DIRECTORY AND CREATE NEW WEB PROJECT DIRECTORY

	function readClientsFolder($inpt_dir,$src,$description_1,$description_2,$description_2_tmp,$direction,$headline,$header,$footer){
		$clients=scandir($inpt_dir);
		$project_path=getcwd();

		for ($i=0;$i<count($clients);$i++){
			if ($i>=2) {

				$data_folder_path=$inpt_dir."/".$clients[$i]."/web";				
				mkdir($data_folder_path);	
				xcopy($src,$data_folder_path);
			}

			if ($i+1==count($clients)) {
				// START LOOPING ON EACH CLIENT FOLDER	
				for ($idx=0;$idx<count($clients);$idx++){
					if ($idx>=2) {

						$current_data_folder_path=$project_path."/".$inpt_dir."/".$clients[$idx];
						chdir($current_data_folder_path);
						$client_path=getcwd();
						$client_web_path=$client_path."/web";
						$client_web_path_img_folder=$client_path."/web/img";

						$content_dir=scandir($client_path);

						for($idz=0;$idz<count($content_dir);$idz++){
								
								$ext = pathinfo($content_dir[$idz], PATHINFO_EXTENSION);
									if ($ext=="jpg" || $ext=="png") {
										rename($client_path."/".$content_dir[$idz], $client_web_path_img_folder."/".$content_dir[$idz]);
									}

									if ($ext=="xls") {
										readData($content_dir[$idz],$description_1,$description_2,$description_2_tmp,$direction,$headline,$header,$footer,$client_web_path."/index.html");
									}

						}

					}
				}

			}
		}
	}

	readClientsFolder($output_dir,$input_dir,$description_1,$description_2,$description_2_tmp,$direction,$headline,$header,$footer);


// COPY DIRECTORY

/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */

function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}



?>