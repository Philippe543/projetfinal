<?php
require("../inc/init.inc.php");


//Restriction d'accès, si l'utilisateur n'est pas admin alors il ne doit pas accéder à cette page

if(!utilisateur_est_connecte())
{
	header("location:../connexion.php");
	exit(); //permet d'arrêter l'exécution du script au cas où une persnne malveillante ferait des injections via GET
}


// Récupération de l'ID de l'utilisateur
///  Il faut récupérer l'id de l'utilisateur et le mettre dans la table.





if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id']) && is_numeric($_GET['id']))
{
	// is_numeric permet de savoir si l'information esr bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POST sont toujours de type  string)
	$id = $_GET['id'];
	// on fait une requête pour récupérer les informations de l'article afin de connaître la photo pour la supprimer.
	$photo_a_supprimer=$pdo->prepare("SELECT * FROM pictures WHERE id =:id");
	$photo_a_supprimer->bindParam(":id", $id, PDO::PARAM_STR);
	$photo_a_supprimer->execute();
	
	$photo_a_suppr =$photo_a_supprimer->fetch(PDO::FETCH_ASSOC);
	//On vérifie si la photo existe
	if(!empty($photo_a_suppr['photo']))
	{
		// on vérifie le chemin si le fichier existe
		$chemin_photo = RACINE_SERVEUR . 'photo/'. $photo_a_suppr['photo'];
		$message .=$chemin_photo;
		if(file_exists($chemin_photo))
		{
			unlink($chemin_photo); // unlink() permet de supprimer un fichier sur le serveur. ici on supprime la photo
		}
	}
	$suppression= $pdo->prepare("DELETE FROM pictures WHERE id =:id");
	$suppression->bindParam(":id", $id, PDO::PARAM_STR);
	$suppression->execute();
	$message .= '<div class="alert alert-success" role="alert" style="margin-top: 20px;">La photo n°' .$id . 'a bien été supprimée</div>';
	
	// on bascule sur l'affichage du tableau
	$_GET['action'] = 'affichage';
	
	
}



$id="";
$title="";
$header="";
$content="";
$date_record="";
$date_picture="";
$country="";
$city="";
$photo_bdd="";

//déclaration d'une variable de contrôle

$erreur="";
$message="";
$status="";
$lastId="";
//*********************************************************
// RECUPERATION DES INFORMATIONS D'UN ARTICLE A MODIFIER
//********************************************************
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id']) && is_numeric($_GET['id']))
{
	$id= $_GET['id'];
	$photo_a_modif =$pdo->prepare("SELECT * FROM pictures WHERE id =:id");
	$photo_a_modif->bindParam(":id", $id, PDO::PARAM_STR);
	$photo_a_modif->execute();
	$photo_actuel = $photo_a_modif->fetch(PDO::FETCH_ASSOC);
	
	$id = $photo_actuel['id'];
	$title = $photo_actuel['title'];
	$header= $photo_actuel['header'];
	$content = $photo_actuel['content'];
	$date_record = $photo_actuel['date_record'];
	$date_picture = $photo_actuel['date_picture'];
	$country = $photo_actuel['country'];
	$city = $photo_actuel['city'];
	// on récupère la photo de l'article dans une nouvelle variable
	$photo_actuelle = $photo_actuel['photo'];
	$keywords=$photo_actuel['keywords'];
}

var_dump($message.'test1', $erreur,$id);




//*************************************************************************************************************
// ENREGISTREMENT DES PRODUITS
//*************************************************************************************************************

// contrôle sur l'existence de tous les champs provenant du formulaire (sauf le bouton de validation)
if(isset($_POST['id']) && 
isset($_POST['title']) && 
isset($_POST['header']) && 
isset($_POST['content']) && 
//isset($_POST['date_record']) && 
isset($_POST['date_picture']) && 
isset($_POST['country']) && 
isset($_POST['city']) &&
isset($_POST['keywords']))
{
	
var_dump($message.'test2', $erreur);

	//le formulaire a été validé, on place dans ces variables, les saisies correspondantes.
	$id = $_POST['id'];
	$title = $_POST['title'];
	$header = $_POST['header'];
	$content = $_POST['content'];
	//$date_record = $_POST['date_record'];
	$date_picture = $_POST['date_picture'];
	$country = $_POST['country'];
	$city = $_POST['city'];
	$keywords=$_POST['keywords'];
	

	var_dump($message.'test3', $erreur,$id);
//*************************************************************************************************************
// FIN ENREGISTREMENT DES PRODUITS
//*************************************************************************************************************	
	

// Contrôle sur la disponibilité du reference en BDD si on est dans le cas d'un ajout car lors de la modification, la référence existera toujours.
			
			/*
			$verif_ref =$pdo->prepare("SELECT * FROM picture WHERE reference =:reference");
			$verif_ref->bindParam(":reference", $reference, PDO::PARAM_STR);
			$verif_ref->execute();
			
			
			
			if($verif_ref->rowCount() > 0  && isset($_GET['action']) && $_GET['action'] == 'ajout') 
			{
				//si l'on obtient au moins 1 ligne de résultat alors le pseudo est déjà pris.
				$message .= '<div class="alert alert-danger" role="alert" style="margin-top:20px;">Attention, la référence est déjà utilisé.<br />Veuillez vérifier votre référence</div>';
				$erreur = true;
			}
			*/
			
			// vérification si le titre n'est pas vide
			if(empty($title))
			{
				$message .= '<div class="alert alert-danger" role="alert" style="margin-top:20px;">Attention, le titre est obligatoire.</div>';
				$erreur = true;
			}

			//Récupération de l'ancienne photo dans le cas d'une modification
			if(isset($_GET['action']) && $_GET['action'] =="modification")
			{
				if(isset($_POST['ancienne_photo']))
				{
					$photo_bdd=$_POST['ancienne_photo'];
				}
			}
			
			var_dump($message.'test3', $erreur);
			
			// vérification si l'utilisateur a chargé une image
			if(!empty($_FILES['photo']['name']))
			{
				//si ce n'est pas vide alors un fichier a bien été chargé via le formulaire.
				//On concatène la référence sur le titre afin de ne jamais avoir un fichier avec un nom existant sur le serveur.
				$photo_bdd = $id . '_'. $_FILES['photo']['name'];
				//vérification de l'extension de l'image (extensions acceptées: jpg,jpeg, png, if)
				$extension = strrchr($_FILES['photo']['name'], '.'); //cette fonction prédéfinie permet de découper une chaîne selon un caractère fourni en 2ième argument (ici le .) Attention, cette fonction découpera la chaîne à partir de la dernière occurence du 2ième argument (donc nous renvoie la chaîne comprise après le dernier point trouvé)
				//exemple: maphoto.jpg => on récupère .jpg
				//exemple: maphoto.phot.png => on récupère .png
				//var_dump($extension);
				
				//On transforme $extension afin que tous les caractères soient en minuscule
				$extension =strtolower($extension); // inverse strtroupper()
				// on enlève le .
				$extension=substr($extension,1); // exemple: .jpg ->jpg
				$tab_extension_valide =array("jpg","jpeg", "png", "gif");
				// nous pouvons donc vérifier si $extension fait partie des valeurs autorisées  dans $tab_extension_valide
				$verif_extension = in_array($extension, $tab_extension_valide);
				// in_array vérifie si une valeur fournie en 1ier argument fait partie des valeurs contenues dans un tableau array fourni en 2ième argument
				
				if($verif_extension && !$erreur)
				{
					//si $verif_extension est égal à true et que $erreur n'est pas égal à true (il n'y a pas eu d'erreurs au préalable)
					$photo_dossier = RACINE_SERVEUR . 'photo/' .$photo_bdd;
					copy($_FILES['photo']['tmp_name'], $photo_dossier);
					// copy() permet de copier un fichier depuis un emplacement vers un autre emplacement fourni en deuxième argument.
				}
				elseif(!$verif_extension )
				{
					
					$message .= '<div class="alert alert-danger" role="alert" style="margin-top:20px;">Attention, la photo n\'a pas une extension valide(extension acceptées : jpg / jpeg / png / gif)</div>';
					$erreur = true;
				}
				
				
			}
			
	//relation avec la table keywords
	$multitags= explode(",",$keywords);
	
	echo '<pre>print :'; print_r($multitags); echo '</pre>';
	echo '<pre>var :'; var_dump($multitags); echo '</pre>';

	//Insertion des produits
	if(!$erreur) //équivaut à if($erreur ==false)
		
		{
			if(isset($_GET['action']) &&  $_GET['action'] == 'ajout' )
			{
				$enregistrement =$pdo->prepare("INSERT INTO pictures (title, header, content, date_record, date_picture, country, city, photo, users_id) VALUES (:title, :header, :content, now(), :date_picture, :country, :city, :photo, :users_id)");		
			}
			elseif(isset($_GET['action']) && $_GET['action'] == 'modification')
			{
				$enregistrement = $pdo->prepare("UPDATE pictures SET title = :title, header= :header, content =:content, date_picture= :date_picture, country =:country, city =:city, photo=:photo WHERE id=:id");
				$id =$_POST['id'];
				$enregistrement->bindParam(":id", $id, PDO::PARAM_STR);
			}
	
	
	
	$enregistrement->bindParam(":title", $title, PDO::PARAM_STR);
	$enregistrement->bindParam(":header", $header, PDO::PARAM_STR);
	$enregistrement->bindParam(":content", $content, PDO::PARAM_STR);
	//$enregistrement->bindParam(":date_record", $date_record, PDO::PARAM_STR);
	$enregistrement->bindParam(":date_picture", $date_picture, PDO::PARAM_STR);
	$enregistrement->bindParam(":country", $country, PDO::PARAM_STR);
	$enregistrement->bindParam(":city", $city, PDO::PARAM_STR);
	$enregistrement->bindParam(":photo", $photo_bdd, PDO::PARAM_STR);
	$enregistrement->bindParam(":users_id", $_SESSION['utilisateur']['id'], PDO::PARAM_INT);
	$enregistrement->execute();

		}

	//$ida=$_SESSION['utilisateur']['id'];
	//var_dump('id en cours', $ida);

	//Enregistrement des mots clefs dans la BDD tags_picture
}	
	$lastId = $pdo->lastInsertId ();
	
	foreach($multitags AS $multitag) {
		$enregistrement2 =$pdo->prepare("INSERT INTO tags_picture (keywords, pictures_id) VALUES( :multitags, :id)");
		$enregistrement2->bindParam(":multitags", $multitag, PDO::PARAM_STR);
		$enregistrement2->bindParam(":id", $lastId, PDO::PARAM_STR);
		$enregistrement2->execute();
	}

//	var_dump($message);




// la ligne suivant commence les affichages dans la page
require("../inc/header.inc.php");
require("../inc/nav.inc.php");
echo'<pre>$_GET : '; print_r($_GET); echo '</pre>';
echo'<pre> $_POST : '; print_r($_POST) ;echo '</pre>';
echo'<pre>'; print_r($_FILES); echo '</pre>';
echo 'test4','$erreur','$erreur'; 
//var_dump($message.'test4', $erreur);
echo'<pre>'; $message; echo '</pre>';
?>
  

    <div class="container">

      <div class="starter-template">
        <h1>Gestion photo</h1>
        <?php //echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; //cette balise php inclue un echo/ cette ligne php est équivalente à la ligne au dessus. ?>
		<hr />
		<a href="?action=ajout" class="btn btn-warning">Ajouter une photo</a>
		<a href="?action=affichage" class="btn btn-info">Afficher les photos</a>
      </div>
		<?php
		//affichage de tous les produits dans un tableau html
		// exercice:couper la description si elle est trop longue
		//exercice: afficher l'image dans une balise <img src="" />
			if(isset($_GET['action']) && $_GET['action'] =='affichage' )
			{
			
			//var_dump('status', $status);
			$ida=$_SESSION['utilisateur']['id'];
			
			$requete = $pdo-> query("select  users.status FROM users, pictures WHERE  users.id = pictures.users_id AND users.id=$ida");
			$res=$requete->fetch(PDO::FETCH_ASSOC);
			var_dump('status', $res['status']);
			var_dump('id',$ida);
			
			if ($res['status'] == 1 ){
				$resultat = $pdo->query("SELECT * FROM pictures");
			}
			else{
				
				$resultat = $pdo->query("SELECT * FROM pictures WHERE users_id=$ida");
			
			}
			
						echo '<hr>';
						// première ligne du tableau pour le nom des colonnes
						echo'<table class="table table-bordered">';
						echo '<tr>';
						// récupération du nombre de colonnes dans la requête:
						$nb_col = $resultat->columnCount();

						for($i = 0; $i< $nb_col; $i++)
						{
						 //echo '<pre>'; print_r($resultat->getColumnMeta($i)); echo '</pre>'; echo '<hr />';
						 $colonne = $resultat->getColumnMeta($i); // on récupère les informations de la, colonne en cours afin ensuite de dem	ander le name.
						echo '<th style="padding:10px;">' . $colonne['name'] . '</th>';
						 
						}
						echo'</tr>';

						While($ligne =$resultat->fetch(PDO::FETCH_ASSOC))
						{
							echo '<tr>';
							foreach($ligne AS $indice => $info)
							{
									if($indice == 'photo')
									{
										echo '<td style="padding:10px;"><img src=" '. URL . 'photo/'.$info .'" width="140" /></td>';
										
									}elseif($indice == "description") {
										echo '<td>' . substr($info,0,5) . '..<a href="#">Voir la fiche de la photo</a></td>';
									}
									else
									{
										echo '<td style="padding:10px;">' .$info .'</td>';
									}
							
								
							}
							echo '<td><a href="?action=modification&id=' . $ligne['id'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></a></td>';
							echo '<td><a onclick="return(confirm(\'Etes vous sûr de vouloir supprimer cette photo\'));"  href="?action=suppression&id=' . $ligne['id'] . '" class="btn btn-warning"><span class="glyphicon glyphicon-trash"></span></a></td>';
							
							//echo '<th>Modif</th>'; // ajout de la colonne modification.
							//echo '<th>Modif</th>'; // ajout de la colonne modification.
							
							echo '</tr>';
						}	

						echo '</table>';
			}
		?>  
	  
	  
	  
		<?php
			if(isset($_GET['action']) && ($_GET['action'] =='ajout' || $_GET['action'] == 'modification'))
			{
		?>
	  
	    <div class="row">
			<div class="col-sm-4 col-sm-offset-4">
				<form method="post" action="" enctype="multipart/form-data">  <!-- on rajoute enctype parce que l'on va demander de joindre une pièce jointe-->
				
					<input type="hidden" name="id" id="id" class="form-control" value="<?php echo $id; ?>">
					
					<div class="form-group">
						<label for="reference">title<span style="color:red;"></span></label>
						<input type="text" name="title" id="title" class="form-control" placeholder="title" value="<?php echo $title; ?>">
					</div>								
					<div class="form-group">
						<label for="header">header<span style="color:red;"></span></label>
						<input type="text" name="header" id="header" class="form-control" placeholder="header "value="<?php echo $header; ?>">
					</div>
					<div class="form-group">
						<label for="content">content</label>
						<input type="text" name="content" id="content" class="form-control" placeholder="content"value="<?php echo $content; ?>">
					</div>
					<div class="form-group">
						<label for="date_picture">date de la photo</label>
						<input type="date" name="date_picture" id="date_picture" class="form-control" placeholder="date de la photo"value="<?php echo $date_picture; ?>">
					</div>
					<div class="form-group">
						<label for="country">pays</label>
						<input type="text" name="country" id="country" class="form-control" placeholder="country"value="<?php echo $country; ?>">
					</div>
					<div class="form-group">
						<label for="city">ville / lieu</label>
						<input type="text" name="city" id="city" class="form-control" placeholder="ville / lieu" value="<?php echo $city; ?>">
					</div>
					
					<?php
					//affichage de la photo actuelle dans le cas d'une modification d'article
						if(isset($photo_actuel)) //si cette variable existe alors nous sommes dans le cas d'une modification
						{
							echo '<div class="form-group">';
							echo '<label>Photo actuelle</label>';
							echo '<img src="'.URL . 'photo/' . $photo_actuelle .'" class="img-thumbnail" width="210" />';
							// On crée un champs caché qui contiendra le nom de la photo afin de le récupérer lors de la validation du formulaire.
							echo '<input type="hidden" name="ancienne_photo" value="' .$photo_actuelle . '"/>';
							echo '</div>';
						}
					
					?>
					
					
					
					<div class="form-group">
						<label for="photo">photo</label>
						<input type="file" name="photo" id="photo" class="form-control" value="">
					</div>	
					
					
					<div class="form-group">
						<label for="keywords">entrer des mots clefs séparés par des virgules , </label>
						<input type="text" name="keywords" id="keywords" class="form-control" value="">
					</div>	
					
					
					
					
					<div class="form-group">
					<button class="form-control btn btn-success"><span class="glyphicon glyphicon-star" style="color:red;"></span>
					<span class="glyphicon glyphicon-star" style="color:red;"></span>
					<span class="glyphicon glyphicon-star" style="color:red;"></span>valider<span class="glyphicon glyphicon-star" style="color:red;"></span><span class="glyphicon glyphicon-star" style="color:red;"></span><span class="glyphicon glyphicon-star" style="color:red;"></span></button>
					
					</div>
				
				
				</form>
			</div>
	  
	  
		</div>
	  <?php
			} // accolade correspondante à la condition sur l'affichage ajout
				// if(isset($_GET['action']) && $_GET['action'] =='ajout' )
		?>

	  
	  
    </div><!-- /.container -->


<?php
require("../inc/footer.inc.php");


?>
