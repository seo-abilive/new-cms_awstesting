import localConfig from './config.local'
import productionConfig from './config.production'

const env = import.meta.env.VITE_APP_ENV
let config

if (env === 'production') {
    config = productionConfig
} else {
    config = localConfig
}

export default config
