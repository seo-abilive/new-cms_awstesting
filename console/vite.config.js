import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import svgr from 'vite-plugin-svgr'
import flowbiteReact from 'flowbite-react/plugin/vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
    return {
        base: mode === 'production' ? '/new-cms/console/dist/' : '/',
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
            flowbiteReact(),
        ],
    }
})
