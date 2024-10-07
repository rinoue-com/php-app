import sys
import torch
from transformers import AutoModel, AutoTokenizer
import faiss
import numpy as np
import json
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
    inputs = tokenizer(texts, padding=True, truncation=True, return_tensors="pt")
    inputs = {k: v.to(device) for k, v in inputs.items()}
    with torch.no_grad():
        outputs = model(**inputs)
    embeddings = outputs.last_hidden_state[:, 0, :].cpu().numpy()
    return embeddings

# コマンドライン引数から検索クエリを取得
query = sys.argv[1] if len(sys.argv) > 1 else ""
if not query:
    print(json.dumps({"error": "検索クエリがありません"}))
    sys.exit(1)

# 事前に登録されたドキュメント（例）
documents = [
    "RAGは、情報検索と生成AIを組み合わせたシステムです。",
    "multilingual-e5は多言語のベクトル表現モデルです。",
    "Pythonは強力なプログラミング言語です。",
    "大阪は日本の関西地方に位置する都市です。"
]

# ドキュメントをベクトル化し、FAISSインデックスを作成
document_embeddings = embed_texts(documents)
dimension = document_embeddings.shape[1]
index = faiss.IndexFlatL2(dimension)
index.add(document_embeddings)

# クエリをベクトル化してFAISSで検索
query_embedding = embed_texts([query])
k = 2
distances, indices = index.search(query_embedding, k)

# 検索結果を作成
results = {"query": query, "results": []}
for idx, (dist, ind) in enumerate(zip(distances[0], indices[0])):
    result = {
        "rank": idx + 1,
        "document": documents[ind],
        "distance": float(dist)
    }
    results["results"].append(result)

# 検索結果を標準出力（JSON形式）として出力
print(json.dumps(results, ensure_ascii=False))
