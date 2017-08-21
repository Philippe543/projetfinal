<?php
require("inc/init.inc.php");

$liste_article = $pdo->query("SELECT * FROM pictures");

// requete de récupération de tous les produits
if($_POST) // équivaut à if(!empty($_POST))
{
	$condition = "";
	$arg_country = false;
	$arg_city = false;
	$arg_tag=false;
	
	if(!empty($_POST['country']))
	{
		$condition .= " WHERE country = :country ";
		$arg_country = true;		
		$filtre_country = $_POST['country'];	
		
		/* $liste_article = $pdo->prepare("SELECT * FROM article WHERE country = :country");
		$liste_article->bindParam(":country", $filtre_country, PDO::PARAM_STR);
		$liste_article->execute();		*/ 
	}
	
	if(!empty($_POST['city']))
	{
		if($arg_country)
		{
			$condition .= " AND city = :city ";
		}
		else {
			$condition .= " WHERE city = :city ";
		}
		
		$arg_city = true;		
		$filtre_city = $_POST['city'];	
	}
	
	// Recherche des photos avec tags (en cours de développement (tags)
	
	if (!empty($_POST['keywords']))
	{
		$arg_tag =true;
	}
	
	
	
	$liste_article = $pdo->prepare("SELECT * FROM pictures $condition");
	//$liste_article = $pdo->prepare("SELECT * FROM tags_picture, pictures WHERE  pictures.id = tags_picture.pictures_id $condition");
	
	if($arg_country) // si $arg_country == true alors il faut fournir l'argument country
	{
		$liste_article->bindParam(":country", $filtre_country, PDO::PARAM_STR);
	}
	if($arg_city) // si $arg_city == true alors il faut fournir l'argument city
	{
		$liste_article->bindParam(":city", $filtre_city, PDO::PARAM_STR);
	}
	
	// en cours de développement (tags)
	
	
	$liste_article->execute();		
}
/*
elseif(!empty($_GET['categorie']))
{
	$cat = $_GET['categorie'];
	$liste_article = $pdo->prepare("SELECT * FROM article WHERE categorie = :categorie");
	$liste_article->bindParam(":categorie", $cat, PDO::PARAM_STR);
	$liste_article->execute();
}
*/

// requete de récupération des différentes catégories en BDD
//$liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM article");


// requete de récupération des différentes country en BDD
$liste_country = $pdo->query("SELECT DISTINCT country FROM pictures ORDER BY country");


// requete de récupération des différentes city en BDD
$liste_city = $pdo->query("SELECT DISTINCT city FROM pictures ORDER BY city");


//requete de récupérations des différents tags en BDD
$liste_tags  = $pdo->query("SELECT DISTINCT keywords FROM tags_picture, pictures WHERE  pictures.id = tags_picture.pictures_id");


// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
echo '<pre>'; print_r($_POST); echo '</pre>';

?>

    <div class="container">

      <div class="starter-template">
        <h1><span class="glyphicon" style="color: NavajoWhite;"></span> Gallerie de photos</h1>
        <?php // echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; // cette balise php inclue un echo // cette ligne php est equivalente à la ligne au dessus. ?>
      </div>
	  
	  <div class="row">
		
		<div class="col-sm-2">
			<?php // récupérer toutes les catégories en BDD et les afficher dans une liste ul li sous forme de lien a href avec une information GET par exemple: ?categorie=pantalon 
				/*
				echo '<ul class="list-group">';
				echo '<li class="list-group-item"><a href="index.php">Tous les articles</a></li>';
				while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC))
				{
					echo '<li class="list-group-item"><a href="?categorie=' . $categorie['categorie'] . '">' . $categorie['categorie'] . '</a></li>';
				}
				
				echo '</ul>';
				echo '<hr />';*/
				echo '<form method="post" action="">';
				
				
					// affichage des country
					
					echo '<div class="form-group">
							<label for="country">pays</label>
							<select name="country" id="country" class="form-control">
								<option></option>';
					while($country = $liste_country->fetch(PDO::FETCH_ASSOC))
					{
						echo '<option>' . $country['country'] . '</option>';
					}
					echo '  </select></div>';
					
					
					// affichage des villes
					
					echo '<div class="form-group">
							<label for="city">ville lieu</label>
							<select name="city" id="city" class="form-control">
								<option></option>';
					while($city = $liste_city->fetch(PDO::FETCH_ASSOC))
					{
						echo '<option>' . $city['city'] . '</option>';
					}
					echo '  </select></div>';
					
					// affichage des tags si on veut combiner les recherches
					
					
					
					
					
					
					// affichage des tags
					
					echo '<div class="form-group">
							<label for="tags">tags (mots clefs</label>
							<select name="tags" id="tags" class="form-control">
								<option></option>';
					while($tags = $liste_tags->fetch(PDO::FETCH_ASSOC))
					{
						echo '<option>' . $tags['keywords'] . '</option>';
					}
					echo '  </select></div>';
					
					
					echo '<div class="form-group">
						<button type="submit"  name="filtrer" id="filtrer" class="form-control btn btn-primary">Valider</button>
					</div>';
					
					
				
				echo '</form>';
			?>
		</div>
		
		<div class="col-sm-10">
			<?php // afficher tous les produits dans cette page par exemple: un block avec image + titre + prix produit
			
				echo '<div class="row">';
				$compteur = 0;
				while($article = $liste_article->fetch(PDO::FETCH_ASSOC))
				{
					
					// afin de ne pas avoir de souci avec le float, on ferme et on ouvre une ligne bootstrap (class="row") pour gérer les lignes d'affichage.
					if($compteur%4 == 0 && $compteur != 0) { echo '</div><div class="row">'; }
					$compteur++;
					
					echo '<div class="col-sm-3">';
					echo '<div class="panel panel-default">';
					//echo '<div class="panel-heading"><img src="' . URL . 'img/timestorrylogo.png" class="img-responsive" /></div>';
					echo '<div class="panel-body text-center">';
					echo '<h5>' . $article['title'] . '</h5>';
					echo '<img src="' . URL . 'photo/' . $article['photo'] . '"  class="img-responsive" />';
					
					echo '<hr />';
					//echo '<a href="fiche_article.php?id=' . $article['id'] . '" class="btn btn-primary">Voir la photo</a>';
					
					echo '</div></div></div>';
				}				
				
				echo '</div>';		
			
			?>
		</div>
	  </div>
	  

    </div><!-- /.container -->
	
<?php
require("inc/footer.inc.php");

















