from faker import Faker
import datetime
import random

# Réglage de la langue en français
fake = Faker("fr_FR")

# Stockage des catégories avec des marques et modèles réalistes
product_data = {
    "Smartphone": {
        "brands": ["Samsung Galaxy", "iPhone", "Google Pixel", "OnePlus", "Xiaomi", "Huawei", "Sony Xperia", "Oppo"],
        "models": ["Pro", "Ultra", "Plus", "Max", "Lite", "SE", "Mini", "Note"],
        "price_range": (299, 1499)
    },
    "Ordinateur portable": {
        "brands": ["HP Pavilion", "Dell Inspiron", "Lenovo ThinkPad", "ASUS ZenBook", "MacBook", "Acer Aspire", "MSI", "Razer Blade"],
        "models": ["15", "14", "13", "Pro", "Air", "Gaming", "Business", "Ultrabook"],
        "price_range": (499, 2499)
    },
    "Casque Audio": {
        "brands": ["Sony WH", "Bose", "JBL", "Sennheiser", "Audio-Technica", "Beats", "AKG", "Jabra"],
        "models": ["1000XM5", "QuietComfort", "Live", "Momentum", "M50x", "Studio", "Pro", "Elite"],
        "price_range": (49, 499)
    },
    "Montre connectée": {
        "brands": ["Apple Watch", "Samsung Galaxy Watch", "Garmin", "Fitbit", "Huawei Watch", "Amazfit", "Fossil Gen", "Polar"],
        "models": ["Series 9", "6", "Venu", "Sense", "GT 4", "GTR", "6", "Grit X"],
        "price_range": (99, 799)
    },
    "Tablette": {
        "brands": ["iPad", "Samsung Galaxy Tab", "Lenovo Tab", "Microsoft Surface", "Huawei MatePad", "Amazon Fire", "Xiaomi Pad", "ASUS"],
        "models": ["Pro", "Air", "S9", "Go", "Pro 11", "HD 10", "6", "ZenPad"],
        "price_range": (199, 1299)
    },
    "Caméra": {
        "brands": ["Canon EOS", "Nikon", "Sony Alpha", "Fujifilm", "Panasonic Lumix", "GoPro Hero", "DJI", "Olympus"],
        "models": ["R5", "Z6", "A7 IV", "X-T5", "GH6", "12", "Action 4", "OM-1"],
        "price_range": (299, 3499)
    }
}

# Nombre d'insertions
nb_data = 50
# Nom du fichier de sortie
output_file = "inserts_generated.sql"

# Ecriture dans un nouveau fichier
with open(output_file, "w", encoding="utf-8") as file:
    file.write("-- Données générées avec Faker\n")
    file.write(f"-- Date de génération: {datetime.date.today()}\n\n")

    # Génération des fausses données
    for _ in range(nb_data):
        # Génération des attributs du produit
        category = fake.random_element(list(product_data.keys()))
        brand = fake.random_element(product_data[category]["brands"])
        model = fake.random_element(product_data[category]["models"])
        version = fake.random_int(min=1, max=15)

        # Créer un nom de produit réaliste
        name = f"{brand} {model} {version}"

        # Prix basé sur la catégorie
        min_price, max_price = product_data[category]["price_range"]
        price = round(random.uniform(min_price, max_price), 2)

        # Promotion (70% sans promo, 30% avec promo)
        if random.random() < 0.3:
            promo = round(random.uniform(5, 50), 2)
        else:
            promo = 0.00

        # Date d'arrivée dans le futur (entre aujourd'hui et 6 mois)
        days_ahead = random.randint(0, 180)
        date_added = datetime.date.today() + datetime.timedelta(days=days_ahead)

        stock = fake.random_int(min=0, max=500)

        # Échapper les apostrophes pour éviter les erreurs SQL
        safe_category = category.replace("'", "''")
        safe_name = name.replace("'", "''")

        # Ecriture des lignes dans le fichier
        insert_line = f"INSERT INTO t_produit_pro (pro_type, pro_designation, pro_prix_ht, pro_promo, pro_date_arrive, pro_stock) VALUES ('{safe_category}', '{safe_name}', {price}, {promo}, '{date_added}', {stock});\n"
        file.write(insert_line)


print(f"{nb_data} lignes d'insertion SQL générées dans {output_file} :D")