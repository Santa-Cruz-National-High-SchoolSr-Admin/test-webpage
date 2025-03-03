import pandas as pd
from sklearn.ensemble import RandomForestClassifier
import pickle

# Sample dataset (You can use actual student data)
data = {
    "attendance_rate": [90, 75, 50, 30, 95, 60],
    "grades_average": [85, 78, 60, 45, 90, 50],
    "dropout_risk": [0, 0, 1, 1, 0, 1]  # 1 = High risk, 0 = Low risk
}

df = pd.DataFrame(data)

# Train AI Model
X = df[["attendance_rate", "grades_average"]]
y = df["dropout_risk"]
model = RandomForestClassifier()
model.fit(X, y)

# Save the trained model
pickle.dump(model, open("dropout_model.pkl", "wb"))
