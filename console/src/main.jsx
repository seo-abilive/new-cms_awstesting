import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import 'swiper/swiper-bundle.css'
import 'flatpickr/dist/flatpickr.css'
import App from './App.jsx'
import { ThemeProvider } from './utils/context/ThemeContext.jsx'
import { AppWrapper } from './utils/components/common/PageMeta.jsx'

async function enableMocking() {
    // if (import.meta.env.DEV) {
    //     const { worker } = await import('./mocks/browser')
    //     return worker.start()
    // }
}

enableMocking().then(() => {
    createRoot(document.getElementById('root')).render(
        <StrictMode>
            <ThemeProvider>
                <AppWrapper>
                    <App />
                </AppWrapper>
            </ThemeProvider>
        </StrictMode>
    )
})
