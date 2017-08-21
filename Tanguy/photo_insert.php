<?php
echo'<pre>'; print_r($_FILES); echo '</pre>';

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
?>		
			<form method="post" action="" enctype="multipart/form-data">
			
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