0。
api/
└ storage/
　 └ app/
　 └ framework/
　 └ logs/
が必要

１。
console/.flowbite-react/class-list.json
サーバーにアップ

２。
api/.env
console/.env.production
console/src/config/config.production.js
ビルド時に、環境変数を AWS に合わせて、適用するように terraform で設定

３。
api/ で composer install 実行
console/ で npm run build 実行

4。
github にて、
トークンに以下は許可すること
repo
admin:repo_hook

5。
terraform/terraform.tfvars
api_url = ""
に、"http://" + alb_dns_name + "/api"を設定
api_url = "new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com/api"
terraform apply 実行

6。
ドメイン/console/dist/login
ログインして、
企業、施設をつくって
「コンテンツモデル管理」で、新着情報などをつくって
「企業使用」、「使用施設」を選択して
企業ページ、または、使用施設　ページにいって、
「新着情報」メニュークリックして、
「API プレビュー」をクリックして、
「API URL」
「X-CMS-API-KEY」をコピーして、
demo 側
demo/\_system/app/config/config.php
の
case 'production':
に設定する！
必要なものだけを追加すればいい。

7。
cd /Applications/MAMP/htdocs/new-cms_awstesting/demo/\_system
composer install
composer インストールしたこの demo/ をサーバーにアップロードする。

8。
demo/ だと、中のファイルのコードで、フィールド ID を
console/ で作ったフィールド ID と合わせる作業が必要
