import pandas as pd
from faker import Faker
import random

fake = Faker('en-US')

# Lire le fichier CSV
df = pd.read_csv('uaScoresDataFrame.csv')
df[df.columns[0]] = df[df.columns[0]].astype(int)  # Convert ID to int

# Créer un DataFrame où les nouvelles données factices seront stockées
fake_df = pd.DataFrame(columns=df.columns)

# Générer une colonne de nouvelles données factices pour chaque colonne dans le DataFrame original
cols = df.columns
for i in range(1000):  # Suppose you want to generate 100 new rows
    id = df[cols[0]].max() + i + 1
    fake_df.loc[i, cols[0]] = int(id)  # Continue ID from max existing, make sure it is integer
    fake_df.loc[i, cols[1]] = fake.city()  # For city
    fake_df.loc[i, cols[2]] = fake.country()  # For country
    fake_df.loc[i, cols[3]] = fake.random_element(elements=('Asia', 'Africa', 'Europe', 'North America', 'South America', 'Oceania'))  # For continent
    for j in range(4, len(cols)):
        # For rest of the columns, sometimes generate integers, sometimes generate floats
        if random.choice([True, False]):
            # Generate a float with a random number of decimal places up to 16
            float_value = random.uniform(0, 10)
            decimal_places = random.randint(0, 16)
            value = round(float_value, decimal_places)
        else:
            # Generate an integer
            value = random.randint(0, 10)
        fake_df.loc[i, cols[j]] = value 

# Fusionner les deux DataFrame
df_final = pd.concat([df, fake_df]).reset_index(drop=True)

# Écrire les données finales à un nouveau fichier CSV
df_final.to_csv('merged_data.csv', index=False)

