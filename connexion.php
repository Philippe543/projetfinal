<?php
require("inc/init.inc.php");

//
if(isset($_GET['action']) && $_GET['action'] =='deconnexion' )
{
	session_destroy();
}	


//vérification si l'utilisateur est connecté sinon on le redirige sur profil
if(utilisateur_est_connecte())
{
//	header("location:profil.php");
}


//Vérification de l'existence des indices du formulaire

if(isset($_POST['pseudo']) && isset($_POST['password']))
{
	$pseudo = $_POST['pseudo'];
	$password = $_POST['password'];
	
	$verif_connexion =$pdo->prepare("SELECT * FROM users WHERE pseudo= :pseudo AND password= :password");
	$verif_connexion->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
	$verif_connexion->bindParam(":password", $password, PDO::PARAM_STR);
	$verif_connexion->execute();
	
	
	
	if($verif_connexion->rowCount() > 0)
	{
		//si nous avons 1 ligne alors le pseudo et le mdp sont corrects
		$info_utilisateur = $verif_connexion->fetch(PDO::FETCH_ASSOC);
		$_SESSION['utilisateur'] = array();
		$_SESSION['utilisateur']['id'] = $info_utilisateur['id'];
		$_SESSION['utilisateur']['lastname'] = $info_utilisateur['lastname'];
		$_SESSION['utilisateur']['firstname'] = $info_utilisateur['firstname'];
		$_SESSION['utilisateur']['gender'] = $info_utilisateur['gender'];
		$_SESSION['utilisateur']['pseudo'] = $info_utilisateur['pseudo'];
		$_SESSION['utilisateur']['email'] = $info_utilisateur['email'];
		
		
		
		//on redirige sur profil
		$message='<div class="alert alert-success" role="success" style="margin-top:20px;">L\'enregistrement s\'est effectué<br /></div>';
	//	header("location:profil.php");
		
		// même chose avec un foreach : c'est une autre solution pour enregistrer un utilisateur
		/*
		$_SESSION['utilisateur']=array();
		foreach($info_utilisateur AS $indice =>$valeur)
		{
			if($indice !=='mdp')
			{
				$_SESSION['utilisateur'][$indice] = $valeur;
			}
		}
		*/
	}
	else
	{
		$message .= '<div class="alert alert-danger" role="alert" style="margin-top:20px;">Attention les informations saisies sont erronées<br />Veuillez recommencer</div>';
	}
	
}	


// la ligne suivant commence les affichages dans la page
require("inc/header.inc.php");
require("inc/nav.inc.php");
echo'<pre>';print_r($_SESSION);echo '</pre>';
?>
  

<div class="container">

    <div class="starter-template">
        <h1><span class="glyphicon glyphicon-user" style="color:plum;"></span>Connexion
        <?php //echo $message; // messages destinés à l'utilisateur ?>
		<?= $message; //cette balise php inclue un echoo/ cette ligne php est équivalente à la ligne au dessus. ?>
    </div>			
	<div class="col-sm-4 col-sm-offset-4">
		<form method="post" action="">
			<div class="form-group">
				<label for="pseudo">pseudo</label>
				<input type="text" name="pseudo" id="pseudo" class="form-control" placeholder="pseudo" value="">
			</div>
			<div class="form-group">
				<label for="password">mot de passe</label>
				<input type="text" name="password" id="password" class="form-control" placeholder="mot de passe "value="">
			</div>					
			<div class="form-group">
				<button class="form-control btn btn-success"><span class="glyphicon glyphicon-star" style="color:red;"></span>
				<span class="glyphicon glyphicon-star" style="color:red;"></span>
				<span class="glyphicon glyphicon-star" style="color:red;"></span>Connexion<span class="glyphicon glyphicon-star" style="color:red;"></span><span class="glyphicon glyphicon-star" style="color:red;"></span><span class="glyphicon glyphicon-star" style="color:red;"></span></button>
					
			</div>
		</form>
	 </div>
</div><!-- /.container -->


<?php
require("inc/footer.inc.php");