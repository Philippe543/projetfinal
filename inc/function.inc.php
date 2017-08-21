<?php

// fonction pour savoir si un utilisateur est connecté
function utilisateur_est_connecte()
{
	if(isset($_SESSION['utilisateur']))
	{
		// si l'indice utilisateur existe alors l'utilisateur est connecté car il est passé par la page de connexion
		return true; // si on passe sur cette ligne, on sort de la fonction et le return false en dessous ne sera pris en compte.
	}
	return false; // si on ne rentre pas dans le if, on return false.
}

//fonction pour savoir si un utilisateur est connecté mais aussi a le statut administrateuradministrateur
function utilisateur_est_admin()
{
	if (utilisateur_est_connecte() && $_SESSION['utilisateur']['statut']== 1)
	{
		return true;
	}
	return false;
}

//Création du panier
function creation_panier()
{
	if(!isset($_SESSION['panier']))
		{
				$_SESSION['panier'] = array();
				$_SESSION['panier']['id_article']=array();
				$_SESSION['panier']['photo']=array();
				$_SESSION['panier']['prix']=array();
				$_SESSION['panier']['quantite']=array();
				$_SESSION['panier']['titre']=array();	
		}
}

//fonction pour ajouter un article dans le panier
function ajouter_un_article_au_panier($id_article, $prix, $quantite, $titre, $photo)
{
	//avant d'ajouter, on vérifie si l'article n'est packs déjà présent dans le panier, si c'est le cas, on ne fait que modifier sa quantité.
	$position = array_search($id_article, $_SESSION['panier']['id_article']);
	
	//array_search() permet de vérifier si une valeur se trouve  dans un tableau array. si c'est le cas, on récupère l'indice correspondant.
	
	if($position !== FALSE) // importance de mettre un double égal car on peut avoir un indice ayant comme valeur 0. Avec le double égal, on vérifie bien la valeur du booléen.
	{
	$_SESSION['panier']['quantite'][$position] += $quantite;
	}
	else {
		$_SESSION['panier']['quantite'][]= $quantite;
		$_SESSION['panier']['id_article'][]= $id_article;
		$_SESSION['panier']['photo'][]= $photo;
		$_SESSION['panier']['prix'][]= $prix;
		$_SESSION['panier']['titre'][]= $titre;
	}	
}

//retirer un article du panier
function retirer_article_du_panier($id_article)
{
	$position = array_search($id_article, $_SESSION['panier']['id_article']);
	// On vérifie si l'article est bien présent dans le panier et avec array_search on récupère son indice correspondant.
	
	if($position!==FALSE)
	{
		array_splice($_SESSION['panier']['id_article'],$position, 1);
		array_splice($_SESSION['panier']['photo'],$position, 1);
		array_splice($_SESSION['panier']['quantite'],$position, 1);
		array_splice($_SESSION['panier']['prix'],$position, 1);
		array_splice($_SESSION['panier']['titre'],$position, 1);
		
		//array_splice() permet de supprimer un élément dans un tableau et surtout de réordonner les indices afin de ne pas avoir d etrou dans notre tableau.
		//array_splice(le tableau concerné, indiceà supprimer, nb d'élements à supprimer)
	}
}


//calcul du montant total du panier

function montant_total()
{
	if(!empty($_SESSION['panier']['titre']))
	{
		$taille_tab =sizeof($_SESSION['panier']['id_article']);
		$total = 0;
		for($i = 0; $i < $taille_tab;$i++)
		{
			$total +=$_SESSION['panier']['prix'][$i] *$_SESSION['panier']['quantite'][$i];
		}
		return $total;
	}
}




















