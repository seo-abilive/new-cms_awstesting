.PHONY: install serve build deploy help

help:
	@echo "利用可能なコマンド:"
	@echo "  make install  - 必要なパッケージをインストール"
	@echo "  make serve    - 開発サーバーを起動 (http://127.0.0.1:8000)"
	@echo "  make build    - 静的サイトをビルド"
	@echo "  make deploy   - GitHub Pages にデプロイ"

install:
	pip install -r requirements.txt

serve:
	mkdocs serve

build:
	mkdocs build

deploy:
	mkdocs gh-deploy

