// next.config.mjs
/** @type {import('next').NextConfig} */
const nextConfig = {
    // 環境変数の設定
    env: {
        CMS_API_URL: process.env.CMS_API_URL || 'http://localhost:8000/api/v1',
    },

    // 本番環境での最適化
    ...(process.env.NODE_ENV === 'production' && {
        // 本番環境での設定
        output: 'export',
        images: { unoptimized: true },
    }),
}

export default nextConfig
