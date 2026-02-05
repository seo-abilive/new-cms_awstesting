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
            // React が二重にバンドルされると #311 (Invalid hook call) が発生するため、単一インスタンスに統一
            dedupe: ['react', 'react-dom', 'react-router', 'react-router-dom'],
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
            // ALB 経由・CloudFront VPC Origin 経由のアクセスを許可
            // （VPC Origin 時は Host が origin の domain_name になる）
            allowedHosts: ['.ap-northeast-1.elb.amazonaws.com', 'vpc-origin.cloudfront.net', '.cloudfront.net'],
            strictPort: false,
            cors: true,
        },
    }
})
