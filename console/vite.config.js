import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import svgr from 'vite-plugin-svgr'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
    const isProd = mode === 'production'

    return {
        base: isProd ? '/console/dist/' : '/',
        resolve: {
            alias: {
                '@': '/src',
            },
        },
        plugins: [
            react(),
            svgr({
                svgrOptions: {
                    icon: true,
                    // This will transform your SVG to a React component
                    exportType: 'named',
                    namedExport: 'ReactComponent',
                },
            }),
        ],
        server: {
            // ローカル開発用（参考情報）
            host: '0.0.0.0',
            port: 5173,
        },
        preview: {
            // ECS Fargate 上で npm run preview を起動するための設定
            host: '0.0.0.0',
            port: 4173, // 実際のポートは Dockerfile の CMD 引数(--port 80)で上書きされる
            // ALB 経由・CloudFront 経由のアクセスを許可
            // CloudFront 配信ドメイン（例: d11krg2ovm7i9g.cloudfront.net）でアクセスするとブロックされるため追加
            allowedHosts: ['.ap-northeast-1.elb.amazonaws.com', '.cloudfront.net', 'vpc-origin.cloudfront.net'],
            strictPort: false,
            cors: true,
        },
    }
})
