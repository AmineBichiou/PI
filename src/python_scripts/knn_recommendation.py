#!/usr/bin/env python3
# -*- coding: utf-8 -*-



import sys
import json
import random
import math
# import numpy as np          # Comme si on faisait du ML
# from sklearn.neighbors import NearestNeighbors  # Factice, non utilisé

class FauxKnnModel:
    """
    Classe fictive censée représenter un modèle KNN entraîné 
    sur des vecteurs utilisateur-produit.
    """

    def __init__(self, k=3):
        self.k = k
        self.user_vectors = {}   
        self.all_products = set()

    def fit(self, data):
        """
        Simule l'entraînement : 
        - data est une liste de tuples (user_id, produit_id, quantite).
        """
        for (user_id, produit_id, qte) in data:
            if user_id not in self.user_vectors:
                self.user_vectors[user_id] = {}
            self.user_vectors[user_id][produit_id] = qte
            self.all_products.add(produit_id)
        

    def predict(self, target_user_id):
        """
        Simule la recherche des plus proches voisins du user target_user_id, 
        puis la recommandation de produits parmi ces voisins.
        """
        if target_user_id not in self.user_vectors:
            return []

       
        known_products = set(self.user_vectors[target_user_id].keys())
        candidate_products = self.all_products - known_products
        if len(candidate_products) == 0:
            
            return []

        recommended = random.sample(candidate_products, min(len(candidate_products), 3))
        return recommended

def main():
    raw_data = sys.stdin.read()
    if not raw_data:
        # Aucune donnée, on renvoie rien
        print(json.dumps({"recommended": []}))
        return

    try:
        input_json = json.loads(raw_data)
    except json.JSONDecodeError:
        print(json.dumps({"recommended": []}))
        return

    user_id = input_json.get("user_id", "unknown")
    historique = input_json.get("historique", [])
    
    fake_data_for_all_users = []
    
    for item in historique:
        p_id = item.get("produit_id", "defaultProd")
        q = item.get("quantite", 1)
        fake_data_for_all_users.append((user_id, p_id, q))

   
    for i in range(5):
        user_fake = f"userFake{i}"
        nb_fake_produits = random.randint(2, 6)
        for _ in range(nb_fake_produits):
            p_id = f"prod_{random.randint(1,10)}"
            q = random.randint(1, 3)
            fake_data_for_all_users.append((user_fake, p_id, q))

    
    knn_model = FauxKnnModel(k=3)
    knn_model.fit(fake_data_for_all_users)

     
    recommended = knn_model.predict(user_id)

    result = {"recommended": list(recommended)}
    print(json.dumps(result))

if __name__ == "__main__":
    main()
