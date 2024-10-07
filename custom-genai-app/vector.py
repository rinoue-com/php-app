import torch
from transformers import AutoModel, AutoTokenizer
import faiss
import numpy as np

import os

os.environ['KMP_DUPLICATE_LIB_OK']='True'

# モデルとトークナイザーの読み込み
model_name = "intfloat/multilingual-e5-base"
tokenizer = AutoTokenizer.from_pretrained(model_name)
model = AutoModel.from_pretrained(model_name)

# GPUが利用可能であればGPUを使用
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
model.to(device)
model.eval()

# ベクトル化関数の定義
def embed_texts(texts):
    # トークナイズ
    inputs = tokenizer(texts, padding=True, truncation=True, return_tensors="pt")
    
    # デバイスに送る
    inputs = {k: v.to(device) for k, v in inputs.items()}
    
    # モデルで埋め込みを計算
    with torch.no_grad():
        outputs = model(**inputs)
    
    # 出力のプールされたベクトル（[CLS]トークン）
    embeddings = outputs.last_hidden_state[:, 0, :].cpu().numpy()
    
    return embeddings

# テキストデータの例
documents = [
    "RAGは、情報検索と生成AIを組み合わせたシステムです。",
    "multilingual-e5は多言語のベクトル表現モデルです。",
    "Pythonは強力なプログラミング言語です。",
    "大阪は日本の関西地方に位置する都市です。",
    "日本で一番高い建物はスカイツリーです。"
]

# ベクトル化
document_embeddings = embed_texts(documents)

# FAISSのインデックスを作成
dimension = document_embeddings.shape[1]  # ベクトルの次元数
index = faiss.IndexFlatL2(dimension)  # L2距離を使用したインデックス

# ベクトルをインデックスに追加
index.add(document_embeddings)

# 検索用テキストをベクトル化してFAISSで検索
query = "一番高いモノは何？"
query_embedding = embed_texts([query])

# 検索（kは上位k件を返す）
k = 2
distances, indices = index.search(query_embedding, k)

# 結果の表示
print("検索クエリ:", query)
for idx, (dist, ind) in enumerate(zip(distances[0], indices[0])):
    print(f"上位 {idx+1} 件目: {documents[ind]} (距離: {dist:.4f})")
