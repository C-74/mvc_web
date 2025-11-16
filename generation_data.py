from faker import Faker
import datetime

# Réglage de la langue en français
fake = Faker("fr_FR")
# Stockage des ctagéroies
categories = ["Smartphone", "Ordinateur portable", "Casque Audio", "Montre connectée", "Tablette", "Caméra"]

# Nombre d'insertions
nb_data = 100
# Nom du fichier de sortie
output_file = "inserts_generated.sql"

# Ecriture dans un nouveau fichier
with open(output_file, "w", encoding="utf-8") as file:
    # Génération des fausses données
    for _ in range(nb_data):
        # Génération des attributs du produit
        category = fake.random_element(categories)
        name = f"{category} {fake.word().capitalize()} {fake.random_int(min=1, max=20)}"
        price = fake.pyfloat(min_value=10, max_value=1000, right_digits=2)
        promo = fake.pyfloat(min_value=0, max_value=75, right_digits=2)
        date_added = datetime.date.today()
        stock = fake.random_int(min=0, max=500)

        # Échapper les apostrophes pour éviter les erreurs SQL
        safe_category = category.replace("'", "''")
        safe_name = name.replace("'", "''")
        
        # Ecriture des lignes dans le fichier
        insert_line = f"INSERT INTO t_produit_pro (pro_type, pro_designation, pro_prix_ht, pro_promo, pro_date_arrive, pro_stock) VALUES ('{safe_category}', '{safe_name}', {price}, {promo}, '{date_added}', {stock});\n"
        file.write(insert_line)


print(f"{nb_data} lignes d'insertion SQL générées dans {output_file} :D")