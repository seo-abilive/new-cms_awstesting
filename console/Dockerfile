FROM node:20

WORKDIR /app

# buildspecでnpm installとnpm run buildが実行される
# 必要なファイルをコピー
COPY package*.json ./
COPY node_modules/ ./node_modules/
COPY dist/ ./dist/
# vite.config.jsとindex.htmlをコピー（previewモードで設定を適用するため）
COPY vite.config.js ./
COPY index.html ./

# vite previewでビルドされたアプリケーションをサーブ
# --host 0.0.0.0で外部からアクセス可能に、--port 4173でポート指定
EXPOSE 4173

CMD ["npm", "run", "preview", "--", "--host", "0.0.0.0", "--port", "4173"]

