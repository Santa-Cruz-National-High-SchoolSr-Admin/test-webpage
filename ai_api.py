from flask import Flask, request, jsonify
import pickle
import pandas as pd

app = Flask(__name__)
model = pickle.load(open("dropout_model.pkl", "rb"))

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    df = pd.DataFrame([data])
    prediction = model.predict(df)
    return jsonify({"dropout_risk": "High" if prediction[0] == 1 else "Low"})

if __name__ == '__main__':
    app.run(port=5000)
